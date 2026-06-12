# Sistem Informasi Kasir "Brew & Co." (POS)

Sistem Informasi Kasir (Point of Sale) berbasis web yang dirancang khusus untuk operasional Coffee Shop "Brew & Co.". Proyek ini dibangun menggunakan framework **Laravel 11.x**, database **MySQL**, template engine **Blade**, dan styling **CSS Vanilla** tanpa menggunakan framework CSS eksternal (Tailwind/Bootstrap). 

Proyek ini ditujukan untuk memenuhi persyaratan Tugas Akhir / Skripsi dengan mengutamakan kerapihan struktur kode, pola arsitektur MVC (Model-View-Controller), pemisahan logika bisnis melalui *Service Classes*, dan kepatuhan terhadap standar PSR-12.

---

## Fitur Utama

1. **Autentikasi & Multi-role (FR-01)**:
   - Login & Logout dengan sesi terproteksi.
   - Hak akses berbeda untuk **Admin** dan **Kasir** (Role-based Access Control via Middleware).
   - Pengamanan halaman pasca logout (mencegah akses kembali lewat tombol back browser).
2. **Menu Transaksi & Keranjang Digital (FR-02)**:
   - Panel grid produk 3 kolom dengan emoji icon representatif.
   - Filter kategori produk (Semua, Coffee, Non-Coffee, Refreshment, Snack).
   - Pencarian produk real-time.
   - Keranjang belanja berbasis session server (aman terhadap reload halaman).
   - Kalkulasi otomatis di backend (Subtotal, Pajak 10%, Total Grand Total).
   - Perhitungan kembalian kasir secara real-time.
3. **Cetak Struk Thermal (FR-03)**:
   - Cetak struk belanja thermal (lebar kertas 58mm/80mm) menggunakan Browser Print API.
   - Layout struk yang rapi dan memuat informasi dinamis (nama toko, alamat, kasir, waktu, rincian produk, total, pembayaran, kembalian).
4. **Riwayat Penjualan (FR-04)**:
   - Daftar riwayat transaksi real-time dari database.
   - Filter data berdasarkan rentang tanggal.
   - Ringkasan harian (Total Pendapatan & Jumlah Transaksi) otomatis.
   - Fitur cetak ulang struk belanja yang identik dengan aslinya.
5. **Pengaturan Sistem (FR-05)**:
   - **Semua Role**: Ubah informasi profil akun (Nama Lengkap, Username, Password baru).
   - **Admin Only**:
     - Ubah metadata toko (Nama Toko, Alamat, No Telepon) yang akan dicetak di struk.
     - CRUD Kelola Menu (Tambah, Edit, Hapus menu) dilengkapi pemilihan emoji dinamis.
     - Proteksi hapus menu jika menu tersebut sedang/pernah digunakan dalam transaksi aktif.

---

## Lingkungan Pengembangan (Prasyarat)

- **PHP** >= 8.2
- **Composer** (Dependency Manager)
- **MySQL** >= 8.0
- **Laragon** / **XAMPP** (Web Server Lokal)

---

## Langkah Instalasi & Setup Lokal

1. **Clone Proyek** ke dalam direktori local web server Anda (misal di Laragon `C:\laragon\www\cofeeshop`).
2. **Install dependensi PHP** menggunakan composer:
   ```bash
   composer install
   ```
3. **Setup File `.env`**:
   - Salin file `.env.example` menjadi `.env`.
   - Konfigurasikan koneksi database Anda pada file `.env`:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=cofeeshop
     DB_USERNAME=root
     DB_PASSWORD=
     ```
4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```
5. **Buat Database** di MySQL dengan nama `cofeeshop`.
6. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate --seed
   ```
   *Perintah ini akan membuat semua tabel yang dibutuhkan dan mengisinya dengan data dummy (pengguna, menu default, dan data toko).*
7. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   ```
8. **Akses di Browser**:
   Buka alamat [http://127.0.0.1:8000](http://127.0.0.1:8000) atau [http://localhost:8000](http://localhost:8000) di browser Anda.

---

## Akun Demo Default

Setelah menjalankan perintah seeder, Anda dapat menggunakan kredensial berikut untuk masuk ke sistem:

### 1. Peran: Admin (Akses Penuh)
- **Username**: `admin`
- **Password**: `password123`

### 2. Peran: Kasir (Akses Terbatas)
- **Username**: `kasir`
- **Password**: `password123`

---

## Arsitektur & Kebersihan Kode

- **Controller Tipis (Thin Controllers)**: Logika penyimpanan transaksi terpisah di [TransactionService](app/Services/TransactionService.php) dan keranjang belanja dikelola terpusat di [CartService](app/Services/CartService.php).
- **Atomisitas Database**: Transaksi penjualan diproses di dalam blok database transaction (`DB::transaction()`) guna menghindari inkonsistensi data atau crash data penjualan.
- **Validasi Terpusat**: Seluruh input form divalidasi ketat di sisi server menggunakan *Form Request* kelas tersendiri di dalam direktori `app/Http/Requests`.
- **Global Helper**: Konversi mata uang Rupiah dan penulisan format tanggal Indonesia distandarisasi secara rapi melalui kelas [FormatHelper](app/Helpers/FormatHelper.php).
