<?php
/**
 * ============================================================
 * DASHBOARD - Halaman Utama Sistem Manajemen Pegawai
 * ============================================================
 * 
 * Halaman ini menampilkan:
 * - Ringkasan statistik pegawai (total, aktif, dll)
 * - Grafik batang perbandingan jumlah Laki-laki vs Perempuan
 * - Grafik komposisi pendidikan terakhir (Doughnut Chart)
 * - Grafik distribusi usia pegawai (Bar Chart)
 * 
 * Teknologi: PHP + MySQL + Chart.js + Bootstrap 5
 * 
 * @package     SistemPegawai\Pages
 * @author      Developer
 * @version     1.0.0
 */

// ============================================================
// LOAD DEPENDENCIES & CONFIGURATION
// Memuat file-file yang diperlukan
// ============================================================

// Definisikan BASE_URL untuk mencegah akses langsung ke includes
define('BASE_URL', 'http://localhost/sistem-pegawai');

// Load konfigurasi database dan helper functions
require_once __DIR__ . '/config.php';

// Load kelas-kelas OOP
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Pegawai.php';

// Set judul halaman untuk header
$pageTitle = 'Dashboard';

// Load template header
require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     PAGE HEADER SECTION
     Judul dan deskripsi halaman dashboard
     ============================================================ -->
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-home me-1"></i>Dashboard
            </li>
        </ol>
    </nav>
    <h1 class="page-title">
        <i class="fas fa-chart-line text-primary-custom me-2"></i>
        Dashboard Analitik Pegawai
    </h1>
    <p class="page-subtitle">
        Ringkasan data dan visualisasi statistik pegawai perusahaan
    </p>
</div>

<?php
// ============================================================
// AMBIL DATA DARI DATABASE
// Menggunakan kelas Pegawai dengan OOP
// ============================================================

try {
    // Buat instance objek Pegawai
    $pegawai = new Pegawai();
    
    // Ambil semua data statistik untuk dashboard
    $dashboardData = $pegawai->getDashboardSummary();
    
    // Ekstrak variabel dari array untuk kemudahan penggunaan
    $totalPegawai = $dashboardData['total_pegawai'];
    $statistikJK = $dashboardData['statistik_jenis_kelamin'];
    $statistikPendidikan = $dashboardData['statistik_pendidikan'];
    $statistikUsia = $dashboardData['statistik_usia'];
    $statistikDepartemen = $dashboardData['statistik_departemen'];
    
} catch (Exception $e) {
    // Handle error jika database tidak bisa diakses
    echo '<div class="alert alert-danger">';
    echo '<i class="fas fa-exclamation-triangle me-2"></i>';
    echo 'Error: Tidak dapat terhubung ke database. ';
    echo 'Pastikan database sudah dibuat dan konfigurasi benar.';
    echo '<br><small>Error detail: ' . htmlspecialchars($e->getMessage()) . '</small>';
    echo '</div>';
    
    // Set default values agar tetap ada tampilan
    $totalPegawai = 0;
    $statistikJK = [];
    $statistikPendidikan = [];
    $statistikUsia = [];
    $statistikDepartemen = [];
}
?>

<!-- ============================================================
     STATISTIK CARDS (KPI Cards)
     Kartu-kartu ringkasan statistik utama
     ============================================================ -->
<div class="row g-4 mb-4">
    
    <!-- Card 1: Total Pegawai -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <p class="stat-label mb-1">Total Pegawai</p>
                    <h2 class="stat-value"><?php echo number_format($totalPegawai); ?></h2>
                    <small class="text-muted">Seluruh data terdaftar</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Pegawai Laki-laki -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <p class="stat-label mb-1">Laki-laki</p>
                    <h2 class="stat-value">
                        <?php 
                        // Hitung jumlah laki-laki dari statistik
                        $jumlahL = 0;
                        foreach ($statistikJK as $jk) {
                            if ($jk['jenis_kelamin'] === 'Laki-laki') {
                                $jumlahL = $jk['jumlah'];
                            }
                        }
                        echo number_format($jumlahL);
                        ?>
                    </h2>
                    <small class="text-muted">
                        <?php echo $totalPegawai > 0 ? round(($jumlahL / $totalPegawai) * 100, 1) : 0; ?>% dari total
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-male"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 3: Pegawai Perempuan -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card danger h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <p class="stat-label mb-1">Perempuan</p>
                    <h2 class="stat-value">
                        <?php 
                        // Hitung jumlah perempuan dari statistik
                        $jumlahP = 0;
                        foreach ($statistikJK as $jk) {
                            if ($jk['jenis_kelamin'] === 'Perempuan') {
                                $jumlahP = $jk['jumlah'];
                            }
                        }
                        echo number_format($jumlahP);
                        ?>
                    </h2>
                    <small class="text-muted">
                        <?php echo $totalPegawai > 0 ? round(($jumlahP / $totalPegawai) * 100, 1) : 0; ?>% dari total
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-female"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 4: Rata-rata Usia (opsional, bisa dihitung) -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <p class="stat-label mb-1">Departemen</p>
                    <h2 class="stat-value"><?php echo count($statistikDepartemen); ?></h2>
                    <small class="text-muted">Unit kerja aktif</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
    </div>
    
