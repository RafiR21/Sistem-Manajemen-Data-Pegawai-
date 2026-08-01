<?php
/**
 * ============================================================
 * MANAJEMEN PEGAWAI - Halaman CRUD Data Pegawai
 * ============================================================
 * 
 * Halaman ini menangani semua operasi CRUD:
 * - READ: Menampilkan tabel data pegawai dengan pagination
 * - CREATE: Form tambah pegawai baru (modal)
 * - UPDATE: Form edit data pegawai (modal)
 * - DELETE: Hapus data dengan konfirmasi SweetAlert2
 * 
 * Fitur:
 * - Validasi input server-side dan client-side
 * - Pencarian real-time
 * - Filter berdasarkan status/departemen
 * - Responsive table design
 * - Flash message notification
 * 
 * @package     SistemPegawai\Pages
 * @author      Developer
 * @version     1.0.0
 */

// ============================================================
// LOAD DEPENDENCIES & CONFIGURATION
// ============================================================

// Definisikan BASE_URL untuk mencegah akses langsung ke includes
define('BASE_URL', 'http://localhost/sistem-pegawai');

// Load konfigurasi database dan helper functions
require_once __DIR__ . '/config.php';

// Load kelas-kelas OOP
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Pegawai.php';

// Set judul halaman untuk header
$pageTitle = 'Manajemen Data Pegawai';

// Load template header
require_once __DIR__ . '/includes/header.php';

// ============================================================
// INISIALISASI OBJEK & VARIABEL
// Menggunakan OOP Pattern
// ============================================================

