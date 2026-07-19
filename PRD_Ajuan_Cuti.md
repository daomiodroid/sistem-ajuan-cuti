# Product Requirements Document (PRD) - Sistem Informasi Ajuan Cuti

## 1. Ikhtisar Proyek
Proyek ini adalah pengembangan aplikasi web dinamis berbasis **Sistem Informasi Ajuan Cuti Pegawai**. Aplikasi ini dirancang untuk memfasilitasi pegawai dalam mengajukan permohonan cuti secara digital dan memungkinkan pengelola/admin untuk meninjau serta memperbarui status ajuan tersebut. 

Proyek ini dibangun untuk memenuhi kriteria Ujian Akhir Semester (UAS) mata kuliah Pemrograman Web 3, yang mewajibkan implementasi fungsionalitas CRUD, integrasi database cloud, dan *live deployment*.

## 2. Tujuan
*   Membangun aplikasi fungsional dengan fitur utama **CRUD (Create, Read, Update, Delete)**.
*   Mengimplementasikan **Supabase (PostgreSQL)** sebagai solusi database cloud secara penuh.
*   Menyediakan antarmuka (UI) yang **responsif** dan mengedepankan *usability* yang baik bagi pengguna (diakses via *mobile* maupun *desktop*).
*   Melakukan *deployment* aplikasi ke *environment production* (publik) menggunakan platform cloud hosting (seperti Vercel, Railway, atau Render).

## 3. Spesifikasi Teknis (Tech Stack)
*   **Backend & Frontend:** Laravel (PHP) dengan Blade Templating.
*   **Database:** Supabase (PostgreSQL).
*   **UI Framework:** Tailwind CSS / Bootstrap 5 (untuk responsivitas yang cepat).
*   **Version Control:** Git & GitHub (Public Repository).
*   **Deployment:** Railway / Render (cocok untuk environment PHP/Laravel).

## 4. Kebutuhan Fungsional (Core Features)
Aplikasi harus memuat siklus pengolahan data yang lengkap (CRUD):

1.  **Create (Pengajuan Cuti)**
    *   Pengguna dapat mengisi form pengajuan cuti.
    *   Input yang diperlukan: NIP (Nomor Induk Pegawai), Nama Lengkap, Jenis Cuti, Tanggal Mulai, Tanggal Selesai, dan Keterangan/Alasan.
2.  **Read (Daftar Ajuan)**
    *   Menampilkan tabel daftar riwayat ajuan cuti dari database.
    *   Tabel memuat kolom informasi pemohon, tanggal, dan **Status** (Menunggu, Disetujui, Ditolak).
3.  **Update (Edit / Konfirmasi Cuti)**
    *   Pengguna dapat mengedit ajuan cuti jika masih dalam status "Menunggu".
    *   (Simulasi Admin) Dapat memperbarui status cuti menjadi "Disetujui" atau "Ditolak".
4.  **Delete (Pembatalan)**
    *   Pengguna dapat menghapus atau membatalkan ajuan cuti dari sistem.

## 5. Skema Database
Sistem menggunakan satu tabel utama `ajuan_cuti` untuk memenuhi syarat CRUD dasar.

**Tabel: `ajuan_cuti`**
| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt / UUID | Primary Key, Auto Increment. |
| `nip` | Varchar | Nomor Induk Pegawai. |
| `nama` | Varchar | Nama pemohon cuti. |
| `jenis_cuti` | Varchar / Enum | Pilihan: Cuti Tahunan, Cuti Sakit, Cuti Alasan Penting, Cuti Melahirkan. |
| `tanggal_mulai` | Date | Tanggal awal pelaksanaan cuti. |
| `tanggal_selesai` | Date | Tanggal akhir pelaksanaan cuti. |
| `keterangan` | Text | Penjelasan/alasan detail mengenai ajuan cuti. |
| `status` | Varchar / Enum | Pilihan: Menunggu, Disetujui, Ditolak. Default: "Menunggu". |
| `created_at` | Timestamp | Waktu rekam data otomatis. |
| `updated_at` | Timestamp | Waktu pembaruan data otomatis. |

## 6. Kebutuhan Non-Fungsional & Kriteria Penerimaan (Acceptance Criteria)
*   **Database Cloud Isolation:** Sistem mutlak terhubung ke Supabase. Tidak ada data yang tersimpan di *localhost* / phpMyAdmin lokal saat aplikasi berjalan.
*   **Responsivitas:** Halaman tabel data dan form tidak boleh *overflow* atau terpotong saat dibuka di layar *smartphone*.
*   **Keamanan Dasar:** Validasi input pada form untuk mencegah pengiriman data kosong atau format tanggal yang tidak logis (misal: tanggal selesai lebih awal dari tanggal mulai).

## 7. Deliverables (Keluaran Proyek)
Untuk keperluan pengumpulan UAS, proyek ini akan menghasilkan:
1.  **Live URL:** Tautan aplikasi yang sudah berjalan di *production*.
2.  **Public Repository:** Tautan GitHub berisi *source code* lengkap.
3.  **Video Presentasi:** Video berdurasi 5-10 menit (diunggah ke YouTube/G-Drive) yang berisi:
    *   *Facecam* & *Screen record*.
    *   Demo fungsionalitas CRUD di Live URL.
    *   Tampilan skema tabel dari *dashboard* Supabase.
    *   *Code Review* (fokus pada koneksi database `.env`, logika *insert* data, dan logika *fetch* data).
4.  **Dokumen Pengumpulan:** File PDF bernama `UAS_PemrogramanWeb_[NIM]_[Nama].pdf` yang berisi rekapitulasi tautan di atas beserta identitas pembuat.
