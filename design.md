# Design Specification — Sistem Kasir Coffee Shop "Brew & Co."

Dokumen ini merangkum bahasa desain yang diturunkan dari mockup HTML (`coffee_shop_kasir_mockup__1_.html`) dan PRD (`PRD_Kasir_CoffeeShop.docx`), sebagai acuan saat membangun ulang antarmuka menggunakan React + TypeScript + Tailwind + shadcn/ui (web-artifacts-builder).

## 1. Brand & Tema Warna

| Token | Hex | Peran |
|---|---|---|
| `coffee` (primary) | `#8B5E3C` | Sidebar, tombol aksi utama, tab kategori aktif, aksen aktif |
| `coffee-light` (secondary) | `#D2B48C` | Border highlight, avatar kasir, elemen pendukung |
| `coffee-pale` | `#F5EDE3` | Background kartu menu terpilih, kotak kembalian |
| `surface-alt` | `#F5F5F5` | Background kartu, baris tabel alternatif |
| `text-primary` | `#333333` | Semua teks konten utama |
| `text-secondary` | abu-abu sedang | Label, sub-teks, header tabel |
| `text-tertiary` | abu-abu terang | Placeholder, ikon non-aktif |
| `success` (badge Lunas) | `#3B6D11` on `#EAF3DE` | Status transaksi sukses |
| `danger` (hapus) | `#A32D2D` on `#FCEBEB` | Aksi hapus / destruktif |

Pemetaan ke Tailwind/shadcn: definisikan sebagai custom CSS variables di `tailwind.config` (`--coffee`, `--coffee-light`, `--coffee-pale`, dst.) agar konsisten dengan token `--color-background-*`, `--color-text-*`, `--color-border-*`, `--border-radius-md/lg` yang sudah dipakai mockup.

## 2. Tipografi & Skala

- Font: sans-serif sistem (`var(--font-sans)`), tidak menggunakan Inter agar tidak terasa generik — gunakan font sans yang lebih hangat (mis. "Plus Jakarta Sans" atau "Figtree").
- Skala ukuran yang dipakai mockup:
  - 11px — label form, sub-teks, badge, header tabel
  - 12px — teks isi tabel, item pesanan, tombol kecil
  - 13px — judul item, nav, isi umum
  - 14–15px — judul halaman/kartu
  - 20px — angka statistik (ringkasan harian)
- Bobot: 400 untuk teks isi, 500 untuk judul/label aktif/tombol.

## 3. Layout Global

Layout dua-panel, desktop-first, tinggi tetap (kontainer `.app`, tinggi ~580px pada mockup, di artifact bisa `h-screen`/`100dvh`):

```
┌───────────┬───────────────────────────────────────────┐
│           │ Topbar (judul halaman + tanggal)           │
│  Sidebar  ├───────────────────────────────────────────┤
│  (200px)  │ Content area (berganti per halaman)        │
│           │                                             │
└───────────┴───────────────────────────────────────────┘
```

- **Sidebar** (lebar 200px, background `coffee`, teks putih transparan):
  - Logo (ikon kotak + nama "Brew & Co." + sub "Sistem Kasir")
  - Navigasi: Menu Transaksi, Riwayat, Pengaturan (item aktif: background putih transparan + bold)
  - Footer: info kasir (avatar inisial, nama, role) + Logout
- **Topbar**: judul halaman dinamis (kiri) + tanggal hari ini format Indonesia (kanan), border bawah tipis.
- **Content area**: berganti sesuai route/halaman aktif.

## 4. Halaman & Komponen

### 4.1 Menu & Transaksi (`/menu`) — FR-02

Layout konten dibagi 2 kolom:

**Panel Menu (flex-1, scrollable, border kanan)**
- Tab kategori (pill button): Semua, ☕ Coffee, 🍵 Non-Coffee, 🥤 Refreshment, 🍟 Snack — aktif = background `coffee`, teks putih.
- Search box (ikon kaca pembesar + input real-time, background `surface-alt`).
- Grid menu 3 kolom, tiap kartu:
  - Kotak emoji besar (representasi gambar produk)
  - Nama menu (12px, 500)
  - Harga (11px, warna `coffee`, format `Rp 28.000`)
  - Stok tersisa (10px, abu-abu)
  - State `selected`: border `coffee` + background `coffee-pale` (saat ada di keranjang)
  - State `hover`: border `coffee-light`

**Panel Pesanan / Keranjang (lebar fixed 280px)**
- Header: "Pesanan" + nomor transaksi (`TRX-YYYYXXXXX`, auto-generated — FR-02 acceptance criteria)
- Daftar item keranjang (scrollable):
  - Nama + harga satuan
  - Kontrol qty: tombol `−` (bulat), angka, tombol `+` (bulat)
  - Tombol hapus (ikon X)
  - Empty state: ikon cart-off + teks "Belum ada pesanan"