try {
    // Buat instance objek Pegawai untuk operasi CRUD
    $pegawai = new Pegawai();
    
    // Variabel untuk menyimpan pesan error/sukses
    $errorMessage = '';
    $successMessage = '';
    
    // Variabel untuk data form (digunakan saat edit)
    $editData = null;
    $isEditMode = false;

    // ============================================================
    // HANDLE ACTION: PROSES CRUD BERDASARKAN PARAMETER 'action'
    // URL: pegawai.php?action=create|update|delete&id=...
    // ============================================================
    
    if (isset($_GET['action'])) {
        $action = sanitizeInput($_GET['action']);
        
        switch ($action) {
            
            // --------------------------------------------
            // ACTION: DELETE - Hapus Data Pegawai
            // Memerlukan konfirmasi dari user (SweetAlert2)
            // --------------------------------------------
            case 'delete':
                // Cek apakah ID tersedia
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    $id = (int)$_GET['id'];
                    
                    // Ambil data pegawai sebelum dihapus (untuk log/nama)
                    $dataToDelete = $pegawai->getById($id);
                    
                    if ($dataToDelete) {
                        // Proses penghapusan
                        if ($pegawai->delete($id)) {
                            setFlashMessage('success', 
                                "Data pegawai " . htmlspecialchars($dataToDelete['nama_lengkap']) . " berhasil dihapus.");
                        } else {
                            setFlashMessage('error', 
                                "Gagal menghapus data: " . $pegawai->getError());
                        }
                    } else {
                        setFlashMessage('error', "Data pegawai tidak ditemukan.");
                    }
                    
                    // Redirect ke halaman ini tanpa parameter action
                    header('Location: pegawai.php');
                    exit;
                    
                } else {
                    $errorMessage = "ID pegawai tidak valid.";
                }
                break;
                
            // --------------------------------------------
            // ACTION: EDIT - Siapkan Form Edit
            // Mengambil data existing untuk ditampilkan di form
            // --------------------------------------------
            case 'edit':
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    $id = (int)$_GET['id'];
                    $editData = $pegawai->getById($id);
                    
                    if ($editData) {
                        $isEditMode = true;
                    } else {
                        setFlashMessage('error', "Data pegawai tidak ditemukan.");
                    }
                } else {
                    $errorMessage = "ID pegawai tidak valid.";
                }
                break;
        }
    }

    // ============================================================
    // HANDLE FORM SUBMIT: POST REQUEST (Create/Update)
    // Diproses ketika user submit form tambah/edit
    // ============================================================
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tentukan aksi berdasarkan tombol yang ditekan
        $postAction = isset($_POST['action']) ? sanitizeInput($_POST['action']) : '';
        
        // Kumpulkan data dari form
        $formData = [
            'nip' => isset($_POST['nip']) ? sanitizeInput($_POST['nip']) : '',
            'nama_lengkap' => isset($_POST['nama_lengkap']) ? sanitizeInput($_POST['nama_lengkap']) : '',
            'tempat_lahir' => isset($_POST['tempat_lahir']) ? sanitizeInput($_POST['tempat_lahir']) : '',
            'tanggal_lahir' => isset($_POST['tanggal_lahir']) ? sanitizeInput($_POST['tanggal_lahir']) : '',
            'jenis_kelamin' => isset($_POST['jenis_kelamin']) ? sanitizeInput($_POST['jenis_kelamin']) : '',
            'alamat' => isset($_POST['alamat']) ? sanitizeInput($_POST['alamat']) : '',
            'no_telepon' => isset($_POST['no_telepon']) ? sanitizeInput($_POST['no_telepon']) : '',
            'email' => isset($_POST['email']) ? sanitizeInput($_POST['email']) : '',
            'jabatan' => isset($_POST['jabatan']) ? sanitizeInput($_POST['jabatan']) : '',
            'departemen' => isset($_POST['departemen']) ? sanitizeInput($_POST['departemen']) : '',
            'pendidikan_terakhir' => isset($_POST['pendidikan_terakhir']) ? sanitizeInput($_POST['pendidikan_terakhir']) : '',
            'status_kerja' => isset($_POST['status_kerja']) ? sanitizeInput($_POST['status_kerja']) : 'Aktif',
            'tanggal_gabung' => isset($_POST['tanggal_gabung']) ? sanitizeInput($_POST['tanggal_gabung']) : ''
        ];
        
        if ($postAction === 'update' && isset($_POST['id'])) {
            // ===========================================
            // PROSES UPDATE DATA PEGAWAI
            // ===========================================
            $id = (int)$_POST['id'];
            
            if ($pegawai->update($id, $formData)) {
                setFlashMessage('success', 
                    "Data pegawai " . htmlspecialchars($formData['nama_lengkap']) . " berhasil diperbarui.");
                header('Location: pegawai.php');
                exit;
            } else {
                $errorMessage = $pegawai->getError();
                $editData = array_merge(['id' => $id], $formData);
                $isEditMode = true;
            }
            
        } elseif ($postAction === 'create') {
            // ===========================================
            // PROSES TAMBAH DATA PEGAWAI BARU
            // ===========================================
            $newId = $pegawai->create($formData);
            
            if ($newId) {
                setFlashMessage('success', 
                    "Pegawai baru " . htmlspecialchars($formData['nama_lengkap']) . " berhasil ditambahkan.");
                header('Location: pegawai.php');
                exit;
            } else {
                $errorMessage = $pegawai->getError();
                $editData = $formData; // Isi kembali form dengan data yang sudah diinput
            }
        }
    }

    // ============================================================
    // AMBIL DATA UNTUK TABEL
    // Dengan fitur pencarian dan filter opsional
    // ============================================================
    
    // Parameter pencarian
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
    $filterStatus = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
    
    // Query data pegawai
    if (!empty($search)) {
        // Jika ada keyword pencarian
        $daftarPegawai = $pegawai->search($search);
    } else {
        // Ambil semua data, urutkan terbaru dulu
        $daftarPegawai = $pegawai->getAll('id', 'DESC');
    }
    
    // Hitung total pegawai
    $totalPegawai = count($daftarPegawai);

} catch (Exception $e) {
    // Handle error fatal (misalnya koneksi database gagal)
    echo '<div class="alert alert-danger">';
    echo '<i class="fas fa-exclamation-triangle me-2"></i>';
    echo '<strong>Error Sistem:</strong> Terjadi kesalahan pada sistem.';
    echo '<br><small class="text-muted">Detail: ' . htmlspecialchars($e->getMessage()) . '</small>';
    echo '</div>';
    
    // Set default values agar halaman tetap bisa ditampilkan
    $daftarPegawai = [];
    $totalPegawai = 0;
}
?>

