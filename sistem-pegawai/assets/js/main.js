/**
 * ============================================================
 * MAIN JAVASCRIPT - Sistem Manajemen Data Pegawai
 * ============================================================
 * 
 * File ini berisi semua fungsi JavaScript yang dibutuhkan:
 * - Konfirmasi hapus data dengan SweetAlert2
 * - Form validation
 * - AJAX helpers (opsional)
 * - UI enhancements
 * - Chart.js initialization helpers
 * 
 * @author      Developer
 * @version     1.0.0
 * @requires    Bootstrap 5.3, SweetAlert2, Chart.js
 */

// ============================================================
// DOCUMENT READY - Inisialisasi saat DOM siap
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Inisialisasi tooltip Bootstrap (jika ada)
    initTooltips();
    
    // Inisialisasi auto-hide flash messages
    initFlashMessages();
    
    // Log ke console (development mode)
    console.log('%c🚀 ' + getAppName() + ' v' + getAppVersion(), 
        'color: #4361ee; font-size: 14px; font-weight: bold;');
});

// ============================================================
// SWEETALERT2 KONFIRMASI HAPUS DATA
// Fitur wajib: Pop-up konfirmasi sebelum menghapus data
// ============================================================

/**
 * confirmDelete() - Menampilkan dialog konfirmasi penghapusan data
 * 
 * Fungsi ini menggunakan SweetAlert2 untuk menampilkan pop-up 
 * konfirmasi yang lebih menarik dan user-friendly dibanding 
 * window.confirm() native.
 * 
 * Fitur:
 * - Animasi smooth
 * - Ikon peringatan yang jelas
 * - Tombol batal dan hapus dengan warna berbeda
 * - Prevent double-click / multiple submission
 * 
 * @param {number} id      ID pegawai yang akan dihapus
 * @param {string} nama    Nama pegawai (untuk ditampilkan di dialog)
 * @param {string} url     URL endpoint untuk proses hapus
 */