</div><!-- /.row KPI Cards -->

<!-- ============================================================
     CHARTS SECTION
     Area grafik-grafik visualisasi data
     ============================================================ -->
<div class="row g-4">

    <!-- ================================================
         GRAFIK 1: Perbandingan Jenis Kelamin (Bar Chart)
         Menampilkan jumlah pegawai Laki-laki vs Perempuan
         ================================================ -->
    <div class="col-lg-6">
        <div class="chart-wrapper h-100">
            <div class="chart-title">
                <i class="fas fa-venus-mars"></i>
                Perbandingan Jumlah Pegawai Berdasarkan Jenis Kelamin
            </div>
            
            <!-- Container untuk Chart.js canvas -->
            <div class="chart-container">
                <canvas id="chartJenisKelamin"></canvas>
            </div>
        </div>
    </div>

    <!-- ================================================
         GRAFIK 2: Komposisi Pendidikan Terakhir (Doughnut)
         Menampilkan distribusi tingkat pendidikan pegawai
         ================================================ -->
    <div class="col-lg-6">
        <div class="chart-wrapper h-100">
            <div class="chart-title">
                <i class="fas fa-graduation-cap"></i>
                Komposisi Pendidikan Terakhir Pegawai
            </div>
            
            <!-- Container untuk Chart.js canvas -->
            <div class="chart-container">
                <canvas id="chartPendidikan"></canvas>
            </div>
        </div>
    </div>

    <!-- ================================================
         GRAFIK 3: Distribusi Usia Pegawai (Bar Chart)
         Mengelompokkan pegawai berdasarkan range usia
         ================================================ -->
    <div class="col-lg-8">
        <div class="chart-wrapper h-100">
            <div class="chart-title">
                <i class="fas fa-birthday-cake"></i>
                Distribusi Usia Pegawai (Kelompok Umur)
            </div>
            
            <!-- Container untuk Chart.js canvas -->
            <div class="chart-container">
                <canvas id="chartUsia"></canvas>
            </div>
        </div>
    </div>

    <!-- ================================================
         GRAFIK 4: Distribusi Departemen (Horizontal Bar)
         Menampilkan jumlah pegawai per departemen
         ================================================ -->
    <div class="col-lg-4">
        <div class="chart-wrapper h-100">
            <div class="chart-title">
                <i class="fas fa-sitemap"></i>
                Jumlah Pegawai Per Departemen
            </div>
            
            <!-- Container untuk Chart.js canvas -->
            <div class="chart-container">
                <canvas id="chartDepartemen"></canvas>
            </div>
        </div>
    </div>

</div><!-- /.row Charts -->

<!-- ============================================================
     QUICK ACTIONS & INFO SECTION
     Tombol cepat dan informasi tambahan
     ============================================================ -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-1"><i class="fas fa-info-circle text-primary-custom me-2"></i>Ingin Mengelola Data?</h5>
                        <p class="text-muted mb-0">Tambah, edit, atau hapus data pegawai melalui halaman manajemen.</p>
                    </div>
                    <a href="pegawai.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-tie me-2"></i>Buka Manajemen Pegawai
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// ============================================================
// JAVASCRIPT: Inisialisasi Chart.js
// Script PHP menghasilkan data JSON untuk dikonsumsi JavaScript
// ============================================================

// Siapkan data untuk Chart Jenis Kelamin
$labelsJK = [];
$dataJK = [];
foreach ($statistikJK as $item) {
    $labelsJK[] = $item['jenis_kelamin'];
    $dataJK[] = (int)$item['jumlah'];
}

// Siapkan data untuk Chart Pendidikan
$labelsPend = [];
$dataPend = [];
foreach ($statistikPendidikan as $item) {
    $labelsPend[] = $item['pendidikan_terakhir'];
    $dataPend[] = (int)$item['jumlah'];
}

// Siapkan data untuk Chart Usia
$labelsUsia = [];
$dataUsia = [];
foreach ($statistikUsia as $item) {
    $labelsUsia[] = $item['range_usia'];
    $dataUsia[] = (int)$item['jumlah'];
}

// Siapkan data untuk Chart Departemen
$labelsDept = [];
$dataDept = [];
foreach ($statistikDepartemen as $item) {
    $labelsDept[] = $item['departemen'];
    $dataDept[] = (int)$item['jumlah'];
}
?>

<!-- ============================================================
     SCRIPT INISIALISASI CHART.JS
     Menggunakan data PHP yang sudah di-encode ke JSON
     ============================================================ -->
