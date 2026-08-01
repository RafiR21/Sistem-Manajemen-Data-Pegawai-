# 🏢 Sistem Manajemen Data Pegawai

## 📋 Deskripsi Proyek

**Sistem Manajemen Data Pegawai** adalah aplikasi web berbasis **PHP Native (OOP)** + **MySQL** yang dibuat untuk memenuhi tugas **Uji Kompetensi Junior Web Programmer**. Sistem ini menyediakan fitur CRUD (Create, Read, Update, Delete) lengkap untuk mengelola data pegawai beserta dashboard analitik dengan visualisasi grafik.

---

## ✨ Fitur Utama

### 🔐 Kebutuhan Fungsional (CRUD)
1. **Create** - Admin dapat memasukkan data pegawai baru melalui form modal
2. **Read** - Admin dapat melihat daftar data pegawai dalam tabel responsif
3. **Update** - Admin dapat mengubah/edit data pegawai yang sudah ada
4. **Delete** - Admin dapat menghapus data pegawai dengan konfirmasi pop-up
5. **Dashboard** - Halaman statistik dengan 4 grafik:
   - 📊 Grafik batang perbandingan jumlah Laki-laki vs Perempuan
   - 🍩 Grafik komposisi pendidikan terakhir (Doughnut Chart)
   - 📈 Grafik distribusi usia pegawai (Horizontal Bar)
   - 🏢 Grafik jumlah pegawai per departemen

### ⚙️ Kebutuhan Non-Fungsional
- ✅ Konfirmasi pop-up menggunakan **SweetAlert2** sebelum hapus data
- ✅ Validasi input di sisi server dan client
- ✅ Responsive design (mobile-friendly)
- ✅ Flash message notification

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| **Backend** | PHP (Native OOP) | 7.4+ / 8.x |
| **Database** | MySQL | 5.7+ / 8.x |
| **Frontend** | HTML5, CSS3, JavaScript ES6+ | - |
| **CSS Framework** | Bootstrap 5.3 | 5.3.2 |
| **Icons** | Font Awesome 6 | 6.5.1 |
| **Charts** | Chart.js | 4.4.1 |
| **Alerts** | SweetAlert2 | 11.x |
| **Font** | Inter (Google Fonts) | - |

---

## 📁 Struktur Folder Proyek

```
sistem-pegawai/
│
├── 📄 index.php                 # Halaman Dashboard (utama)
├── 📄 pegawai.php               # Halaman Manajemen Pegawai (CRUD)
├── 📄 config.php                # Konfigurasi & Helper Functions
│
├── 📁 assets/                   # Static Assets
│   ├── css/
│   │   └── style.css           # Custom CSS styling
│   └── js/
│       └── main.js             # JavaScript utama & SweetAlert2
│
├── 📁 classes/                  # Kelas-kelas OOP (Backend)
│   ├── Database.php            # Kelas koneksi database (PDO)
│   └── Pegawai.php             # Model CRUD pegawai
│
├── 📁 includes/                 # Template Components
│   ├── header.php              # Template header (navbar, CSS)
│   └── footer.php              # Template footer (JS, scripts)
│
├── 📁 pages/                    # Halaman tambahan (opsional)
│
├── 📁 database/                 # File Database
│   └── schema.sql              # Skema SQL & sample data
│
├── 📁 assets/img/               # Gambar (opsional)
│
└── 📄 README.md                 # Dokumentasi ini

```

---

## 🚀 Instalasi & Cara Menjalankan

### Prasyarat
- [X] **Laragon** (atau XAMPP/WAMP) sudah terinstall
- [X] **PHP** versi 7.4 atau lebih tinggi
- [X] **MySQL** versi 5.7 atau lebih tinggi
- [X] Web browser modern (Chrome/Firefox/Edge)

### Langkah-langkah Instalasi:

#### 1️⃣ Copy Project ke Folder Laragon
```
1. Buka folder instalasi Laragon (biasanya C:\laragon\www)
2. Copy seluruh folder "sistem-pegawai" ke dalam folder www
3. Pastikan struktur path: C:\laragon\www\sistem-pegawai\
```

#### 2️⃣ Buat Database MySQL
```sql
-- Option A: Menggunakan phpMyAdmin
1. Buka browser, akses: http://localhost/phpmyadmin
2. Klik tab "Import"
3. Pilih file: sistem-pegawai/database/schema.sql
4. Klik tombol "Go"

-- Option B: Menggunakan Command Line
mysql -u root -p < database/schema.sql
```

#### 3⃣ Jalankan Aplikasi
```
1. Pastikan Laragon sudah running (Apache & MySQL ON)
2. Buka browser
3. Akses: http://localhost/sistem-pegawai/
   atau
   http://sistem-pegawai.test/ (jika menggunakan virtual host)
```

---

## 📊 Skema Database

