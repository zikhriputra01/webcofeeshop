# PROMPT PENGEMBANGAN — Sistem Informasi Kasir Coffee Shop "Brew & Co."
**Stack: PHP, Laravel, MySQL, Blade + CSS Vanilla**

Gunakan prompt ini sebagai instruksi tunggal untuk AI coding assistant (Blackbox CLI / Claude Code / dsb.) agar mengembangkan aplikasi dari nol sampai selesai, mengacu pada empat dokumen acuan: `PRD.md`, `design.md`, `code.md`, dan mockup HTML `coffee_shop_kasir_mockup.html`.

---

## 1. KONTEKS PROYEK

Bangun **Sistem Informasi Kasir Berbasis Web** untuk Coffee Shop "Brew & Co." menggunakan **Laravel 11.x**, **MySQL**, **Blade templating**, dan **CSS Vanilla** (tanpa Tailwind/Bootstrap). Proyek ini untuk keperluan Tugas Akhir/Skripsi, sehingga kode harus rapi, mengikuti konvensi MVC Laravel, PSR-12, dan mudah dijelaskan saat sidang.

Referensi wajib dibaca sebelum mulai coding:
- `PRD.md` — kebutuhan fungsional (FR-01 s/d FR-06), non-fungsional, skema database, user stories, prioritas MoSCoW.
- `design.md` — palet warna, tipografi, layout dua-panel, breakdown komponen per halaman, pola interaksi.
- `code.md` — prinsip clean code (akan diadaptasi ke konteks Laravel di bagian §8 prompt ini).
- `coffee_shop_kasir_mockup.html` — referensi visual HTML/CSS yang HARUS dipertahankan tampilannya (kelas CSS, struktur layout, palet warna `--coffee`, `--coffee-light`, `--coffee-pale`).

**Aturan utama:** Tampilan akhir harus identik secara visual dengan mockup (warna, spacing, layout sidebar 200px, grid menu 3 kolom, panel pesanan 280px, dsb.), tapi diimplementasikan sebagai Blade view dinamis berbasis data dari database, bukan data hardcoded di JS.

---

## 2. STACK & SETUP AWAL

- Laravel 11.x, PHP 8.2+, MySQL 8.x
- Autentikasi: Laravel built-in Auth (session-based, sesuai FR-01), middleware `auth` + middleware role custom (`admin`, `kasir`)
- Frontend: Blade + CSS Vanilla — **konversi semua `<style>` di mockup menjadi file `public/css/app.css`** (atau dipecah per halaman: `app.css`, `transaksi.css`, `history.css`, `setting.css`), gunakan CSS variables (`:root { --coffee: #8B5E3C; ... }`) sesuai `design.md` §1.
- JS: Vanilla JS (boleh modular per file di `public/js/`), gunakan `fetch()` ke route Laravel untuk operasi AJAX (tambah ke keranjang, hitung total, simpan transaksi) — **tidak menggunakan framework JS**.
- Gunakan `php artisan make:migration`, `make:model`, `make:controller`, `make:middleware`, `make:request` sesuai kebutuhan — JANGAN tulis SQL manual di controller.

---

## 3. DATABASE — IKUTI SKEMA PRD §5 PERSIS

Buat migration untuk 4 tabel berikut (kolom, tipe, constraint sesuai PRD):

### `users`
- `id` BIGINT UNSIGNED PK AI
- `nama` VARCHAR(100)
- `username` VARCHAR(50) UNIQUE
- `password` VARCHAR(255) — hashed bcrypt (rounds >= 10)
- `role` ENUM('admin','kasir')
- timestamps

### `menus`
- `id` BIGINT UNSIGNED PK AI
- `nama_menu` VARCHAR(100)
- `kategori` ENUM('coffee','noncoffee','refreshment','snack')
- `harga` DECIMAL(10,2)
- `stok` INT DEFAULT 0
- timestamps

### `transactions`
- `id` BIGINT UNSIGNED PK AI
- `trx_id` VARCHAR(20) UNIQUE — format `TRX-YYYYXXXXX` (lihat §6)
- `user_id` BIGINT UNSIGNED FK -> users.id
- `total_harga` DECIMAL(12,2)
- `uang_bayar` DECIMAL(12,2)
- `kembalian` DECIMAL(12,2)
- `tanggal` TIMESTAMP DEFAULT NOW()

### `transaction_details`
- `id` BIGINT UNSIGNED PK AI
- `transaction_id` BIGINT UNSIGNED FK -> transactions.id
- `menu_id` BIGINT UNSIGNED FK -> menus.id
- `jumlah` INT
- `harga` DECIMAL(10,2) — harga saat transaksi (snapshot, bukan ambil dari `menus` agar histori akurat)
- `subtotal` DECIMAL(12,2)