<script>
// Tunggu sampai DOM siap sebelum membuat chart
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // DATA DARI PHP (Server-side)
    // Data di-encode menggunakan json_encode()
    // ========================================
    
    // Data untuk Grafik Jenis Kelamin
    const labelsJK = <?php echo json_encode($labelsJK); ?>;
    const dataJK = <?php echo json_encode($dataJK); ?>;
    
    // Data untuk Grafik Pendidikan
    const labelsPend = <?php echo json_encode($labelsPend); ?>;
    const dataPend = <?php echo json_encode($dataPend); ?>;
    
    // Data untuk Grafik Usia
    const labelsUsia = <?php echo json_encode($labelsUsia); ?>;
    const dataUsia = <?php echo json_encode($dataUsia); ?>;
    
    // Data untuk Grafik Departemen
    const labelsDept = <?php echo json_encode($labelsDept); ?>;
    const dataDept = <?php echo json_encode($dataDept); ?>;
    
    // ========================================
    // WARNA UNTUK GRAFIK
    // Palet warna konsisten dan profesional
    // ========================================
    
    const colors = {
        primary: '#4361ee',
        danger: '#ef476f',
        success: '#06d6a0',
        warning: '#ffd166',
        info: '#118ab2',
        purple: '#7209b7'
    };
    
    // ========================================
    // GRAFIK 1: BAR CHART - JENIS KELAMIN
    // Perbandingan jumlah Laki-laki vs Perempuan
    // ========================================
    
    const ctxJK = document.getElementById('chartJenisKelamin');
    if (ctxJK) {
        new Chart(ctxJK.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsJK,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataJK,
                    backgroundColor: [
                        'rgba(17, 138, 178, 0.8)',   // Biru untuk Laki-laki
                        'rgba(239, 71, 111, 0.8)'     // Merah muda untuk Perempuan
                    ],
                    borderColor: [
                        'rgba(17, 138, 178, 1)',
                        'rgba(239, 71, 111, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 10,
                    borderSkipped: false,
                    barThickness: 60
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(33, 37, 41, 0.95)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 14,
                        cornerRadius: 10,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' orang';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 12 },
                            color: '#868e96'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: {
                            font: { size: 13, weight: '600' },
                            color: '#495057'
                        },
                        grid: { display: false }
                    }
                },
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                }
            }
        });
        
        console.log('✅ Chart Jenis Kelamin berhasil dibuat');
    }
    
    // ========================================
    // GRAFIK 2: DOUGHNUT CHART - PENDIDIKAN
    // Komposisi tingkat pendidikan terakhir
    // ========================================
    
    const ctxPend = document.getElementById('chartPendidikan');
    if (ctxPend) {
        new Chart(ctxPend.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labelsPend,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataPend,
                    backgroundColor: [
                        colors.primary,  // SMA/SMK
                        colors.info,     // D3
                        colors.success,  // S1
                        colors.warning,  // S2
                        colors.purple    // S3
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%', // Ukuran lubang tengah doughnut
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(33, 37, 41, 0.95)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 14,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1200,
                    easing: 'easeOutQuart'
                }
            }
        });
        
        console.log('✅ Chart Pendidikan berhasil dibuat');
    }
    
    // ========================================
    // GRAFIK 3: BAR CHART - DISTRIBUSI USIA
    // Pengelompokan berdasarkan range umur
    // ========================================
    
    const ctxUsia = document.getElementById('chartUsia');
    if (ctxUsia) {
        new Chart(ctxUsia.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsUsia,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataUsia,
                    backgroundColor: 'rgba(67, 97, 238, 0.75)',
                    borderColor: 'rgba(67, 97, 238, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bar chart
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(33, 37, 41, 0.95)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 14,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.x + ' orang';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 12 },
                            color: '#868e96'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        ticks: {
                            font: { size: 13, weight: '500' },
                            color: '#495057'
                        },
                        grid: { display: false }
                    }
                },
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                }
            }
        });
        
        console.log('✅ Chart Usia berhasil dibuat');
    }
    
    // ========================================
    // GRAFIK 4: BAR CHART - DEPARTEMEN
    // Jumlah pegawai per unit/departemen
    // ========================================
    
    const ctxDept = document.getElementById('chartDepartemen');
    if (ctxDept) {
        new Chart(ctxDept.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsDept,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataDept,
                    backgroundColor: [
                        'rgba(114, 9, 183, 0.75)',
                        'rgba(6, 214, 160, 0.75)',
                        'rgba(255, 209, 102, 0.75)',
                        'rgba(239, 71, 111, 0.75)',
                        'rgba(17, 138, 178, 0.75)'
                    ],
                    borderColor: [
                        'rgba(114, 9, 183, 1)',
                        'rgba(6, 214, 160, 1)',
                        'rgba(255, 209, 102, 1)',
                        'rgba(239, 71, 111, 1)',
                        'rgba(17, 138, 178, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(33, 37, 41, 0.95)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 11 },
                            color: '#868e96'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: {
                            font: { size: 11, weight: '500' },
                            color: '#495057',
                            maxRotation: 45
                        },
                        grid: { display: false }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
        
        console.log('✅ Chart Departemen berhasil dibuat');
    }
    
    // Log sukses inisialisasi semua chart
    console.log('%c📊 Semua grafik Dashboard berhasil dimuat!', 
        'color: #06d6a0; font-weight: bold;');
});
</script>

<?php
// ============================================================
// LOAD FOOTER TEMPLATE
// ============================================================
require_once __DIR__ . '/includes/footer.php';
?>
