# LAPORAN TUGAS AKHIR SEMESTER
## MATA KULIAH: PEMROGRAMAN WEB 3
### TOPIK: SISTEM PENGAJUAN CUTI PEGAWAI BERBASIS LARAVEL 11 DAN DATABASE SUPABASE

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang
Proses pengajuan cuti secara konvensional seringkali mengalami kendala birokrasi, penumpukan berkas fisik, serta lambatnya proses persetujuan oleh atasan. Untuk mengatasi kendala tersebut, dibangun sebuah sistem informasi pengajuan cuti pegawai berbasis web yang dinamis. Aplikasi ini mempermudah pegawai dalam mengirim permohonan cuti secara digital dan membantu administrator/pengelola untuk meninjau secara langsung (*realtime*).

### 1.2 Tujuan Proyek
*   Mengembangkan aplikasi web berbasis framework **Laravel 11** dengan implementasi operasi **CRUD** (Create, Read, Update, Delete) yang lengkap.
*   Mengintegrasikan sistem dengan database cloud **Supabase (PostgreSQL)** untuk penyimpanan data terpusat.
*   Mengimplementasikan sistem autentikasi pengguna (*Login/Register*) dengan pembedaan hak akses (*Role-based access control*).
*   Menyediakan tampilan antarmuka (UI) yang responsif menggunakan framework **Tailwind CSS**.
*   Melakukan publikasi sistem (*live deployment*) ke cloud hosting **Railway**.

### 1.3 Spesifikasi Sistem & Lingkungan Kerja
*   **Backend:** PHP 8.3 dengan Laravel 11.
*   **Database:** Supabase (PostgreSQL).
*   **Frontend:** Blade Templating dengan Tailwind CSS.
*   **Web Server Lokal:** Laragon.
*   **Version Control:** Git & GitHub.
*   **Deployment:** Railway.

---

## BAB II: DESAIN DATABASE (SUPABASE)

Database sistem menggunakan dua tabel utama yang saling berelasi secara logis melalui NIP (Nomor Induk Pegawai).

### 2.1 Tabel: `users`
Digunakan untuk mencatat akun pengguna yang berhak masuk ke sistem.
*   `id`: Primary Key (BigInt, Auto Increment).
*   `nip`: Nomor Induk Pegawai (Varchar, Unik) - Digunakan untuk mengaitkan pengajuan cuti.
*   `name`: Nama lengkap pegawai/user (Varchar).
*   `email`: Alamat email login (Varchar, Unik).
*   `password`: Kata sandi terenkripsi (Varchar).
*   `role`: Hak akses sistem (Enum: `pegawai`, `admin`).
*   `created_at` / `updated_at`: Waktu perekaman log otomatis.

### 2.2 Tabel: `ajuan_cuti`
Digunakan untuk menyimpan data permohonan cuti pegawai.
*   `id`: Primary Key (BigInt, Auto Increment).
*   `nip`: NIP pegawai pemohon (Varchar).
*   `nama`: Nama lengkap pegawai pemohon (Varchar).
*   `jenis_cuti`: Pilihan jenis cuti (Enum: `Cuti Tahunan`, `Cuti Sakit`, `Cuti Alasan Penting`, `Cuti Melahirkan`).
*   `tanggal_mulai`: Tanggal awal cuti (Date).
*   `tanggal_selesai`: Tanggal berakhirnya cuti (Date).
*   `keterangan`: Deskripsi/alasan detail cuti (Text).
*   `status`: Status persetujuan (Enum: `Menunggu`, `Disetujui`, `Ditolak`). Default: `Menunggu`.
*   `created_at` / `updated_at`: Waktu perekaman log otomatis.

---

## BAB III: IMPLEMENTASI SOURCE CODE & PENJELASAN

### 3.1 Pengaturan Rute Web (`routes/web.php`)
Mengatur pemetaan URL aplikasi. Rute dasbor dan CRUD diproteksi oleh middleware `auth` sehingga hanya pengguna yang sudah login yang dapat mengaksesnya.

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AjuanCutiController;