<!-- ============================================================
     PAGE HEADER SECTION
     Judul halaman dan deskripsi
     ============================================================ -->
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-user-tie me-1"></i>Data Pegawai
            </li>
        </ol>
    </nav>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="page-title mb-1">
                <i class="fas fa-users text-primary-custom me-2"></i>
                Manajemen Data Pegawai
            </h1>
            <p class="page-subtitle mb-0">
                Kelola data pegawai: tambah, edit, lihat, dan hapus
            </p>
        </div>
        <!-- Tombol Tambah Pegawai Baru -->
        <button type="button" class="btn btn-primary btn-lg" 
                data-bs-toggle="modal" data-bs-target="#modalPegawai"
                onclick="resetForm()">
            <i class="fas fa-plus-circle"></i>Tambah Pegawai
        </button>
    </div>
</div>

<!-- ============================================================
     ERROR MESSAGE (jika ada error dari proses)
     ============================================================ -->
<?php if (!empty($errorMessage)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    <?php echo htmlspecialchars($errorMessage); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- ============================================================
     SEARCH & FILTER BAR
     Toolbar pencarian dan filter data - Layout Horizontal Full Width
     ============================================================ -->
<div class="card mb-4">
    <div class="card-body">
        <!-- Baris 1: Search + Filter + Actions (Horizontal) -->
        <div class="row g-3 align-items-center">
            
            <!-- Kolom Pencarian (Lebar) -->
            <div class="col-lg-5 col-md-4 col-sm-12">
                <label for="searchInput" class="form-label text-muted small mb-1">
                    <i class="fas fa-search me-1"></i>Cari Pegawai
                </label>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" 
                           class="form-control form-control-sm" 
                           id="searchInput" 
                           name="search" 
                           placeholder="Ketik nama, NIP, email..."
                           value="<?php echo htmlspecialchars($search); ?>"
                           oninput="liveSearch(this.value)"
                           onkeyup="handleSearch(event)">
                </div>
            </div>
            
            <!-- Kolom Filter Status -->
            <div class="col-lg-3 col-md-3 col-sm-6">
                <label for="filterStatus" class="form-label text-muted small mb-1">
                    <i class="fas fa-filter me-1"></i>Filter
                </label>
                <select class="form-select form-select-sm" id="filterStatus" name="status" onchange="applyFilter()">
                    <option value="">Semua Status</option>
                    <option value="Aktif" <?php echo $filterStatus === 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Cuti" <?php echo $filterStatus === 'Cuti' ? 'selected' : ''; ?>>Cuti</option>
                    <option value="Resign" <?php echo $filterStatus === 'Resign' ? 'selected' : ''; ?>>Resign</option>
                    <option value="Pensiun" <?php echo $filterStatus === 'Pensiun' ? 'selected' : ''; ?>>Pensiun</option>
                </select>
            </div>
            
            <!-- Kolom Info & Tombol Aksi -->
            <div class="col-lg-4 col-md-5 col-sm-6">
                <label class="form-label text-muted small mb-1 d-block">&nbsp;</label>
                <div class="d-flex align-items-center gap-2 w-100">
                    
                    <!-- Info Jumlah Data -->
                    <span class="text-muted me-2 flex-shrink-0">
                        <strong class="text-primary-custom" id="totalRecords"><?php echo $totalPegawai; ?></strong>
                        <span class="d-none d-sm-inline">data</span>
                    </span>
                    
                    <!-- Spacer -->
                    <div class="flex-grow-1"></div>
                    
                    <!-- Tombol Reset -->
                    <button type="button" 
                            class="btn btn-outline-secondary btn-sm flex-shrink-0" 
                            onclick="resetFilters()"
                            title="Reset filter & pencarian">
                        <i class="fas fa-undo-alt"></i>
                        <span class="d-none d-md-inline">Reset</span>
                    </button>
                    
                    <!-- Tombol Export -->
                    <button type="button" 
                            class="btn btn-outline-success btn-sm flex-shrink-0" 
                            onclick="exportTableToCSV('tablePegawai', 'data_pegawai')"
                            title="Export data ke CSV">
                        <i class="fas fa-file-csv"></i>
                        <span class="d-none d-md-inline">Export</span>
                    </button>
                    
                </div>
            </div>
            
        </div><!-- /.row -->
    </div>
</div><!-- /.card -->

<!-- ============================================================
     TABLE DATA PEGAWAI
     Tabel responsif dengan semua data pegawai
     ============================================================ -->
<div class="table-container">
    <table class="table table-hover" id="tablePegawai">
        <!-- Header Tabel -->
        <thead>
            <tr>
                <th width="50" class="text-center">#</th>
                <th width="250">Data Pegawai</th>
                <th>NIP</th>
                <th>Jenis Kelamin</th>
                <th>Pendidikan</th>
                <th>Jabatan</th>
                <th>Departemen</th>
                <th>Status</th>
                <th width="150" class="text-center">Aksi</th>
            </tr>
        </thead>
        
        <!-- Body Tabel -->
        <tbody>
            <?php if (empty($daftarPegawai)): ?>
            <!-- Empty State: Tidak ada data -->
            <tr>
                <td colspan="9">
                    <div class="empty-state py-4">
                        <i class="fas fa-inbox"></i>
                        <h5>Tidak Ada Data</h5>
                        <p>Belum ada data pegawai yang tersedia.</p>
                        <p class="mb-0">
                            <small>Klik tombol <strong>"Tambah Pegawai"</strong> untuk memulai.</small>
                        </p>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <!-- Loop: Tampilkan data pegawai -->
            <?php foreach ($daftarPegawai as $index => $row): ?>
            <tr data-id="<?php echo $row['id']; ?>">
                
                <!-- Nomor Urut -->
                <td class="text-center">
                    <span class="badge bg-light text-dark"><?php echo $index + 1; ?></span>
                </td>
                
                <!-- Info Pegawai (Nama + Detail) -->
                <td>
                    <div class="user-info">
                        <!-- Avatar dengan inisial nama -->
                        <div class="user-avatar <?php echo $row['jenis_kelamin'] === 'Laki-laki' ? 'bg-primary-light' : 'bg-danger'; ?>" 
                             style="background-color: <?php echo $row['jenis_kelamin'] === 'Laki-laki' ? 'rgba(67,97,238,0.1)' : 'rgba(239,71,111,0.1)'; ?>; color: <?php echo $row['jenis_kelamin'] === 'Laki-laki' ? '#4361ee' : '#ef476f'; ?>;">
                            <?php echo strtoupper(substr($row['nama_lengkap'], 0, 2)); ?>
                        </div>
                        <div>
                            <div class="user-name"><?php echo htmlspecialchars($row['nama_lengkap']); ?></div>
                            <div class="user-detail">
                                <?php 
                                // Hitung usia dari tanggal lahir
                                $usia = hitungUsia($row['tanggal_lahir']);
                                echo $usia . ' tahun • ' . formatTanggal($row['tanggal_lahir']);
                                ?>
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- NIP -->
                <td>
                    <code><?php echo htmlspecialchars($row['nip']); ?></code>
                </td>
                
                <!-- Jenis Kelamin (Badge) -->
                <td>
                    <span class="badge <?php echo $row['jenis_kelamin'] === 'Laki-laki' ? 'badge-laki' : 'badge-perempuan'; ?>">
                        <i class="fas fa-<?php echo $row['jenis_kelamin'] === 'Laki-laki' ? 'male' : 'female'; ?> me-1"></i>
                        <?php echo htmlspecialchars($row['jenis_kelamin']); ?>
                    </span>
                </td>
                
                <!-- Pendidikan Terakhir -->
                <td><?php echo htmlspecialchars($row['pendidikan_terakhir']); ?></td>
                
                <!-- Jabatan -->
                <td><?php echo htmlspecialchars($row['jabatan'] ?: '-'); ?></td>
                
                <!-- Departemen -->
                <td><?php echo htmlspecialchars($row['departemen'] ?: '-'); ?></td>
                
                <!-- Status Kerja (Badge) -->
                <td>
                    <?php 
                    $statusClass = [
                        'Aktif' => 'bg-success',
                        'Cuti' => 'bg-warning',
                        'Resign' => 'bg-secondary',
                        'Pensiun' => 'bg-info'
                    ];
                    ?>
                    <span class="badge <?php echo $statusClass[$row['status_kerja']] ?? 'bg-secondary'; ?>">
                        <?php echo htmlspecialchars($row['status_kerja']); ?>
                    </span>
                </td>
                
                <!-- Tombol Aksi (Edit & Delete) -->
                <td class="text-center action-btns">
                    <!-- Tombol Edit -->
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary" 
                            title="Edit Data"
                            onclick="editPegawai(
                                <?php echo $row['id']; ?>,
                                '<?php echo addslashes(htmlspecialchars($row['nama_lengkap'])); ?>'
                            )">
                        <i class="fas fa-edit"></i>Edit
                    </button>
                    
                    <!-- Tombol Hapus (dengan SweetAlert2) -->
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger" 
                            title="Hapus Data Pegawai"
                            onclick="confirmDeletePegawai(
                                <?php echo (int)$row['id']; ?>,
                                this
                            )">
                        <i class="fas fa-trash-alt"></i>Hapus
                    </button>
                </td>
                
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div><!-- /.table-container -->