- Footer pesanan:
  - Ringkasan: Subtotal, Pajak (10%), **Total** (grand total, dipisah border atas, bold)
  - Input "Masukkan uang bayar..." (numeric)
  - Baris Kembalian (highlight `coffee-pale` background, teks `coffee`)
  - Tombol utama "Simpan & Cetak Struk" (full width, background `coffee`, ikon check)
  - Validasi: tombol disabled / aksi diabaikan jika keranjang kosong (acceptance criteria FR-02)

### 4.2 Riwayat Transaksi (`/history`) — FR-04

- Header: judul + ringkasan "X transaksi hari ini" + input filter tanggal (kanan atas)
- Tabel riwayat, kolom: ID Transaksi, Kasir, Waktu, Total, Status (badge "Lunas" hijau), Aksi (tombol "Cetak" dengan ikon printer — memicu cetak ulang struk identik, FR-03/FR-04)
- Dua kartu ringkasan di bawah tabel: "Total Pendapatan" dan "Jumlah Transaksi" (angka besar 20px)

### 4.3 Pengaturan (`/setting`) — FR-05

Grid 2 kolom + 1 baris full-width:

- **Kartu "Akun Saya"**: form Nama Lengkap, Username, Password Baru (placeholder "Kosongkan jika tidak diubah" — hanya tersimpan jika diisi), tombol "Simpan perubahan". Validasi: username unik (server-side).
- **Kartu "Informasi Toko"** (khusus role Admin): Nama Toko, Alamat, No. Telepon — data ini tampil di struk (FR-03).
- **Kartu "Kelola Menu"** (full width, khusus Admin):
  - Tombol "Tambah menu" (ikon plus, background `coffee`)
  - Tabel: Nama Menu, Kategori, Harga, Stok, Aksi (Edit / Hapus)
  - Hapus menu memerlukan dialog konfirmasi; menu yang masih dipakai transaksi aktif tidak boleh dihapus.

### 4.4 Struk / Cetak (FR-03 — belum ada di mockup, perlu ditambahkan)

Komponen struk thermal (58mm/80mm), berisi:

| Kolom Kiri | Kolom Kanan |
|---|---|
| Nama Coffee Shop | Nomor Transaksi (TRX-ID) |
| Alamat Toko | Tanggal & Jam |
| No. Telepon Toko | Nama Kasir |
| Daftar Produk + Jumlah | Subtotal per item |
| Pajak (10%) | Total Pembayaran |
| Uang Bayar | Kembalian |

Render via halaman/komponen khusus dengan CSS `@media print`, dipanggil lewat `window.print()`.

### 4.5 Login (`/login`) — FR-01 (belum ada di mockup, perlu ditambahkan)

- Form sederhana: username, password, tombol login, area pesan error spesifik (acceptance criteria: "Login gagal menampilkan pesan error spesifik").
- Tema warna konsisten (aksen `coffee`), logo "Brew & Co." di atas form.

## 5. Pola Interaksi Utama

- Klik kartu menu → tambah ke keranjang & tandai `selected` (FR-02)
- Tombol +/− pada item keranjang → ubah qty; qty 0 atau klik X → hapus item
- Setiap perubahan keranjang → hitung ulang subtotal, pajak (10%), total secara instan (US-03)
- Input "uang bayar" → hitung kembalian instan (US-04), `kembalian = max(0, bayar - total)`
- "Simpan & Cetak Struk" → validasi keranjang tidak kosong → simpan transaksi → buka/print struk → reset keranjang
- Filter kategori & search bekerja bersamaan (AND) secara real-time tanpa reload (US-02)
- Navigasi sidebar mengganti `page-title` topbar dan area konten aktif

## 6. State & Role Awareness

Berdasarkan FR-05 & struktur navigasi:

- **Admin**: akses penuh — Menu Transaksi, Riwayat, Pengaturan Akun, Pengaturan Toko, Kelola Menu.
- **Kasir**: Menu Transaksi, Cetak Struk, Riwayat (lihat saja), Pengaturan Akun saja.

Komponen navigasi & sub-tab Pengaturan harus dirender kondisional berdasarkan `role` user yang login.

## 7. Catatan Adaptasi ke React/Tailwind/shadcn

- Gunakan komponen shadcn: `Card`, `Tabs` (kategori), `Input`, `Badge`, `Button`, `Table`, `Dialog` (konfirmasi hapus menu), `Avatar` (kasir).
- Hindari uniform rounded corner berlebihan & gradient ungu — pertahankan radius kecil-menengah (6–12px) dan palet coklat/krem sesuai brand.
- Semua angka mata uang diformat `Rp` + separator titik (locale `id-ID`), konsisten dengan fungsi `fmt()` di mockup.
- Tanggal topbar memakai format Indonesia: `weekday, d month yyyy` (locale `id-ID`).
