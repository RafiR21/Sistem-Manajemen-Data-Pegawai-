<?php
/**
 * ============================================================
 * KELAS PEGAWAI - Model untuk Data Pegawai (OOP CRUD)
 * ============================================================
 * 
 * Kelas ini menangani semua operasi CRUD (Create, Read, Update, Delete)
 * untuk data pegawai. Menggunakan pendekatan OOP yang terstruktur.
 * 
 * @package     SistemPegawai\Models
 * @author      Developer
 * @version     2.0.0 (Fixed for Laragon compatibility)
 */

// Mencegah akses langsung - cek apakah config sudah di-load
if (!defined('DB_HOST')) {
    require_once dirname(__FILE__) . '/../config.php';
}

// Import kelas Database (dependency)
require_once __DIR__ . '/Database.php';

/**
 * Class Pegawai
 * 
 * Model utama untuk mengelola data pegawai dengan operasi CRUD lengkap.
 */
class Pegawai {
    
    /**
     * Instance Database untuk koneksi
     * @var Database
     */
    private $db;
    
    /**
     * Nama tabel database
     * @var string
     */
    private $table = 'pegawai';
    
    /**
     * Error message terakhir (jika ada)
     * @var string|null
     */
    private $error = null;
    
    /**
     * Daftar field yang valid untuk tabel pegawai
     * @var array
     */
    private $allowedFields = [
        'nip', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'alamat', 'no_telepon', 'email',
        'jabatan', 'departemen', 'pendidikan_terakhir',
        'status_kerja', 'tanggal_gabung', 'foto_profil'
    ];
    
    /**
     * Aturan validasi untuk setiap field
     * @var array
     */
    private $validationRules = [
        'nip' => [
            'required' => true,
            'maxLength' => 20,
            'unique' => true
        ],
        'nama_lengkap' => [
            'required' => true,
            'minLength' => 3,
            'maxLength' => 100
        ],
        'tanggal_lahir' => [
            'required' => true,
            'date' => true
        ],
        'jenis_kelamin' => [
            'required' => true,
            'enum' => ['Laki-laki', 'Perempuan']
        ],
        'pendidikan_terakhir' => [
            'required' => true,
            'enum' => ['SMA/SMK', 'D3', 'S1', 'S2', 'S3']
        ],
        'email' => [
            'email' => true,
            'maxLength' => 100
        ],
        'no_telepon' => [
            'maxLength' => 15
        ]
    ];
    
    /**
     * __construct() - Constructor kelas Pegawai
     * 
     * @param Database|null $db Optional database instance
     */
    public function __construct($db = null) {
        try {
            $this->db = ($db !== null) ? $db : Database::getInstance();
        } catch (Exception $e) {
            $this->error = "Gagal inisialisasi database: " . $e->getMessage();
            // Re-throw agar bisa ditangani di atas
            throw new Exception($this->error);
        }
    }
    