// Rute untuk tamu yang belum login (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Aksi keluar akun (Logout)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rute dasbor & CRUD cuti (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/', [AjuanCutiController::class, 'index'])->name('dashboard');
    Route::post('/ajuan-cuti', [AjuanCutiController::class, 'store'])->name('ajuan-cuti.store');
    Route::put('/ajuan-cuti/{id}', [AjuanCutiController::class, 'update'])->name('ajuan-cuti.update');
    Route::patch('/ajuan-cuti/{id}/status', [AjuanCutiController::class, 'updateStatus'])->name('ajuan-cuti.status');
    Route::delete('/ajuan-cuti/{id}', [AjuanCutiController::class, 'destroy'])->name('ajuan-cuti.destroy');
});
```
**Penjelasan Rute:**
*   `Route::middleware('guest')` membatasi agar halaman login/register hanya bisa diakses oleh user yang belum masuk.
*   `Route::middleware('auth')` memproteksi rute utama dasbor (`/`) serta aksi manipulasi data agar tidak bisa diakses secara ilegal tanpa login.

---

### 3.2 Migrasi Database Tabel Cuti (`database/migrations/2026_07_19_105930_create_ajuan_cuti_table.php`)
Digunakan untuk mendefinisikan struktur fisik tabel `ajuan_cuti` di PostgreSQL Supabase.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajuan_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nip');
            $table->string('nama');
            $table->enum('jenis_cuti', ['Cuti Tahunan', 'Cuti Sakit', 'Cuti Alasan Penting', 'Cuti Melahirkan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajuan_cuti');
    }
};
```
**Penjelasan Migrasi:**
*   `enum('jenis_cuti', [...])` membatasi agar jenis cuti hanya bernilai salah satu dari pilihan yang telah ditentukan.
*   `default('Menunggu')` mengatur agar setiap ajuan baru secara otomatis berstatus pending hingga diproses oleh admin.

---

