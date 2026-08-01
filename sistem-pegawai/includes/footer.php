<?php
/**
 * ============================================================
 * FOOTER - Template Footer untuk Semua Halaman
 * ============================================================
 * 
 * File ini berisi bagian footer yang konsisten di semua halaman:
 * - Penutup container dan main content
 * - Footer dengan copyright info
- JavaScript dependencies (Bootstrap JS, SweetAlert2, Custom JS)
 * - Penutup tag HTML
 * 
 * @package     SistemPegawai\Templates
 */

// Mencegah akses langsung
if (!defined('BASE_URL')) {
    header('Location: index.php');
    exit;
}
?>
        </div><!-- /.container -->
    </main><!-- /.main-content -->

    <!-- ============================================================
         FOOTER - Bagian Bawah Halaman
         ============================================================ -->
    <footer class="footer bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row">
                <!-- Copyright & Info -->
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">
                        <i class="fas fa-code me-1"></i>
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> 
                        <span class="text-muted">| Version <?php echo APP_VERSION; ?></span>
                    </p>
                </div>
                
                <!-- Developer Credit -->
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-muted">
                        Dibuat untuk <strong>Uji Kompetensi Junior Web Programmer</strong>
                        <br class="d-md-none">
                        Menggunakan PHP OOP + MySQL + Bootstrap 5 + Chart.js
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- ============================================================
         JAVASCRIPT DEPENDENCIES
         Bootstrap 5.3 JS + SweetAlert2 + Custom Scripts
         ============================================================ -->
    
    <!-- Bootstrap 5.3 Bundle (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" 
            crossorigin="anonymous"></script>
    
    <!-- SweetAlert2 Library (untuk konfirmasi pop-up yang cantik) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript Aplikasi -->
    <script src="assets/js/main.js"></script>

</body>
</html>