    /**
     * create() - Menambahkan data pegawai baru
     * 
     * @param array $data Data pegawai yang akan disimpan
     * @return int|false ID pegawai atau false jika gagal
     */
    public function create($data) {
        try {
            // Validasi input
            $validation = $this->validate($data);
            if ($validation !== true) {
                $this->error = implode(', ', $validation);
                return false;
            }
            
            // Filter data
            $filteredData = $this->filterData($data);
            
            // Siapkan query INSERT
            $fields = array_keys($filteredData);
            $placeholders = array_map(function($field) {
                return ':' . $field;
            }, $fields);
            
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $this->table,
                implode(', ', $fields),
                implode(', ', $placeholders)
            );
            
            // Eksekusi query
            $stmt = $this->db->query($sql, $filteredData);
            
            return $this->db->lastInsertId();
            
        } catch (Exception $e) {
            $this->error = "Gagal menambah data: " . $e->getMessage();
            error_log("Pegawai::create() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * getAll() - Mengambil semua data pegawai
     * 
     * @param string $orderBy   Kolom sorting
     * @param string $orderDir  Arah sorting
     * @param int    $limit     Batas jumlah data
     * @param int    $offset    Offset pagination
     * @return array Array of records
     */
    public function getAll($orderBy = 'id', $orderDir = 'DESC', $limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM {$this->table}";
            
            // ORDER BY
            $allowedOrderFields = ['id', 'nama_lengkap', 'nip', 'tanggal_lahir', 'created_at'];
            $orderBy = in_array($orderBy, $allowedOrderFields) ? $orderBy : 'id';
            $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
            $sql .= " ORDER BY {$orderBy} {$orderDir}";
            
            // LIMIT & OFFSET
            if ($limit !== null) {
                $offset = ($offset !== null) ? (int)$offset : 0;
                $sql .= " LIMIT {$offset}, " . (int)$limit;
            }
            
            return $this->db->fetchAll($sql);
            
        } catch (Exception $e) {
            $this->error = "Gagal mengambil data: " . $e->getMessage();
            error_log("Pegawai::getAll() Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * getById() - Mengambil data pegawai berdasarkan ID
     * 
     * @param int $id ID pegawai
     * @return array|false Data pegawai atau false
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $result = $this->db->fetchOne($sql, ['id' => (int)$id]);
            return $result ?: false;
            
        } catch (Exception $e) {
            $this->error = "Gagal mengambil data: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * getByNip() - Mencari pegawai berdasarkan NIP
     * 
     * @param string $nip NIP yang dicari
     * @return array|false Data pegawai atau false
     */
    public function getByNip($nip) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE nip = :nip LIMIT 1";
            $result = $this->db->fetchOne($sql, ['nip' => $nip]);
            return $result ?: false;
            
        } catch (Exception $e) {
            $this->error = "Gagal mencari data: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * search() - Mencari pegawai berdasarkan keyword
     * 
     * @param string $keyword Kata kunci pencarian
     * @return array Array of matching records
     */
    public function search($keyword) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE 
                    nama_lengkap LIKE :keyword OR 
                    nip LIKE :keyword OR 
                    email LIKE :keyword OR 
                    jabatan LIKE :keyword OR
                    departemen LIKE :keyword
                    ORDER BY nama_lengkap ASC";
            
            $params = ['keyword' => '%' . $keyword . '%'];
            return $this->db->fetchAll($sql, $params);
            
        } catch (Exception $e) {
            $this->error = "Gagal mencari data: " . $e->getMessage();
            return [];
        }
    }
    
    /**
     * count() - Menghitung total jumlah pegawai
     * 
     * @param string|null $status Filter status kerja
     * @return int Total jumlah
     */
    public function count($status = null) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            $params = [];
            
            if ($status !== null && in_array($status, ['Aktif', 'Cuti', 'Resign', 'Pensiun'])) {
                $sql .= " WHERE status_kerja = :status";
                $params['status'] = $status;
            }
            
            return (int)$this->db->fetchColumn($sql, $params);
            
        } catch (Exception $e) {
            $this->error = "Gagal menghitung data: " . $e->getMessage();
            return 0;
        }
    }
    
