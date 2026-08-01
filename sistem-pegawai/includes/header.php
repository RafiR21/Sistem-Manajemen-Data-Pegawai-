<?php
/**
 * ============================================================
 * HEADER - Template Header untuk Semua Halaman
 * ============================================================
 * 
 * File ini berisi bagian header yang konsisten di semua halaman:
 * - DOCTYPE dan tag HTML opening
 * - Meta tags (charset, viewport, description)
 * - CSS dependencies (Bootstrap 5, Custom CSS)
 * - Navbar/Navigation
 * 
 * @package     SistemPegawai\Templates
 */

// Mencegah akses langsung
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/sistem-pegawai');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- ============================================================
         META TAGS & BASIC SETUP
         ============================================================ -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo APP_NAME; ?> - Sistem Manajemen Data Pegawai">
    <meta name="author" content="Developer">
    
    <!-- Title halaman dengan fallback -->
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?><?php echo APP_NAME; ?></title>
    
    <!-- Favicon (opsional) -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- ============================================================
         CSS DEPENDENCIES
         Bootstrap 5.3 CDN + Font Awesome Icons + Google Fonts
         ============================================================ -->
     
    <!-- Bootstrap 5.3 CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
          crossorigin="anonymous">
    
    <!-- Font Awesome 6 Icons (untuk ikon di navbar dan tombol) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    
    <!-- Google Fonts: Inter untuk tampilan modern -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS Aplikasi -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Chart.js Library (dimuat di sini agar tersedia di semua halaman) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>

<body class="bg-light">

    <!-- ============================================================
         NAVBAR - Navigasi Utama Aplikasi
         Fixed top navbar dengan branding dan menu navigasi
         ============================================================ -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <!-- Brand / Logo Aplikasi -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-users-cog me-2 fs-4"></i>
                <span class="fw-bold"><?php echo APP_NAME; ?></span>
            </a>
            
            <!-- Tombol Toggle untuk Mobile (Hamburger Menu) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" aria-controls="navbarNav" 
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Menu Navigasi -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Menu Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" 
                           href="index.php">
                            <i class="fas fa-chart-line me-1"></i>Dashboard
                        </a>
                    </li>
                    
                    <!-- Menu Manajemen Pegawai -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'pegawai.php' ? 'active' : ''; ?>" 
                           href="pegawai.php">
                            <i class="fas fa-user-tie me-1"></i>Data Pegawai
                        </a>
                    </li>
                    
                    <!-- Menu Dropdown (opsional untuk fitur tambahan) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog me-1"></i>Pengaturan
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-database me-2"></i>Backup Database</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-export me-2"></i>Export Data</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============================================================
         MAIN CONTENT WRAPPER
         Spacer untuk mengkompensasi fixed navbar
         ============================================================ -->
    <main class="main-content pt-5 mt-5">
        <div class="container py-4">
            
            <!-- Flash Message Notification Area -->
            <?php $flash = getFlashMessage(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show flash-message" role="alert">
                <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' || $flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle'); ?> me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