### 3.3 Model Data Cuti (`app/Models/AjuanCuti.php`)
Digunakan untuk memetakan database ke dalam pemrograman berbasis objek Laravel Eloquent.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AjuanCuti extends Model
{
    protected $table = 'ajuan_cuti';

    protected $fillable = [
        'nip',
        'nama',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}
```
**Penjelasan Model:**
*   `$fillable` mendefinisikan kolom mana saja yang boleh diisi secara massal (*mass assignment*) untuk menghindari celah keamanan.
*   `$casts` memaksa kolom tanggal diubah menjadi tipe objek Date Carbon di Laravel agar format tanggal mudah dikelola.

---

### 3.4 Controller Logika CRUD (`app/Http/Controllers/AjuanCutiController.php`)
Mengatur logika proses CRUD dan aturan bisnis (seperti pengecekan hak akses dan pemisahan tampilan data).

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AjuanCuti;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AjuanCutiController extends Controller
{
    /**
     * READ: Menampilkan data dasbor, daftar cuti, dan statistik ringkasan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AjuanCuti::orderBy('created_at', 'desc');

        // Jika login sebagai pegawai, batasi hanya menampilkan data miliknya sendiri
        if ($user->role === 'pegawai') {
            $query->where('nip', $user->nip);
        }

        // Filter berdasarkan status (opsional)
        if ($request->has('status') && in_array($request->status, ['Menunggu', 'Disetujui', 'Ditolak'])) {
            $query->where('status', $request->status);
        }

        // Pencarian berdasarkan nama / NIP (khusus Admin)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $ajuanCutis = $query->paginate(10)->withQueryString();

        // Penghitungan statistik ringkasan kartu di dashboard
        if ($user->role === 'pegawai') {
            $stats = [
                'total' => AjuanCuti::where('nip', $user->nip)->count(),
                'menunggu' => AjuanCuti::where('nip', $user->nip)->where('status', 'Menunggu')->count(),
                'disetujui' => AjuanCuti::where('nip', $user->nip)->where('status', 'Disetujui')->count(),
                'ditolak' => AjuanCuti::where('nip', $user->nip)->where('status', 'Ditolak')->count(),
            ];
        } else {
            $stats = [
                'total' => AjuanCuti::count(),
                'menunggu' => AjuanCuti::where('status', 'Menunggu')->count(),
                'disetujui' => AjuanCuti::where('status', 'Disetujui')->count(),
                'ditolak' => AjuanCuti::where('status', 'Ditolak')->count(),
            ];
        }

        return view('dashboard', compact('ajuanCutis', 'stats'));
    }

    /**
     * CREATE: Menyimpan data pengajuan cuti baru pegawai
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'pegawai') {
            return redirect()->route('dashboard')->with('error', 'Hanya pegawai yang dapat mengajukan cuti.');
        }

        $validated = $request->validate([
            'jenis_cuti' => [
                'required',
                Rule::in(['Cuti Tahunan', 'Cuti Sakit', 'Cuti Alasan Penting', 'Cuti Melahirkan']),
            ],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        // Mengamankan data pengaju dengan mengambil langsung dari user login
        $validated['nip'] = $user->nip;
        $validated['nama'] = $user->name;
        $validated['status'] = 'Menunggu';

        AjuanCuti::create($validated);

        return redirect()->route('dashboard')->with('success', 'Ajuan cuti berhasil dikirim.');
    }

    /**
     * UPDATE: Memperbarui data ajuan cuti (hanya untuk status Menunggu)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $ajuan = AjuanCuti::findOrFail($id);

        if ($ajuan->nip !== $user->nip) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        if ($ajuan->status !== 'Menunggu') {
            return redirect()->route('dashboard')->with('error', 'Ajuan yang telah diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'jenis_cuti' => [
                'required',
                Rule::in(['Cuti Tahunan', 'Cuti Sakit', 'Cuti Alasan Penting', 'Cuti Melahirkan']),
            ],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ]);

        $ajuan->update($validated);

        return redirect()->route('dashboard')->with('success', 'Ajuan cuti berhasil diperbarui.');
    }

    /**
     * UPDATE STATUS: Aksi Admin menyetujui / menolak pengajuan cuti
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $ajuan = AjuanCuti::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Disetujui', 'Ditolak'])],
        ]);

        $ajuan->update(['status' => $validated['status']]);

        return redirect()->route('dashboard')->with('success', "Status ajuan atas nama {$ajuan->nama} berhasil diubah.");
    }

    /**
     * DELETE: Membatalkan atau menghapus ajuan cuti
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $ajuan = AjuanCuti::findOrFail($id);
        $namaPegawai = $ajuan->nama;

        if ($user->role !== 'admin' && $ajuan->nip !== $user->nip) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $ajuan->delete();

        return redirect()->route('dashboard')->with('success', "Ajuan cuti atas nama {$namaPegawai} telah dibatalkan.");
    }
}
```
**Penjelasan Metode Controller:**
*   `index()`: Memisahkan data yang ditarik dari database berdasarkan status login. Jika login sebagai pegawai, data disaring menggunakan klausa `where('nip', $user->nip)` agar pegawai lain tidak bisa melihat data kita.
*   `store()`: Memasukkan data cuti baru. Keamanannya ditingkatkan dengan menyuntikkan nilai `nip` dan `nama` secara langsung dari sesi login aktif, bukan dari input form bebas. Validasi `after_or_equal:tanggal_mulai` memastikan tanggal akhir tidak mendahului tanggal mulai.
*   `updateStatus()`: Menangani pemrosesan admin. Memvalidasi input status hanya boleh berisi `Disetujui` atau `Ditolak`.
*   `destroy()`: Menghapus data permohonan cuti dari database PostgreSQL Supabase.

---

### 3.5 Controller Autentikasi (`app/Http/Controllers/AuthController.php`)
Mengendalikan proses log masuk, pendaftaran akun baru dengan pemilihan peran, dan penghancuran sesi saat log keluar.

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:users,nip',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['pegawai', 'admin'])],
        ]);

        $user = User::create([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }
}
```
**Penjelasan Metode Autentikasi:**
*   `login()`: Menggunakan metode `Auth::attempt` bawaan Laravel untuk memverifikasi kecocokan email dan password terenkripsi di database. `regenerate()` dijalankan untuk mencegah celah keamanan pembajakan sesi (*session fixation*).
*   `register()`: Menyimpan akun user baru. Kolom password disandi menggunakan `Hash::make()` (Bcrypt) untuk menjamin keamanan password sebelum disimpan ke database Supabase.
*   `logout()`: Mengosongkan data sesi pengguna dan memperbarui token CSRF baru.

---

## BAB IV: PANDUAN INSTALASI & PENGUJIAN LOKAL

### 4.1 Langkah Instalasi Lokal
1.  **Clone Source Code**: Unduh repositori dari GitHub:
    ```bash
    git clone https://github.com/daomiodroid/sistem-ajuan-cuti.git
    cd sistem-ajuan-cuti
    ```
2.  **Instal Dependensi PHP**: Unduh paket library composer:
    ```bash
    composer install
    ```
3.  **Salin File Konfigurasi**:
    Buat file konfigurasi `.env` dengan menduplikat `.env.example`:
    ```bash
    cp .env.example .env
    ```
4.  **Aktifkan Ekstensi PostgreSQL di Laragon**:
    *   Buka editor, buka file konfigurasi PHP Anda (`php.ini`).
    *   Hilangkan tanda titik koma `;` pada baris:
        `extension=pdo_pgsql`
        `extension=pgsql`
    *   Simpan file dan restart server Laragon Anda.
5.  **Isi Kredensial Database Supabase**:
    Buka file `.env` dan masukkan data pooler IPv4 Supabase (diambil dari menu Settings -> Database di web Supabase Anda):
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=your-supabase-pooler-host.supabase.com
    DB_PORT=5432
    DB_DATABASE=postgres
    DB_USERNAME=postgres.your-project-id
    DB_PASSWORD=your-database-password
    ```
