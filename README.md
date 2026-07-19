# Aplikasi Pengajuan Cuti Pegawai (Tugas Akhir Semester - Pemrograman Web 3)

Aplikasi web dinamis ini dirancang untuk memfasilitasi pegawai dalam mengajukan permohonan cuti secara digital dan memungkinkan pengelola/admin untuk meninjau serta memperbarui status ajuan tersebut. 

Proyek ini dibangun untuk memenuhi kriteria Ujian Akhir Semester (UAS) mata kuliah Pemrograman Web 3, dengan implementasi fungsionalitas CRUD lengkap, integrasi database cloud Supabase (PostgreSQL), sistem login pengguna (auth), dan *live deployment* ke Railway.

---

## Spesifikasi Teknis (Tech Stack)
*   **Framework Utama:** Laravel 11 (PHP 8.3)
*   **Database Cloud:** Supabase (PostgreSQL) dengan Connection Pooler (IPv4)
*   **Antarmuka UI:** Tailwind CSS (Modern Dark Mode & Glassmorphism) & Font Awesome
*   **Version Control:** Git & GitHub
*   **Deployment Server:** Railway (HTTPS terkonfigurasi)

---

## Fitur Utama

1.  **Sistem Registrasi & Login (Autentikasi)**
    *   Membatasi akses data. Pengguna wajib mendaftar dan login terlebih dahulu untuk mengakses menu utama.
    *   Pendaftaran akun baru otomatis disetel sebagai **Pegawai** (tidak ada pilihan Peran saat registrasi untuk alasan keamanan).
    *   Akun **Admin** bersifat khusus/tunggal dan didaftarkan melalui seeder/database langsung.
2.  **Pegawai Mode (Pengajuan Cuti - CRUD)**
    *   **Create (Pengajuan)**: Pegawai mengisi jenis cuti, tanggal mulai, tanggal selesai, dan alasan cuti. Nama lengkap dan NIP pengaju otomatis terisi dari sesi akun login untuk mencegah pemalsuan.
    *   **Read (Daftar Ajuan)**: Menampilkan tabel daftar riwayat ajuan pribadi pegawai yang bersangkutan.
    *   **Update (Edit)**: Pegawai dapat mengedit detail ajuan cuti selama status ajuan tersebut masih dalam tahap "Menunggu".
    *   **Delete (Batalkan)**: Pegawai dapat menghapus/membatalkan ajuan cutinya jika statusnya masih "Menunggu".
3.  **Admin Mode (Persetujuan Cuti - CRUD)**
    *   **Read (Seluruh Ajuan)**: Admin dapat melihat seluruh daftar permohonan cuti dari semua pegawai yang masuk ke database.
    *   **Update (Persetujuan)**: Admin dapat memproses dan mengubah status ajuan cuti pegawai secara realtime menjadi **Disetujui** atau **Ditolak**.
    *   **Pencarian & Filter**: Menyediakan kolom pencarian nama/NIP pegawai dan filter status cuti.
4.  **Validasi Tanggal Logis**
    *   Sistem secara otomatis menolak pengajuan jika tanggal selesai cuti mendahului tanggal mulai cuti, dilengkapi dengan pesan peringatan di browser (JavaScript) serta di server (Laravel Validation).
5.  **Force HTTPS (Production Ready)**
    *   Aplikasi dikonfigurasi untuk memaksa semua tautan menggunakan protokol HTTPS saat di-deploy, guna mencegah error *mixed content* atau peringatan *insecure form submission* di browser.

---

## Berkas Sumber Kode (CRUD & Autentikasi)

Berikut adalah file-file utama yang digunakan untuk mengimplementasikan fungsionalitas CRUD dan sistem Autentikasi di aplikasi ini:

1.  **Logika CRUD**:
    *   **Controller**: [AjuanCutiController.php](file:///c:/laragon/www/pengajuan-cuti/app/Http/Controllers/AjuanCutiController.php) — Tempat semua logika pengolahan data (Create, Read, Update, Delete) dan validasi tanggal dilaksanakan.
    *   **Model**: [AjuanCuti.php](file:///c:/laragon/www/pengajuan-cuti/app/Models/AjuanCuti.php) — Representasi objek Eloquent ORM untuk tabel database `ajuan_cuti`.
    *   **View (Tampilan)**: [dashboard.blade.php](file:///c:/laragon/www/pengajuan-cuti/resources/views/dashboard.blade.php) — Dasbor utama yang memuat formulir input cuti (Create), tabel daftar riwayat cuti (Read), modul edit popup (Update), dan tombol pembatalan (Delete).
2.  **Sistem Autentikasi (Login/Register)**:
    *   **Controller**: [AuthController.php](file:///c:/laragon/www/pengajuan-cuti/app/Http/Controllers/AuthController.php) — Mengurus alur registrasi user baru, login, dan penghapusan session logout.
    *   **Views**:
        *   [login.blade.php](file:///c:/laragon/www/pengajuan-cuti/resources/views/auth/login.blade.php) — Tampilan form masuk pengguna.
        *   [register.blade.php](file:///c:/laragon/www/pengajuan-cuti/resources/views/auth/register.blade.php) — Tampilan form daftar akun pegawai baru.
3.  **Routing & Database**:
    *   **Rute Web**: [web.php](file:///c:/laragon/www/pengajuan-cuti/routes/web.php) — Mengatur pemetaan URL rute aplikasi dan pembatasan hak akses via middleware `auth`.
    *   **Migrasi Tabel Cuti**: [2026_07_19_105930_create_ajuan_cuti_table.php](file:///c:/laragon/www/pengajuan-cuti/database/migrations/2026_07_19_105930_create_ajuan_cuti_table.php) — Mengatur pembuatan tabel skema `ajuan_cuti` di database cloud.
    *   **Migrasi Kolom User**: [2026_07_19_112222_add_role_and_nip_to_users_table.php](file:///c:/laragon/www/pengajuan-cuti/database/migrations/2026_07_19_112222_add_role_and_nip_to_users_table.php) — Menambahkan kolom NIP dan peran (*role*) pegawai/admin ke tabel pengguna.

---

## Skema Database

### 1. Tabel: `users`
Tabel ini digunakan untuk mengelola data akun pengguna yang login.
| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Auto Increment. |
| `nip` | Varchar | Nomor Induk Pegawai (Unik). |
| `name` | Varchar | Nama lengkap pengguna. |
| `email` | Varchar | Email pengguna untuk login (Unik). |
| `password` | Varchar | Password terenkripsi (hashed). |
| `role` | Enum | Peran akun: `pegawai` atau `admin`. |
| `created_at` / `updated_at` | Timestamp | Waktu pembuatan & pembaruan data otomatis. |

### 2. Tabel: `ajuan_cuti`
Tabel ini digunakan untuk mengelola data pengajuan cuti pegawai.
| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Auto Increment. |
| `nip` | Varchar | NIP pegawai yang mengajukan cuti. |
| `nama` | Varchar | Nama lengkap pegawai pengaju. |
| `jenis_cuti` | Enum | Jenis cuti: `Cuti Tahunan`, `Cuti Sakit`, `Cuti Alasan Penting`, `Cuti Melahirkan`. |
| `tanggal_mulai` | Date | Tanggal awal pelaksanaan cuti. |
| `tanggal_selesai` | Date | Tanggal akhir pelaksanaan cuti. |
| `keterangan` | Text | Penjelasan/alasan detail mengenai ajuan cuti. |
| `status` | Enum | Status persetujuan: `Menunggu`, `Disetujui`, `Ditolak`. Default: `Menunggu`. |
| `created_at` / `updated_at` | Timestamp | Waktu rekam data otomatis. |

---

## Panduan Instalasi Lokal (Development)

1.  **Clone Repository**
    ```bash
    git clone https://github.com/daomiodroid/sistem-ajuan-cuti.git
    cd sistem-ajuan-cuti
    ```
2.  **Instal Dependensi PHP & Node.js**
    ```bash
    composer install
    npm install
    ```
3.  **Salin File Environment**
    Salin file `.env.example` menjadi `.env`
    ```bash
    cp .env.example .env
    ```
4.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```
5.  **Konfigurasi Database Supabase di `.env`**
    Buka file `.env` lalu sesuaikan baris kredensial database menggunakan Connection Pooler Supabase:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=your-supabase-pooler-host.supabase.com
    DB_PORT=5432
    DB_DATABASE=postgres
    DB_USERNAME=postgres.your-project-id
    DB_PASSWORD=your-database-password
    ```
6.  **Jalankan Migrasi Database**
    ```bash
    php artisan migrate
    ```
7.  **Jalankan Server Lokal**
    ```bash
    php artisan serve
    ```
    Buka **http://127.0.0.1:8000** di browser Anda.

---

## Akun Demo Pengujian

Aplikasi sudah memiliki 2 akun default yang tersimpan di database Supabase untuk kebutuhan presentasi:

### 1. Akun Pegawai (Mencoba Pengajuan Cuti)
*   **Email:** `budi@gmail.com`
*   **Password:** `budi123`
*   **NIP Pegawai:** `123456`
*   **Nama Pegawai:** `Budi Santoso`

### 2. Akun Admin (Mencoba Persetujuan Cuti)
*   **Email:** `admin@gmail.com`
*   **Password:** `admin123`
*   **NIP Admin:** `999999`
*   **Nama Admin:** `Admin Utama`

---

## Pengujian Otomatis (Automated Tests)
Proyek ini dilengkapi dengan skrip *feature test* otomatis untuk memvalidasi keamanan rute, hak akses CRUD pegawai, validasi tanggal selesai tidak boleh mendahului tanggal mulai, dan hak akses approval admin.

Untuk menjalankan pengujian unit:
```bash
php artisan test
```
**Hasil Pengujian:**
```json
{
  "tests": 7,
  "passed": 7,
  "assertions": 15,
  "duration_ms": 730
}
```

---

## Langkah Deployment ke Railway

1.  **Hubungkan Akun GitHub ke Railway** di portal [railway.app](https://railway.app).
2.  Buat **New Project** -> Pilih repositori **`sistem-ajuan-cuti`**.
3.  Konfigurasikan **Environment Variables** di tab proyek Railway:
    *   `APP_KEY` = Kunci unik dari `.env` lokal Anda
    *   `APP_ENV` = `production`
    *   `APP_DEBUG` = `false`
    *   `DB_CONNECTION` = `pgsql`
    *   `DB_HOST` = `your-supabase-pooler-host.supabase.com`
    *   `DB_PORT` = `5432`
    *   `DB_DATABASE` = `postgres`
    *   `DB_USERNAME` = `postgres.your-project-id`
    *   `DB_PASSWORD` = `your-database-password`
4.  Di tab **Settings** -> **Domains**, klik **Generate Domain** untuk mendapatkan Live URL.