<!-- ============================================================
     MODAL FORM: TAMBAH / EDIT PEGAWAI
     Modal dialog untuk input data pegawai baru atau edit
     ============================================================ -->
<div class="modal fade" id="modalPegawai" tabindex="-1" aria-labelledby="modalPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalPegawaiLabel">
                    <i class="fas fa-user-plus me-2"></i>
                    <span id="modalTitle">Tambah Pegawai Baru</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body: Form Input -->
            <div class="modal-body">
                <form id="formPegawai" method="POST" action="pegawai.php" onsubmit="return validateForm(this)">
                    
                    <!-- Hidden field untuk ID (saat edit) -->
                    <input type="hidden" name="id" id="inputId" value="">
                    <input type="hidden" name="action" id="inputAction" value="create">
                    
                    <!-- Row 1: NIP & Nama Lengkap -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="inputNip" class="form-label">
                                NIP <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="inputNip" 
                                   name="nip" 
                                   placeholder="Contoh: PGW001"
                                   maxlength="20"
                                   required
                                   value="<?php echo $editData['nip'] ?? ''; ?>">
                            <div class="form-text">Nomor Induk Pegawai (unik)</div>
                        </div>
                        
                        <div class="col-md-8">
                            <label for="inputNama" class="form-label">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="inputNama" 
                                   name="nama_lengkap" 
                                   placeholder="Masukkan nama lengkap pegawai"
                                   maxlength="100"
                                   required
                                   value="<?php echo $editData['nama_lengkap'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <!-- Row 2: Tempat Lahir & Tanggal Lahir -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="inputTempatLahir" class="form-label">Tempat Lahir</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="inputTempatLahir" 
                                   name="tempat_lahir" 
                                   placeholder="Kota kelahiran"
                                   value="<?php echo $editData['tempat_lahir'] ?? ''; ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="inputTanggalLahir" class="form-label">
                                Tanggal Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="inputTanggalLahir" 
                                   name="tanggal_lahir" 
                                   required
                                   value="<?php echo $editData['tanggal_lahir'] ?? ''; ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="inputJenisKelamin" class="form-label">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="inputJenisKelamin" name="jenis_kelamin" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" <?php echo (isset($editData['jenis_kelamin']) && $editData['jenis_kelamin'] === 'Laki-laki') ? 'selected' : ''; ?>>
                                    Laki-laki
                                </option>
                                <option value="Perempuan" <?php echo (isset($editData['jenis_kelamin']) && $editData['jenis_kelamin'] === 'Perempuan') ? 'selected' : ''; ?>>
                                    Perempuan
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Row 3: Kontak (Email & Telepon) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="inputEmail" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" 
                                       class="form-control" 
                                       id="inputEmail" 
                                       name="email" 
                                       placeholder="email@contoh.com"
                                       value="<?php echo $editData['email'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="inputTelepon" class="form-label">No. Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="inputTelepon" 
                                       name="no_telepon" 
                                       placeholder="08xxxxxxxxxx"
                                       maxlength="15"
                                       value="<?php echo $editData['no_telepon'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Row 4: Alamat -->
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="inputAlamat" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" 
                                      id="inputAlamat" 
                                      name="alamat" 
                                      rows="2"
                                      placeholder="Masukkan alamat lengkap pegawai"><?php echo $editData['alamat'] ?? ''; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Row 5: Jabatan & Departemen -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="inputJabatan" class="form-label">Jabatan</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="inputJabatan" 
                                   name="jabatan" 
                                   placeholder="Contoh: Senior Developer"
                                   value="<?php echo $editData['jabatan'] ?? ''; ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="inputDepartemen" class="form-label">Departemen</label>
                            <select class="form-select" id="inputDepartemen" name="departemen">
                                <option value="">-- Pilih Departemen --</option>
                                <option value="IT" <?php echo (isset($editData['departemen']) && $editData['departemen'] === 'IT') ? 'selected' : ''; ?>>IT</option>
                                <option value="Human Resources" <?php echo (isset($editData['departemen']) && $editData['departemen'] === 'Human Resources') ? 'selected' : ''; ?>>Human Resources</option>
                                <option value="Finance" <?php echo (isset($editData['departemen']) && $editData['departemen'] === 'Finance') ? 'selected' : ''; ?>>Finance</option>
                                <option value="Marketing" <?php echo (isset($editData['departemen']) && $editData['departemen'] === 'Marketing') ? 'selected' : ''; ?>>Marketing</option>
                                <option value="Operations" <?php echo (isset($editData['departemen']) && $editData['departemen'] === 'Operations') ? 'selected' : ''; ?>>Operations</option>
                                <option value="Sales" <?php echo (isset($editData['departemen']) && $editData['departemen'] === 'Sales') ? 'selected' : ''; ?>>Sales</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Row 6: Pendidikan & Status -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label for="inputPendidikan" class="form-label">
                                Pendidikan Terakhir <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="inputPendidikan" name="pendidikan_terakhir" required>
                                <option value="">-- Pilih --</option>
                                <option value="SMA/SMK" <?php echo (isset($editData['pendidikan_terakhir']) && $editData['pendidikan_terakhir'] === 'SMA/SMK') ? 'selected' : ''; ?>>SMA/SMK</option>
                                <option value="D3" <?php echo (isset($editData['pendidikan_terakhir']) && $editData['pendidikan_terakhir'] === 'D3') ? 'selected' : ''; ?>>D3 (Diploma)</option>
                                <option value="S1" <?php echo (isset($editData['pendidikan_terakhir']) && $editData['pendidikan_terakhir'] === 'S1') ? 'selected' : ''; ?>>S1 (Sarjana)</option>
                                <option value="S2" <?php echo (isset($editData['pendidikan_terakhir']) && $editData['pendidikan_terakhir'] === 'S2') ? 'selected' : ''; ?>>S2 (Magister)</option>
                                <option value="S3" <?php echo (isset($editData['pendidikan_terakhir']) && $editData['pendidikan_terakhir'] === 'S3') ? 'selected' : ''; ?>>S3 (Doktor)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="inputStatus" class="form-label">Status Kerja</label>
                            <select class="form-select" id="inputStatus" name="status_kerja">
                                <option value="Aktif" <?php echo (isset($editData['status_kerja']) && $editData['status_kerja'] === 'Aktif') || !isset($editData['status_kerja']) ? 'selected' : ''; ?>>Aktif</option>
                                <option value="Cuti" <?php echo (isset($editData['status_kerja']) && $editData['status_kerja'] === 'Cuti') ? 'selected' : ''; ?>>Cuti</option>
                                <option value="Resign" <?php echo (isset($editData['status_kerja']) && $editData['status_kerja'] === 'Resign') ? 'selected' : ''; ?>>Resign</option>
                                <option value="Pensiun" <?php echo (isset($editData['status_kerja']) && $editData['status_kerja'] === 'Pensiun') ? 'selected' : ''; ?>>Pensiun</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="inputTanggalGabung" class="form-label">Tgl Gabung</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="inputTanggalGabung" 
                                   name="tanggal_gabung"
                                   value="<?php echo $editData['tanggal_gabung'] ?? ''; ?>">
                        </div>
                    </div>
                    
                </form><!-- /#formPegawai -->
            </div><!-- /.modal-body -->
            
            <!-- Modal Footer: Tombol Aksi -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>Batal
                </button>
                <button type="submit" form="formPegawai" class="btn btn-primary" id="btnSubmit">
                    <i class="fas fa-save"></i><span id="btnSubmitText">Simpan Data</span>
                </button>
            </div>
            
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /#modalPegawai -->