function confirmDelete(id, nama, url) {
    // Cek apakah SweetAlert tersedia
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 tidak terload. Pastikan CDN sudah benar.');
        // Fallback ke confirm() native jika SweetAlert gagal
        if (confirm('Apakah Anda yakin ingin menghapus data pegawai "' + nama + '"?')) {
            window.location.href = url + '?action=delete&id=' + id;
        }
        return;
    }
    
    // Tampilkan dialog konfirmasi dengan SweetAlert2
    Swal.fire({
        title: '<strong>Konfirmasi Hapus Data</strong>',
        html: `
            <div style="text-align: left; padding: 10px 0;">
                <p>Apakah Anda yakin ingin <strong>menghapus</strong> data pegawai berikut?</p>
                <div class="alert alert-warning mb-0" style="background-color: rgba(255,209,102,0.15); border-left: 4px solid #ffd166; padding: 12px; margin-top: 15px;">
                    <i class="fas fa-user" style="color: #b38600;"></i>
                    <strong>${escapeHtml(nama)}</strong>
                    <br>
                    <small style="color: #666;">ID: ${id}</small>
                </div>
                <p class="mt-3 mb-0" style="font-size: 13px; color: #ef476f;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <em>Tindakan ini tidak dapat dibatalkan!</em>
                </p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef476f',
        cancelButtonColor: '#868e96',
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus!',
        cancelButtonText: '<i class="fas fa-times"></i> Batal',
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: true,
        focusCancel: true,
        
        // Custom animation
        showClass: {
            popup: 'swal2-show',
            backdrop: 'swal2-backdrop-show',
            icon: 'swal2-icon-show'
        },
        hideClass: {
            popup: 'swal2-hide',
            backdrop: 'swal2-backdrop-hide',
            icon: 'swal2-icon-hide'
        }
    }).then((result) => {
        // Jika user mengklik tombol "Ya, Hapus!"
        if (result.isConfirmed) {
            
            // Tampilkan loading indicator
            Swal.fire({
                title: 'Menghapus Data...',
                html: '<i class="fas fa-spinner fa-spin"></i> Sedang memproses penghapusan data.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirect ke URL hapus setelah delay singkat (untuk efek visual)
            setTimeout(() => {
                window.location.href = url + '?action=delete&id=' + encodeURIComponent(id);
            }, 800);
        }
    });
}

/**
 * confirmAction() - Dialog konfirmasi umum untuk aksi lainnya
 * 
 * Fungsi generik untuk konfirmasi aksi selain delete.
 * 
 * @param {string} title      Judul dialog
 * @param {string} message    Pesan konfirmasi
 * @param {string} url        URL tujuan jika dikonfirmasi
 * @param {string} type       Tipe: 'warning', 'info', 'success'
 */
function confirmAction(title, message, url, type = 'warning') {
    Swal.fire({
        title: title,
        text: message,
        icon: type,
        showCancelButton: true,
        confirmButtonColor: '#4361ee',
        cancelButtonColor: '#868e96',
        confirmButtonText: '<i class="fas fa-check"></i> Lanjutkan',
        cancelButtonText: '<i class="fas fa-times"></i> Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

/**
 * showSuccessMessage() - Notifikasi sukses custom
 * 
 * Menampilkan notifikasi sukses setelah aksi berhasil.
 * 
 * @param {string} message Pesan sukses
 */
function showSuccessMessage(message) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        customClass: {
            popup: 'colored-toast'
        }
    });
}

/**
 * showErrorMessage() - Notifikasi error custom
 * 
 * Menampilkan notifikasi error jika terjadi kesalahan.
 * 
 * @param {string} message Pesan error
 */
function showErrorMessage(message) {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: message,
        confirmButtonColor: '#4361ee'
    });
}

// ============================================================
// FORM VALIDATION HELPERS
// Validasi form di sisi client sebelum submit
// ============================================================

/**
 * validateForm() - Validasi form pegawai
 * 
 * Melakukan validasi dasar pada form input pegawai.
 * Returns false dan menampilkan error jika validasi gagal.
 * 
 * @param {HTMLFormElement} form Element form yang akan divalidasi
 * @returns {boolean} True jika valid, False jika tidak
 */
function validateForm(form) {
    let isValid = true;
    let firstErrorField = null;
    
    // Reset semua error state sebelumnya
    clearFormErrors(form);
    
    // Validasi field wajib
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            isValid = false;
            showFieldError(field, 'Field ini wajib diisi.');
            if (!firstErrorField) firstErrorField = field;
        }
    });
    
    // Validasi format email
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(function(field) {
        if (field.value && !isValidEmail(field.value)) {
            isValid = false;
            showFieldError(field, 'Format email tidak valid.');
            if (!firstErrorField) firstErrorField = field;
        }
    });
    
    // Validasi format tanggal
    const dateFields = form.querySelectorAll('input[type="date"]');
    dateFields.forEach(function(field) {
        if (field.value && !isValidDate(field.value)) {
            isValid = false;
            showFieldError(field, 'Format tanggal harus YYYY-MM-DD.');
            if (!firstErrorField) firstErrorField = field;
        }
    });
    
    // Scroll ke field pertama yang error
    if (!isValid && firstErrorField) {
        firstErrorField.focus();
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    return isValid;
}

/**
 * showFieldError() - Menampilkan error pada field tertentu
 * 
 * @param {HTMLElement} field   Field yang error
 * @param {string} message      Pesan error
 */
function showFieldError(field, message) {
    field.classList.add('is-invalid');
    
    // Buat atau update element pesan error
    let errorMsg = field.parentNode.querySelector('.invalid-feedback');
    if (!errorMsg) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'invalid-feedback';
        field.parentNode.appendChild(errorMsg);
    }
    errorMsg.textContent = message;
}

/**
 * clearFormErrors() - Menghapus semua error pada form
 * 
 * @param {HTMLFormElement} form Element form
 */
function clearFormErrors(form) {
    const invalidFields = form.querySelectorAll('.is-invalid');
    invalidFields.forEach(function(field) {
        field.classList.remove('is-invalid');
    });
    
    const errorMessages = form.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(function(msg) {
        msg.remove();
    });
}

// ============================================================
// UTILITY FUNCTIONS
// Fungsi-fungsi pembantu umum
// ============================================================

/**
 * escapeHtml() - Escape HTML characters untuk mencegah XSS
 * 
 * @param {string} text String yang akan di-escape
 * @returns {string String yang sudah aman
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * isValidEmail() - Validasi format email
 * 
 * @param {string} email Email yang akan dicek
 * @returns {boolean}
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * isValidDate() - Validasi format tanggal YYYY-MM-DD
 * 
 * @param {string} dateStr Tanggal string
 * @returns {boolean}
 */
function isValidDate(dateStr) {
    const regex = /^\d{4}-\d{2}-\d{2}$/;
    if (!regex.test(dateStr)) return false;
    
    const date = new Date(dateStr);
    return !isNaN(date.getTime());
}

/**
 * formatRupiah() - Format angka ke Rupiah
 * 
 * @param {number} angka Angka yang akan diformat
 * @returns {string Format Rupiah
 */
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

/**
 * getAppName() - Mendapatkan nama aplikasi dari meta/variable
 * @returns {string}
 */
function getAppName() {
    return 'Sistem Manajemen Pegawai';
}

/**
 * getAppVersion() - Mendapatkan versi aplikasi
 * @returns {string}
 */
function getAppVersion() {
    return '1.0.0';
}

// ============================================================
// BOOTSTRAP COMPONENT INITIALIZATION
// ============================================================

/**
 * initTooltips() - Inisialisasi semua tooltips Bootstrap
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * initFlashMessages() - Auto-hide flash messages setelah beberapa detik
 */
function initFlashMessages() {
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function(msg) {
        setTimeout(() => {
            msg.classList.add('fade-out');
            setTimeout(() => {
                msg.remove();
            }, 300);
        }, 5000); // Hilang setelah 5 detik
    });
}

// ============================================================
// CHART.JS HELPERS
 * Helper functions untuk membuat grafik dengan mudah
// ============================================================

/**
 * createBarChart() - Membuat bar chart baru
 * 
 * @param {string} canvasId    ID elemen canvas
 * @param {object} config      Konfigurasi chart
 * @returns {Chart} Instance Chart.js
 */
function createBarChart(canvasId, labels, data, label, colors) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas element not found: ' + canvasId);
        return null;
    }
    
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: colors || [
                    'rgba(67, 97, 238, 0.8)',
                    'rgba(239, 71, 111, 0.8)',
                    'rgba(6, 214, 160, 0.8)',
                    'rgba(255, 209, 102, 0.8)',
                    'rgba(17, 138, 178, 0.8)'
                ],
                borderColor: colors ? colors.map(c => c.replace('0.8', '1')) : [
                    'rgba(67, 97, 238, 1)',
                    'rgba(239, 71, 111, 1)',
                    'rgba(6, 214, 160, 1)',
                    'rgba(255, 209, 102, 1)',
                    'rgba(17, 138, 178, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(33, 37, 41, 0.9)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
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
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: { size: 12, weight: '500' },
                        color: '#495057'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });
}

/**
 * createDoughnutChart() - Membuat doughnut/pie chart
 * 
 * @param {string} canvasId    ID elemen canvas
 * @param {Array} labels       Label data
 * @param {Array} data         Nilai data
 * @param {string} label       Label dataset
 * @returns {Chart} Instance Chart.js
 */
function createDoughnutChart(canvasId, labels, data, label) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas element not found: ' + canvasId);
        return null;
    }
    
    const colors = [
        '#4361ee',
        '#ef476f',
        '#06d6a0',
        '#ffd166',
        '#118ab2',
        '#7209b7'
    ];
    
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 12, weight: '500' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(33, 37, 41, 0.9)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    padding: 12,
                    cornerRadius: 8,
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
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });
}

// ============================================================
// SEARCH & FILTER FUNCTIONALITY
// Live search dan filter tabel
// ============================================================

/**
 * setupTableSearch() - Setup live search untuk tabel
 * 
 * @param {string} searchInputId   ID input pencarian
 * @param {string} tableId         ID tabel yang akan difilter
 */
function setupTableSearch(searchInputId, tableId) {
    const searchInput = document.getElementById(searchInputId);
    const table = document.getElementById(tableId);
    
    if (!searchInput || !table) return;
    
    searchInput.addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(function(row) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
        
        // Update counter hasil pencarian
        const visibleRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
        const counter = document.getElementById('searchCounter');
        if (counter) {
            counter.textContent = visibleRows.length + ' data ditemukan';
        }
    });
}

// ============================================================
// EXPORT FUNCTIONS (Opsional)
// Untuk export data ke CSV/PDF
// ============================================================

/**
 * exportTableToCSV() - Export tabel ke file CSV
 * 
 * @param {string} tableId     ID tabel
 * @param {string} filename    Nama file output
 */
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(function(row) {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(function(col) {
            // Hapus tag HTML dan ambil teks saja
            let text = col.textContent.trim().replace(/"/g, '""');
            rowData.push('"' + text + '"');
        });
        csv.push(rowData.join(','));
    });
    
    // Download file
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename + '.csv';
    link.click();
}

console.log('%c✅ Main.js loaded successfully!', 'color: #06d6a0; font-size: 11px;');