6.  **Jalankan Migrasi Database**:
    Kirimkan struktur tabel database ke Supabase:
    ```bash
    php artisan migrate
    ```
7.  **Jalankan Aplikasi**:
    ```bash
    php artisan serve
    ```
    Akses web di: **http://127.0.0.1:8000**

### 4.2 Akun Contoh untuk Uji Coba Demo
Tabel database Anda telah diisi dengan dua akun pengujian default:
*   **Akun Pegawai**:
    *   Email: `budi@gmail.com`
    *   Password: `budi123`
*   **Akun Admin**:
    *   Email: `admin@gmail.com`
    *   Password: `admin123`

---

## BAB V: PANDUAN LIVE DEPLOYMENT (RAILWAY)

Proses publikasi (*deployment*) dilakukan menggunakan platform Railway dengan mengaktifkan integrasi repositori GitHub.

### 5.1 Langkah-Langkah Deploy ke Railway
1.  **Unggah Kode ke GitHub**:
    Hubungkan folder lokal Anda dengan repositori GitHub baru dan lakukan push:
    ```bash
    git remote add origin https://github.com/USERNAME/REPOSITORI.git
    git branch -M main
    git push -u origin main
    ```
2.  **Hubungkan ke Railway**:
    *   Buka **[railway.app](https://railway.app)** dan login via GitHub.
    *   Klik **New Project** -> Pilih **Deploy from GitHub repo** -> Pilih repositori aplikasi Anda.
    *   Klik **Deploy Now**.
3.  **Pengaturan Variabel Lingkungan (Environment Variables)**:
    Setelah build awal dimulai, masuk ke tab **Variables** di panel proyek Railway Anda. Masukkan seluruh variabel `.env` Anda sebagai berikut:
    *   `APP_KEY` = (Salin dari file `.env` lokal Anda)
    *   `APP_ENV` = `production`
    *   `APP_DEBUG` = `false`
    *   `DB_CONNECTION` = `pgsql`
    *   `DB_HOST` = `your-supabase-pooler-host.supabase.com`
    *   `DB_PORT` = `5432`
    *   `DB_DATABASE` = `postgres`
    *   `DB_USERNAME` = `postgres.your-project-id`
    *   `DB_PASSWORD` = `your-database-password`
4.  **Mengatasi Error Keamanan Submisi Form (Mixed Content)**:
    Saat dideploy ke server HTTPS (seperti Railway), browser akan memblokir pengiriman form karena Laravel mendeteksi rute sebagai HTTP biasa. Untuk mengatasinya, pemaksaan HTTPS telah ditambahkan pada berkas `AppServiceProvider.php`:
    ```php
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
    ```
5.  **Membuat URL Live**:
    Masuk ke tab **Settings** proyek Railway Anda, temukan kolom **Domains**, dan klik **Generate Domain**. Railway akan menyediakan alamat web publik gratis (misalnya `sistem-ajuan-cuti-production.up.railway.app`).