<!-- ============================================================
     JAVASCRIPT: Interaktivitas Halaman Manajemen Pegawai
     Fungsi-fungsi untuk handle form, search, dll.
     ============================================================ -->
<script>
/**
 * resetForm() - Mereset form ke mode tambah data
 * Dipanggil saat tombol "Tambah Pegawai" diklik
 */
function resetForm() {
    // Reset semua field form
    document.getElementById('formPegawai').reset();
    
    // Reset hidden fields
    document.getElementById('inputId').value = '';
    document.getElementById('inputAction').value = 'create';
    
    // Update tampilan modal
    document.getElementById('modalTitle').textContent = 'Tambah Pegawai Baru';
    document.getElementById('btnSubmitText').textContent = 'Simpan Data';
    document.getElementById('modalPegawaiLabel').innerHTML = 
        '<i class="fas fa-user-plus me-2"></i>Tambah Pegawai Baru';
}

/**
 * editPegawai() - Membuka form dalam mode edit
 * Mengambil data dari server dan mengisi form
 * 
 * @param {number} id   ID pegawai yang akan diedit
 * @param {string} nama Nama pegawai (untuk konfirmasi)
 */
function editPegawai(id, nama) {
    // Redirect ke halaman ini dengan parameter edit
    window.location.href = 'pegawai.php?action=edit&id=' + encodeURIComponent(id);
}