### Tabel: `pegawai`

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| `id` | INT (PK, Auto Increment) | ID unik pegawai |
| `nip` | VARCHAR(20), UNIQUE | Nomor Induk Pegawai |
| `nama_lengkap` | VARCHAR(100) | Nama lengkap pegawai |
| `tempat_lahir` | VARCHAR(50) | Tempat lahir |
| `tanggal_lahir` | DATE | Tanggal lahir (untuk hitung usia) |
| `jenis_kelamin` | ENUM('Laki-laki','Perempuan') | Jenis kelamin |
| `alamat` | TEXT | Alamat lengkap |
| `no_telepon` | VARCHAR(15) | Nomor telepon |
| `email` | VARCHAR(100) | Email pegawai |
| `jabatan` | VARCHAR(100) | Posisi/jabatan |
| `departemen` | VARCHAR(100) | Departemen/bagian |
| `pendidikan_terakhir` | ENUM('SMA/SMK','D3','S1','S2','S3') | Tingkat pendidikan |
| `status_kerja` | ENUM('Aktif','Cuti','Resign','Pensiun') | Status kerja |
| `tanggal_gabung` | DATE | Tanggal bergabung |
| `foto_profil` | VARCHAR(255) | Path foto profil |
| `created_at` | TIMESTAMP | Waktu data dibuat |
| `updated_at` | TIMESTAMP | Waktu data diupdate |

### Relasi Entity Relationship (ERD)

```
┌─────────────────────────────────────────┐
│                PEGAWAI                  │
├─────────────────────────────────────────┤
│ PK  id                                  │
│    nip (UNIQUE)                         │
│    nama_lengkap                         │
│    tempat_lahir                         │
│    tanggal_lahir                        │
│    jenis_kelamin                        │
│    alamat                               │
│    no_telepon                           │
│    email                                │
│    jabatan                              │
│    departemen                           │
│    pendidikan_terakhir                  │
│    status_kerja                         │
│    tanggal_gabung                       │
│    foto_profil                          │
│    created_at                           │
│    updated_at                           │
└─────────────────────────────────────────┘
         │
         │ (Tidak ada foreign key - single table design)
         ▼
```

---

## 💻 Penjelasan Kode Program

### Arsitektur OOP (Object-Oriented Programming)

Proyek ini menerapkan **Pemrograman Berorientasi Objek (OOP)** dengan pola berikut:

```
┌─────────────┐       uses        ┌─────────────┐
│  Database   │ ◄──────────────── │   Pegawai   │
│  (Singleton)│                   │   (Model)   │
└─────────────┘                   └─────────────┘
       │                                   │
       │ PDO Connection                    │ CRUD Methods
       ▼                                   ▼
   MySQL Server                      pegawai table
```

#### 1. Kelas `Database.php`
- **Pattern**: Singleton (hanya 1 instance koneksi)
- **Fungsi**: Menangani koneksi PDO ke MySQL
- **Method penting**: `getInstance()`, `query()`, `fetchAll()`, `fetchOne()`

#### 2. Kelas `Pegawai.php`
- **Pattern**: Model / Active Record (sederhana)
- **Fungsi**: Operasi CRUD dan statistik
- **Method penting**:
  - `create($data)` - Tambah pegawai baru
  - `getAll()` - Ambil semua data
  - `getById($id)` - Ambil data by ID
  - `update($id, $data)` - Update data
  - `delete($id)` - Hapus data
  - `getStatistikJenisKelamin()` - Data grafik jenis kelamin
  - `getStatistikPendidikan()` - Data grafik pendidikan
  - `getStatistikUsia()` - Data grafik usia

---


## 🔒 Keamanan

- ✅ **Prepared Statements** (PDO) - Mencegah SQL Injection
- ✅ **Input Sanitization** - `sanitizeInput()` helper
- ✅ **Output Escaping** - `htmlspecialchars()` pada semua output
- ✅ **CSRF Protection** (basic) - Form validation
- ✅ **XSS Prevention** - Escape HTML entities

---

## 📞 Troubleshooting

### Masalah Umum:

| Masalah | Solusi |
|---------|--------|
| "Connection failed" | Cek konfigurasi database di `config.php` |
| Blank page putih | Enable error reporting di `config.php` |
| Chart tidak muncul | Cek koneksi internet (CDN) atau download library lokal |
| SweetAlert tidak jalan | Cek console browser (F12) untuk error |

---

## 👨‍💻 Author

**Dibuat untuk:** Uji Kompetensi Junior Web Programmer  
**Teknologi:** PHP OOP + MySQL + Bootstrap 5 + Chart.js  
**Versi:** 1.0.0  
**Tahun:** 2026

---

## 📜 Lisensi

Proyek ini dibuat untuk tujuan edukasi dan dapat digunakan secara bebas untuk pembelajaran.
