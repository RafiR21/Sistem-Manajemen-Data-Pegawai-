<?php
/**
 * ============================================================
 * KONFIGURASI SISTEM - Sistem Manajemen Data Pegawai
 * ============================================================
 * 
 * File ini berisi semua konfigurasi dasar yang dibutuhkan sistem,
 * termasuk koneksi database, pengaturan session, dan konstanta.
 * 
 * @author      Developer
 * @version     2.1.0 (Fixed headers already sent error)
 * @package     SistemPegawai
 */

// ============================================================
# OUTPUT BUFFERING - Mencegah "Headers Already Sent" Error
# Buffer semua output sampai script selesai
# ============================================================
if (!ob_start()) {
    ob_start();
}

// ============================================================
# ERROR REPORTING (Development Mode)
# Tampilkan semua error untuk debugging
# ============================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// ============================================================
# KONFIGURASI DATABASE MYSQL
# Sesuaikan dengan setting Laragon Anda
# Default Laragon: host=localhost, user=root, password=(kosong)
# ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_sistem_pegawai');
define('DB_CHARSET', 'utf8mb4');

// ============================================================
# KONFIGURASI APLIKASI
# ============================================================
define('APP_NAME', 'Sistem Manajemen Pegawai');
define('APP_VERSION', '1.0.0');

// Base URL - akan di-set otomatis atau manual
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname($_SERVER['SCRIPT_NAME'] ?? '/') ?: '';
    define('BASE_URL', $protocol . '://' . $host . $script);
}

// ============================================================
# START SESSION
# Session diperlukan untuk fitur autentikasi dan notifikasi
# ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
# FUNGSI HELPER: Notifikasi Flash Message
# ============================================================

/**
 * setFlashMessage() - Menyimpan pesan notifikasi ke session
 * 
 * @param string $type    Tipe pesan ('success', 'error', 'warning', 'info')
 * @param string $message Isi pesan notifikasi
 * @return void
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
        'time' => time()
    ];
}

/**
 * getFlashMessage() - Mengambil dan menghapus pesan notifikasi
 * 
 * @return array|null Array berisi type dan message, atau null jika tidak ada
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * sanitizeInput() - Membersihkan input dari karakter berbahaya
 * 
 * @param string $data Input yang akan dibersihkan
 * @return string Data yang sudah bersih dan aman
 */
function sanitizeInput($data) {
    if (is_string($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

/**
 * formatTanggal() - Format tanggal Indonesia
 * 
 * @param string $tanggal Tanggal dalam format Y-m-d
 * @return string Tanggal dalam format Indonesia
 */
function formatTanggal($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') {
        return '-';
    }
    
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $timestamp = strtotime($tanggal);
    if ($timestamp === false) {
        return '-';
    }
    
    $hari = date('d', $timestamp);
    $bulanNama = $bulan[(int)date('m', $timestamp)];
    $tahun = date('Y', $timestamp);
    
    return "$hari $bulanNama $tahun";
}

/**
 * hitungUsia() - Menghitung usia dari tanggal lahir
 * 
 * @param string $tanggalLahir Tanggal lahir dalam format Y-m-d
 * @return int Usia dalam tahun
 */
function hitungUsia($tanggalLahir) {
    if (empty($tanggalLahir) || $tanggalLahir === '0000-00-00') {
        return 0;
    }
    
    try {
        $lahir = new DateTime($tanggalLahir);
        $today = new DateTime();
        $interval = $lahir->diff($today);
        return $interval->y;
    } catch (Exception $e) {
        return 0;
    }
}
?>