Tambahkan **Seeder**:
- Seeder `UserSeeder` — minimal 1 admin + 1 kasir (password hashed)
- Seeder `MenuSeeder` — isi 14 item menu sesuai data dummy di mockup (Americano, Cappuccino, Latte, dst., dengan kategori, harga, stok, dan emoji disimpan di kolom tambahan `icon` VARCHAR jika diperlukan untuk representasi visual)

Buat **Eloquent Model** untuk masing-masing tabel beserta relasi:
- `User hasMany Transaction`
- `Transaction belongsTo User`, `hasMany TransactionDetail`
- `TransactionDetail belongsTo Transaction`, `belongsTo Menu`
- `Menu hasMany TransactionDetail`

---

## 4. ROUTING — SESUAI TABEL STRUKTUR NAVIGASI PRD §6

| Halaman | Route | Method | Middleware |
|---|---|---|---|
| Login | `/login` | GET, POST | guest |
| Logout | `/logout` | POST | auth |
| Menu Transaksi | `/menu` | GET | auth |
| Simpan Transaksi | `/transaksi` | POST | auth |
| Riwayat | `/history` | GET | auth |
| Cetak/Cetak Ulang Struk | `/history/{trx_id}/print` | GET | auth |
| Pengaturan Akun | `/setting/akun` | GET, PUT | auth |
| Pengaturan Toko | `/setting/toko` | GET, PUT | auth + role:admin |
| Kelola Menu | `/setting/menu` | GET, POST, PUT, DELETE | auth + role:admin |

Buat middleware `EnsureUserHasRole` (atau gunakan Gate/Policy) untuk membatasi akses Admin vs Kasir sesuai tabel role di PRD §2.2:
- **Admin**: semua fitur (transaksi, history, setting akun, setting toko, kelola menu)
- **Kasir**: transaksi, cetak struk, lihat history, setting akun saja

Setelah logout, pastikan halaman terlindungi tidak bisa diakses lewat tombol back browser (gunakan header cache-control `no-store` pada middleware atau session invalidation — FR-06).

---

## 5. HALAMAN & FITUR — IMPLEMENTASI PER FR

### 5.1 Login (`/login`) — FR-01
- Form: username, password
- Validasi server-side via `FormRequest` (`LoginRequest`)
- Jika gagal → tampilkan pesan error spesifik di Blade (gunakan `@error` directive), JANGAN redirect ke halaman lain
- Jika berhasil → simpan session, redirect ke `/menu`
- Style: form sederhana bercabang dari palet `coffee`, logo "Brew & Co." (lihat `design.md` §4.5)

### 5.2 Menu & Transaksi (`/menu`) — FR-02
Bangun view Blade `menu/index.blade.php` mereplikasi struktur mockup (`page-transaksi`), tapi data menu di-render dari `Menu::all()` (server-side, bukan array JS hardcoded).

**Panel Menu (kiri):**
- Tab kategori: Semua, ☕ Coffee, 🍵 Non-Coffee, 🥤 Refreshment, 🍟 Snack — filter via JS (client-side filter terhadap data yang sudah di-render, ATAU AJAX ke endpoint `/menu/filter?kategori=...&q=...`)
- Search box real-time — gunakan `input` event + `fetch()` debounce ke endpoint filter (server-side query agar konsisten dengan stok terbaru)
- Grid 3 kolom kartu menu: emoji/icon, nama, harga (`Rp` format Indonesia), stok
- Kartu yang sudah ada di keranjang → tambahkan class `selected` (border `coffee` + bg `coffee-pale`)

**Panel Pesanan (kanan, 280px):**
- Keranjang dikelola di **session Laravel** (bukan localStorage/array global JS) — `session('cart')` sebagai array `[menu_id => jumlah]`, agar konsisten antar request dan tahan refresh
- Endpoint AJAX:
  - `POST /cart/add` — tambah item (body: `menu_id`)
  - `POST /cart/update` — ubah qty (body: `menu_id`, `delta`)
  - `POST /cart/remove` — hapus item (body: `menu_id`)
  - Setiap endpoint return JSON: daftar item keranjang + subtotal/pajak/total terkini, JS mengupdate DOM tanpa reload
