<?php
/**
 * ============================================================
 * KELAS DATABASE - Koneksi MySQL dengan PDO (OOP)
 * ============================================================
 * 
 * Kelas ini menangani koneksi ke database MySQL menggunakan PDO
 * (PHP Data Objects) yang lebih aman dari mysqli procedural.
 * Mengimplementasikan pattern Singleton untuk memastikan hanya
 * ada satu koneksi database yang aktif.
 * 
 * @package     SistemPegawai\Database
 * @author      Developer
 * @version     2.0.0 (Fixed for Laragon compatibility)
 */

// Mencegah akses langsung - cek apakah config sudah di-load
if (!defined('DB_HOST')) {
    // Jika konstanta belum didefinisikan, load config terlebih dahulu
    require_once dirname(__FILE__) . '/../config.php';
}

/**
 * Class Database
 * 
 * Kelas utama untuk mengelola koneksi database MySQL menggunakan PDO.
 */
class Database {
    
    /**
     * Instance tunggal dari kelas Database (Singleton)
     * @var Database|null
     */
    private static $instance = null;
    
    /**
     * Object PDO untuk koneksi database
     * @var PDO|null
     */
    private $pdo = null;
    
    /**
     * Status koneksi aktif atau tidak
     * @var bool
     */
    private $isConnected = false;
    
    /**
     * __construct() - Private constructor untuk Singleton Pattern
     * 
     * @access private
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * getInstance() - Mendapatkan instance tunggal Database
     * 
     * @return Database Instance tunggal kelas Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            try {
                self::$instance = new self();
            } catch (Exception $e) {
                die("Gagal membuat koneksi database: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
    
    /**
     * getConnection() - Mendapatkan object PDO
     * 
     * @return PDO Object PDO untuk eksekusi query
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * isConnected() - Cek status koneksi
     * 
     * @return bool Status koneksi
     */
    public function isConnected() {
        return $this->isConnected;
    }
    
    /**
     * connect() - Membuat koneksi PDO ke database
     * 
     * @throws Exception Jika koneksi gagal
     * @return void
     */
    private function connect() {
        try {
            // DSN (Data Source Name) untuk MySQL
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            // Opsi PDO
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            // Buat koneksi PDO
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            $this->isConnected = true;
            
        } catch (PDOException $e) {
            $this->isConnected = false;
            
            // Log error
            error_log("Database Connection Error: " . $e->getMessage());
            
            // Lembar exception yang informatif
            throw new Exception(
                "Koneksi database gagal. Pastikan:\n" .
                "1. Server MySQL sudah berjalan\n" .
                "2. Database '" . DB_NAME . "' sudah dibuat\n" .
                "3. Konfigurasi user/password benar\n\n" .
                "Error detail: " . $e->getMessage()
            );
        }
    }
    
    /**
     * query() - Menjalankan query SQL dengan prepared statement
     * 
     * @param string $sql    Query SQL dengan placeholder
     * @param array  $params Parameter yang akan di-bind
     * @return PDOStatement Statement hasil query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage() . "\nSQL: " . $sql);
            throw new Exception("Query gagal: " . $e->getMessage());
        }
    }
    
    /**
     * fetchAll() - Mengambil semua baris hasil query
     * 
     * @param string $sql    Query SQL
     * @param array  $params Parameter query
     * @return array Array of records
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * fetchOne() - Mengambil satu baris hasil query
     * 
     * @param string $sql    Query SQL
     * @param array  $params Parameter query
     * @return array|false Single record atau false jika tidak ditemukan
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * fetchColumn() - Mengambil satu kolom dari hasil query
     * 
     * @param string $sql    Query SQL
     * @param array  $params Parameter query
     * @return mixed Nilai kolom
     */
    public function fetchColumn($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }
    
    /**
     * lastInsertId() - Mendapatkan ID terakhir yang di-insert
     * 
     * @return string ID auto-increment terakhir
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * __destruct() - Destructor untuk membersihkan resource
     */
    public function __destruct() {
        $this->pdo = null;
        $this->isConnected = false;
    }
    
    /**
     * __clone() - Mencegah cloning object
     */
    private function __clone() {}
    
    /**
     * __wakeup() - Mencegah unserialization
     */
    public function __wakeup() {
        throw new Exception("Tidak bisa unserialize singleton");
    }
}
?>