/**
 * liveSearch() - Pencarian REAL-TIME (tanpa reload halaman)
 * Memfilter baris tabel langsung saat user mengetik
 * 
 * @param {string} keyword Kata kunci pencarian
 */
function liveSearch(keyword) {
    // Dapatkan tabel dan baris-barisnya
    const table = document.getElementById('tablePegawai');
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;
    const searchTerm = keyword.toLowerCase().trim();
    
    // Loop setiap baris dan filter berdasarkan keyword
    rows.forEach(function(row) {
        // Skip baris empty state
        if (row.querySelector('.empty-state')) {
            row.style.display = 'none';
            return;
        }
        
        // Ambil semua teks dari baris ini
        const textContent = row.textContent.toLowerCase();
        
        // Cek apakah keyword ditemukan di baris ini
        if (searchTerm === '' || textContent.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update counter hasil pencarian
    const counter = document.getElementById('totalRecords');
    if (counter) {
        counter.textContent = visibleCount + ' data';
    }
    
    // Tampilkan pesan jika tidak ada hasil
    showHideNoResult(rows, visibleCount, searchTerm);
}

/**
 * showHideNoResult() - Tampilkan/sembunyikan pesan "tidak ada data"
 */
function showHideNoResult(rows, visibleCount, searchTerm) {
    let noResultRow = document.getElementById('noResultRow');
    
    if (searchTerm !== '' && visibleCount === 0) {
        // Tampilkan pesan tidak ada hasil
        if (!noResultRow) {
            const tbody = document.querySelector('#tablePegawai tbody');
            noResultRow = document.createElement('tr');
            noResultRow.id = 'noResultRow';
            noResultRow.innerHTML = `
                <td colspan="9">
                    <div class="empty-state py-3">
                        <i class="fas fa-search" style="font-size: 2rem; color: #adb5bd;"></i>
                        <h6 class="mt-2">Tidak Ada Hasil</h6>
                        <p class="mb-0 text-muted small">Pegawai dengan kata kunci "<strong>${searchTerm}</strong>" tidak ditemukan.</p>
                    </div>
                </td>
            `;
            tbody.appendChild(noResultRow);
        }
        noResultRow.style.display = '';
    } else {
        // Sembunyikan pesan jika ada hasil atau search kosong
        if (noResultRow) {
            noResultRow.style.display = 'none';
        }
    }
}

/**
 * handleSearch() - Handle event pencarian
 * Mendukung Enter key untuk submit search (server-side)
 * 
 * @param {Event} event Keyboard event
 */
function handleSearch(event) {
    if (event.key === 'Enter') {
        applyFilter();
    }
}

/**
 * applyFilter() - Menerapkan filter dan pencarian
 * Reload halaman dengan parameter search/status
 */
function applyFilter() {
    const searchValue = document.getElementById('searchInput').value.trim();
    const statusValue = document.getElementById('filterStatus').value;
    
    // Bangun URL dengan parameter
    let url = 'pegawai.php?';
    const params = [];
    
    if (searchValue) params.push('search=' + encodeURIComponent(searchValue));
    if (statusValue) params.push('status=' + encodeURIComponent(statusValue));
    
    url += params.join('&');
    
    // Redirect ke URL dengan filter
    window.location.href = url || 'pegawai.php';
}

/**
 * resetFilters() - Reset semua filter ke default
 */
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    window.location.href = 'pegawai.php';
}

// ============================================================
// FUNGSI HAPUS PEGAWAI (FIXED VERSION)
// Konfirmasi hapus dengan SweetAlert2 + Fallback
// ============================================================

/**
 * confirmDeletePegawai() - Konfirmasi hapus data pegawai
 * 
 * Versi fixed yang lebih robust:
 * - Ambil nama dari DOM (tidak perlu parameter string)
 * - Support SweetAlert2 dan fallback ke confirm()
 * - Redirect yang pasti bekerja
 * 
 * @param {number} id ID pegawai yang akan dihapus
 * @param {HTMLElement} buttonElem Element tombol yang diklik
 */
function confirmDeletePegawai(id, buttonElem) {
    // Ambil nama pegawai dari baris tabel (DOM)
    let nama = 'Pegawai ini';
    
    try {
        // Cari elemen .user-name di baris yang sama
        const row = buttonElem.closest('tr');
        if (row) {
            const nameElem = row.querySelector('.user-name');
            if (nameElem) {
                nama = nameElem.textContent.trim();
            }
        }
    } catch(e) {
        console.log('Tidak bisa ambil nama dari DOM, menggunakan default');
    }
    
    // Cek apakah SweetAlert2 tersedia
    if (typeof Swal !== 'undefined' && Swal.fire) {
        // Gunakan SweetAlert2
        Swal.fire({
            title: 'Konfirmasi Hapus Data',
            html: `
                <div style="text-align: left; padding: 10px 0;">
                    <p>Apakah Anda yakin ingin <strong style="color: #ef476f;">menghapus</strong> data pegawai berikut?</p>
                    <div style="background-color: rgba(255,209,102,0.15); border-left: 4px solid #ffd166; padding: 12px; margin-top: 15px; border-radius: 4px;">
                        <i class="fas fa-user" style="color: #b38600;"></i>
                        <strong>${nama}</strong><br>
                        <small style="color: #666;">ID: ${id}</small>
                    </div>
                    <p style="margin-top: 15px; margin-bottom: 0; font-size: 13px; color: #ef476f;">
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
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menghapus...',
                    html: '<i class="fas fa-spinner fa-spin"></i> Sedang memproses penghapusan.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Redirect ke URL delete setelah delay singkat
                setTimeout(() => {
                    window.location.href = 'pegawai.php?action=delete&id=' + encodeURIComponent(id);
                }, 500);
            }
        });
        
    } else {
        // Fallback ke window.confirm() jika SweetAlert2 tidak tersedia
        const konfirmasi = confirm(
            '⚠️ KONFIRMASI HAPUS\n\n' +
            'Apakah Anda yakin ingin menghapus data pegawai:\n\n' +
            'Nama: ' + nama + '\n' +
            'ID: ' + id + '\n\n' +
            '⚠️ Tindakan ini TIDAK DAPAT dibatalkan!'
        );
        
        if (konfirmasi) {
            // Redirect langsung tanpa loading animation
            window.location.href = 'pegawai.php?action=delete&id=' + encodeURIComponent(id);
        }
    }
}

// ============================================================
// AUTO-OPEN MODAL JIKA MODE EDIT
// Jika ada data edit dari server, buka modal otomatis
// ============================================================
<?php if ($isEditMode && $editData): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Buka modal otomatis
    var modal = new bootstrap.Modal(document.getElementById('modalPegawai'));
    modal.show();
    
    // Update judul modal ke mode edit
    document.getElementById('modalTitle').textContent = 'Edit Data Pegawai';
    document.getElementById('btnSubmitText').textContent = 'Update Data';
    document.getElementById('modalPegawaiLabel').innerHTML = 
        '<i class="fas fa-user-edit me-2"></i>Edit Data Pegawai';
    
    // Set hidden field untuk update
    document.getElementById('inputId').value = '<?php echo $editData['id']; ?>';
    document.getElementById('inputAction').value = 'update';
});
<?php endif; ?>

// Log bahwa halaman sudah siap
console.log('%c📋 Halaman Manajemen Pegawai dimuat!', 'color: #4361ee; font-weight: bold;');
</script>

<?php
// ============================================================
// LOAD FOOTER TEMPLATE
// ============================================================
require_once __DIR__ . '/includes/footer.php';
?>