- Kalkulasi: `subtotal = Σ(harga × jumlah)`, `pajak = subtotal × 10%` (round), `total = subtotal + pajak` — hitung di **backend** (Controller/Service), JS hanya menampilkan hasil
- Input "uang bayar" → JS hitung kembalian client-side untuk responsivitas (`kembalian = max(0, bayar - total)`), tapi validasi ulang di server saat simpan
- Tombol "Simpan & Cetak Struk" → `POST /transaksi`:
  - **Validasi**: tolak jika keranjang kosong (return error, jangan proses)
  - **Validasi**: tolak jika `uang_bayar < total`
  - Generate `trx_id` otomatis (lihat §6)
  - Simpan ke `transactions` + `transaction_details` dalam **`DB::transaction()`** (atomicity — mitigasi risiko #5 di PRD)
  - Kurangi `stok` menu sesuai jumlah terjual (mitigasi risiko #2 di PRD)
  - Kosongkan session cart
  - Redirect/response ke halaman cetak struk (`/history/{trx_id}/print`) — bisa dibuka di tab baru via JS

### 5.3 Cetak Struk (FR-03)
- View `transaksi/struk.blade.php` (atau `history/print.blade.php`) — layout 2 kolom sesuai `design.md` §4.4 (Nama Toko/Alamat/Telepon vs No Transaksi/Tanggal/Kasir, lalu daftar item, pajak, total, uang bayar, kembalian)
- Data toko (`nama_toko`, `alamat`, `telepon`) diambil dari tabel `settings` (lihat §5.5) atau config — TIDAK hardcode
- Gunakan CSS `@media print` khusus dengan `@page { size: 58mm auto; }` atau `80mm` (sediakan toggle ukuran kertas — opsional/Could Have)
- Trigger cetak via `window.print()` saat halaman dimuat atau via tombol "Cetak"
- Halaman ini harus bisa diakses ulang dari Riwayat dengan data identik (FR-04 AC)

### 5.4 Riwayat Transaksi (`/history`) — FR-04
- Tabel: ID Transaksi, Nama Kasir (`transaction.user.nama`), Waktu, Total, Status (badge "Lunas"), Aksi (tombol Cetak → link ke `/history/{trx_id}/print`)
- Filter rentang tanggal: form `GET /history?from=...&to=...`, query Eloquent dengan `whereBetween('tanggal', [...])`
- Ringkasan harian: total pendapatan (`SUM(total_harga)`) dan jumlah transaksi untuk tanggal yang difilter (default: hari ini) — hitung via query agregat di Controller, JANGAN di Blade/JS
- Data **selalu real-time dari database** (tidak ada cache statis)

### 5.5 Pengaturan (`/setting/*`) — FR-05
Buat tabel tambahan `settings` (key-value atau single row) untuk informasi toko: `nama_toko`, `alamat`, `telepon`. Tambahkan migration & model `Setting`.

**Pengaturan Akun** (`/setting/akun`, semua role):
- Form: nama, username, password baru (opsional)
- Validasi: username unik kecuali milik user sendiri (`unique:users,username,EXCEPT_CURRENT_ID`)
- Password baru hanya di-update jika field diisi (cek `$request->filled('password')`)
- Simpan via tombol "Simpan perubahan" (bukan auto-save)

**Pengaturan Toko** (`/setting/toko`, admin only):
- Form: nama toko, alamat, no telepon → update tabel `settings`
- Data ini dipakai di struk (§5.3)

**Kelola Menu** (`/setting/menu`, admin only):
- Tabel CRUD: Nama Menu, Kategori, Harga, Stok, Aksi (Edit/Hapus)
- Tombol "Tambah menu" → modal/form (bisa modal CSS vanilla sederhana atau halaman terpisah)
- Hapus menu: konfirmasi via `confirm()` JS atau modal konfirmasi → cek dulu apakah menu masih punya `transaction_details` aktif (definisikan "aktif" — misal transaksi hari ini) sebelum mengizinkan hapus; jika ya, tolak dengan pesan error
- Validasi server-side semua field (FormRequest)

### 5.6 Logout — FR-06
- `POST /logout` → `Auth::logout()`, invalidate session, regenerate CSRF token, redirect ke `/login`

---

## 6. ATURAN PENOMORAN TRANSAKSI

Format `TRX-YYYYXXXXX` (4 digit tahun + 5 digit sequence, contoh `TRX-20250001`). Implementasi disarankan:
- Hitung jumlah transaksi tahun berjalan + 1, pad dengan leading zero 5 digit
- Lakukan dalam `DB::transaction()` dengan lock (`lockForUpdate()`) untuk hindari duplikasi nomor saat concurrent request

---

## 7. KONSISTENSI VISUAL DENGAN MOCKUP

- Salin seluruh CSS dari `<style>` mockup ke `public/css/app.css`, pertahankan semua class (`.app`, `.sidebar`, `.menu-grid`, `.menu-card`, `.order-panel`, `.hist-table`, `.settings-grid`, dst.) dan CSS variables (`--coffee`, `--coffee-light`, `--coffee-pale`, `--sidebar-w`, dll.)
- Buat `layouts/app.blade.php` sebagai master layout berisi struktur `.app > .sidebar + .main(.topbar + .content)`, dengan `@yield('content')` untuk area konten per halaman
- Sidebar nav item `active` ditentukan dari route aktif (`request()->routeIs(...)`), bukan `onclick` JS seperti mockup
- Topbar: judul halaman via `@yield('title')`, tanggal hari ini di-render server-side (`now()->translatedFormat('l, d F Y')` dengan locale `id`)
- Gunakan ikon dari library yang sama dengan mockup (Tabler Icons `ti ti-*`) via CDN

---

## 8. PRINSIP CLEAN CODE (ADAPTASI `code.md` UNTUK LARAVEL)

- **Controller tipis**: Controller hanya menangani request/response & validasi; logic bisnis (kalkulasi total, generate trx_id, cek stok) dipindah ke **Service class** (`app/Services/CartService.php`, `app/Services/TransactionService.php`, `app/Services/ReceiptService.php`)
- **FormRequest** untuk semua validasi input (`LoginRequest`, `MenuRequest`, `TransactionRequest`, `AccountSettingRequest`, `StoreSettingRequest`)
- **Eloquent Resource/ViewModel** (opsional) untuk format data ke Blade agar Blade tidak berisi logic kompleks (`$transaction->trx_id`, format `Rp` lewat Helper/Accessor)
- **Helper terpusat** untuk format mata uang & tanggal, mis. `app/Helpers/FormatHelper.php` dengan fungsi `rupiah($n)` dan `tanggalIndonesia($date)` — daftarkan sebagai global helper via `composer.json autoload.files` atau gunakan Blade directive custom (`@rupiah(...)`)
- **Konstanta**: `PAJAK_RATE = 0.1` didefinisikan satu tempat (`config/pos.php` atau konstanta di `TransactionService`), jangan hardcode di banyak file
- **Naming**: gunakan istilah domain Bahasa Indonesia untuk kolom/variabel sesuai PRD (`nama_menu`, `harga`, `stok`, `uang_bayar`, `kembalian`), tapi nama class/method tetap konvensi Laravel (PascalCase untuk class, camelCase untuk method)
- **Tidak ada query mentah/manipulasi DOM berlebihan** — gunakan Eloquent query builder, dan JS vanilla hanya untuk update fragmen DOM hasil response AJAX (hindari `innerHTML` besar; gunakan template fragment kecil per item)
- **DB::transaction()** wajib untuk: simpan transaksi + detail + update stok (atomicity, mitigasi risiko #5 PRD)
- **Middleware role** terpusat, jangan cek `if ($user->role === 'admin')` berulang di banyak controller — gunakan `Gate::define` / Policy

---

## 9. NON-FUNGSIONAL (PRD §4) — PASTIKAN TERPENUHI

- Semua route (kecuali `/login`) dilindungi middleware `auth`
- Password di-hash bcrypt rounds >= 10 (default Laravel sudah cukup, pastikan tidak diturunkan)
- Validasi server-side di SETIAP form (jangan andalkan validasi JS saja)
- Halaman `/menu` harus responsif < 2 detik — hindari N+1 query (gunakan eager loading `with('user','details.menu')` di Riwayat)
- Kode mengikuti PSR-12 (jalankan `./vendor/bin/pint` jika tersedia)

---

## 10. URUTAN PENGERJAAN (MENGIKUTI FASE PRD §8.2)

1. **Foundation**: setup project Laravel, migration + model 5 tabel (termasuk `settings`), seeder user & menu, scaffolding Auth (login/logout), layout master + CSS dari mockup
2. **Core Transaction**: halaman `/menu`, session cart, AJAX add/update/remove, kalkulasi subtotal/pajak/total/kembalian, simpan transaksi (`DB::transaction`, generate trx_id, kurangi stok)
3. **Print & History**: view struk + `@media print`, halaman `/history` dengan filter tanggal + ringkasan harian + cetak ulang
4. **Settings & Polish**: CRUD menu (kelola menu admin), setting akun, setting toko, perbaikan UI agar 1:1 dengan mockup
5. **Testing & Deploy**: feature test untuk login, transaksi (termasuk edge case keranjang kosong & uang kurang), kelola menu (cegah hapus menu aktif); siapkan `.env.example` dan instruksi setup lokal (XAMPP/Laragon)

---

## 11. DELIVERABLE YANG DIHARAPKAN

- Source code Laravel lengkap (migration, seeder, model, controller, service, FormRequest, middleware, routes, Blade views, CSS, JS)
- File `README.md` berisi instruksi instalasi (`composer install`, `.env`, `php artisan migrate --seed`, `php artisan serve`)
- Tampilan akhir setiap halaman (`/login`, `/menu`, `/history`, `/setting/*`, struk cetak) secara visual sesuai mockup dan `design.md`
- Seluruh FR-01 s/d FR-06 beserta acceptance criteria di PRD terpenuhi dan dapat diverifikasi