    /**
     * update() - Mengubah data pegawai
     * 
     * @param int   $id   ID pegawai
     * @param array $data Data baru
     * @return bool True jika berhasil
     */
    public function update($id, $data) {
        try {
            // Cek keberadaan data
            $existing = $this->getById($id);
            if (!$existing) {
                $this->error = "Data pegawai tidak ditemukan.";
                return false;
            }
            
            // Cek unik NIP jika diubah
            if (isset($data['nip']) && $data['nip'] !== $existing['nip']) {
                $nipExists = $this->getByNip($data['nip']);
                if ($nipExists) {
                    $this->error = "NIP '{$data['nip']}' sudah digunakan.";
                    return false;
                }
            }
            
            // Filter data
            $filteredData = $this->filterData($data);
            
            if (empty($filteredData)) {
                $this->error = "Tidak ada data yang valid untuk diupdate.";
                return false;
            }
            
            // Siapkan query UPDATE
            $setClauses = array_map(function($field) {
                return "{$field} = :{$field}";
            }, array_keys($filteredData));
            
            $sql = sprintf(
                "UPDATE %s SET %s WHERE id = :id",
                $this->table,
                implode(', ', $setClauses)
            );
            
            $filteredData['id'] = (int)$id;
            
            $stmt = $this->db->query($sql, $filteredData);
            
            return $stmt->rowCount() > 0 || true;
            
        } catch (Exception $e) {
            $this->error = "Gagal mengupdate data: " . $e->getMessage();
            error_log("Pegawai::update() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * delete() - Menghapus data pegawai
     * 
     * @param int $id ID pegawai
     * @return bool True jika berhasil
     */
    public function delete($id) {
        try {
            $existing = $this->getById($id);
            if (!$existing) {
                $this->error = "Data pegawai tidak ditemukan.";
                return false;
            }
            
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->query($sql, ['id' => (int)$id]);
            
            $deleted = $stmt->rowCount() > 0;
            
            if (!$deleted) {
                $this->error = "Gagal menghapus data.";
            }
            
            return $deleted;
            
        } catch (Exception $e) {
            $this->error = "Gagal menghapus data: " . $e->getMessage();
            error_log("Pegawai::delete() Error: " . $e->getMessage());
            return false;
        }
    }
    
    // ==========================================================
    // METHOD STATISTIK DASHBOARD
    // ==========================================================
    
    /**
     * getStatistikJenisKelamin() - Data grafik jenis kelamin
     * 
     * @return array Statistik JK
     */
    public function getStatistikJenisKelamin() {
        try {
            $sql = "SELECT 
                        jenis_kelamin, 
                        COUNT(*) as jumlah 
                    FROM {$this->table} 
                    GROUP BY jenis_kelamin 
                    ORDER BY 
                        CASE jenis_kelamin 
                            WHEN 'Laki-laki' THEN 1 
                            WHEN 'Perempuan' THEN 2 
                        END";
            
            return $this->db->fetchAll($sql);
            
        } catch (Exception $e) {
            error_log("Error getStatistikJenisKelamin: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * getStatistikPendidikan() - Data grafik pendidikan
     * 
     * @return array Statistik pendidikan
     */
    public function getStatistikPendidikan() {
        try {
            $sql = "SELECT 
                        pendidikan_terakhir, 
                        COUNT(*) as jumlah 
                    FROM {$this->table} 
                    GROUP BY pendidikan_terakhir 
                    ORDER BY 
                        FIELD(pendidikan_terakhir, 'SMA/SMK', 'D3', 'S1', 'S2', 'S3')";
            
            return $this->db->fetchAll($sql);
            
        } catch (Exception $e) {
            error_log("Error getStatistikPendidikan: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * getStatistikUsia() - Data distribusi usia
     * 
     * @return array Statistik usia
     */
    public function getStatistikUsia() {
        try {
            $sql = "SELECT 
                        CASE 
                            WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 25 THEN '< 25'
                            WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
                            WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
                            WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 54 THEN '45-54'
                            ELSE '> 54'
                        END as range_usia,
                        COUNT(*) as jumlah
                    FROM {$this->table}
                    GROUP BY range_usia
                    ORDER BY MIN(TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()))";
            
            return $this->db->fetchAll($sql);
            
        } catch (Exception $e) {
            error_log("Error getStatistikUsia: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * getStatistikDepartemen() - Data departemen
     * 
     * @return array Statistik departemen
     */
    public function getStatistikDepartemen() {
        try {
            $sql = "SELECT 
                        COALESCE(departemen, 'Belum ditentukan') as departemen,
                        COUNT(*) as jumlah
                    FROM {$this->table}
                    GROUP BY departemen
                    ORDER BY jumlah DESC";
            
            return $this->db->fetchAll($sql);
            
        } catch (Exception $e) {
            error_log("Error getStatistikDepartemen: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * getDashboardSummary() - Ringkasan dashboard
     * 
     * @return array Semua statistik dashboard
     */
    public function getDashboardSummary() {
        return [
            'total_pegawai' => $this->count(),
            'statistik_jenis_kelamin' => $this->getStatistikJenisKelamin(),
            'statistik_pendidikan' => $this->getStatistikPendidikan(),
            'statistik_usia' => $this->getStatistikUsia(),
            'statistik_departemen' => $this->getStatistikDepartemen()
        ];
    }
    
    // ==========================================================
    // PRIVATE HELPER METHODS
    // ==========================================================
    
    /**
     * validate() - Validasi input data
     * 
     * @param array $data Input data
     * @return true|array True jika valid, array errors jika tidak
     */
    private function validate($data) {
        $errors = [];
        
        foreach ($this->validationRules as $field => $rules) {
            $value = isset($data[$field]) ? trim($data[$field]) : '';
            
            // Required check
            if (isset($rules['required']) && $rules['required'] && empty($value)) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' wajib diisi.';
                continue;
            }
            
            if (empty($value)) continue;
            
            // Min length
            if (isset($rules['minLength']) && strlen($value) < $rules['minLength']) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' minimal ' . $rules['minLength'] . ' karakter.';
            }
            
            // Max length
            if (isset($rules['maxLength']) && strlen($value) > $rules['maxLength']) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' maksimal ' . $rules['maxLength'] . ' karakter.';
            }
            
            // Email validation
            if (isset($rules['email']) && $rules['email'] && !empty($value)) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Format email tidak valid.';
                }
            }
            
            // Enum validation
            if (isset($rules['enum']) && is_array($rules['enum']) && !empty($value)) {
                if (!in_array($value, $rules['enum'])) {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' harus salah satu: ' . implode(', ', $rules['enum']);
                }
            }
            
            // Date validation
            if (isset($rules['date']) && $rules['date'] && !empty($value)) {
                $date = DateTime::createFromFormat('Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    $errors[] = 'Format tanggal tidak valid (YYYY-MM-DD).';
                }
            }
            
            // Unique check (for NIP)
            if (isset($rules['unique']) && $rules['unique'] && !empty($value)) {
                $exists = $this->getByNip($value);
                if ($exists) {
                    $errors[] = 'NIP "' . $value . '" sudah digunakan.';
                }
            }
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * filterData() - Filter data untuk field yang diizinkan
     * 
     * @param array $data Raw input
     * @return array Filtered data
     */
    private function filterData($data) {
        $filtered = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $this->allowedFields)) {
                if (is_string($value)) {
                    $value = trim($value);
                }
                if ($value !== '' && $value !== null) {
                    $filtered[$key] = $value;
                }
            }
        }
        
        return $filtered;
    }
    
    /**
     * getError() - Mendapatkan pesan error terakhir
     * 
     * @return string|null Error message
     */
    public function getError() {
        return $this->error;
    }
}
?>
