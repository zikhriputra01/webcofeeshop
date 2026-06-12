



PRODUCT REQUIREMENT DOCUMENT


Sistem Informasi Kasir Berbasis Web
Coffee Shop
Nama Proyek	Web Kasir Coffee Shop
Versi Dokumen	v1.0
Teknologi	Laravel 13, PHP, MySQL, CSS Vanilla
Platform	Web Desktop
Status	Draft
Tanggal	Juni 2025






Dikembangkan untuk keperluan Tugas Akhir / Skripsi
 
 
1. Pendahuluan
1.1 Tujuan Dokumen
Dokumen Product Requirement Document (PRD) ini mendefinisikan secara lengkap kebutuhan fungsional dan non-fungsional Sistem Informasi Kasir Berbasis Web untuk Coffee Shop. Dokumen ini menjadi acuan utama bagi tim pengembang, desainer, dan pemangku kepentingan selama siklus pengembangan berlangsung.
1.2 Latar Belakang
Coffee shop sebagai bisnis F&B dengan volume transaksi tinggi membutuhkan sistem pencatatan yang cepat, akurat, dan terorganisir. Proses kasir manual berpotensi menimbulkan kesalahan perhitungan, lambatnya pelayanan, serta sulitnya penelusuran riwayat penjualan. Sistem ini dirancang untuk menjawab kebutuhan tersebut melalui aplikasi web berbasis desktop.
1.3 Ruang Lingkup
Sistem mencakup modul-modul berikut:
•	Autentikasi pengguna (Login / Logout)
•	Transaksi penjualan real-time dengan keranjang digital
•	Pencetakan struk transaksi
•	Riwayat dan penelusuran transaksi
•	Pengaturan akun, toko, dan menu
1.4 Definisi & Singkatan
Istilah	Definisi
PRD	Product Requirement Document — dokumen spesifikasi kebutuhan produk
Kasir	Pengguna dengan hak akses melakukan transaksi penjualan
Admin	Pengguna dengan hak akses penuh termasuk pengelolaan menu dan pengaturan
Keranjang	Daftar item yang dipilih sebelum transaksi dikonfirmasi
Struk	Bukti transaksi berisi rincian pembelian pelanggan
TRX	Kode unik transaksi yang dibuat otomatis oleh sistem
SKU	Stock Keeping Unit — kode unik setiap item menu

 
2. Gambaran Produk
2.1 Visi Produk
Visi	Menjadi sistem kasir digital yang membantu operasional coffee shop menjadi lebih cepat, akurat, dan terorganisir melalui antarmuka web yang intuitif dan modern.

2.2 Target Pengguna
Peran	Deskripsi	Hak Akses Utama
Admin	Pemilik atau manajer coffee shop	Semua fitur: transaksi, history, setting, kelola menu
Kasir	Pegawai yang bertugas di meja kasir	Transaksi, cetak struk, lihat history, setting akun

2.3 Konsep Antarmuka
Antarmuka dirancang dengan pendekatan desktop-first menggunakan layout dua-panel: sidebar navigasi di kiri dan area konten di kanan. Tema warna mengikuti identitas coffee shop:
Elemen	Kode Warna	Peran
Primer	#8B5E3C	Sidebar, tombol aksi utama, aksen aktif
Sekunder	#D2B48C	Border highlight, elemen pendukung
Pale Brown	#F5EDE3	Background kembalian, tabel bergaris
Abu-abu	#F5F5F5	Background kartu, baris tabel alternatif
Teks Utama	#333333	Semua teks konten

 
3. Kebutuhan Fungsional
FR-01 — Autentikasi Pengguna (Login)
DESKRIPSI
Pengguna harus memasukkan kredensial yang valid untuk mengakses sistem. Sesi disimpan hingga pengguna melakukan logout secara eksplisit.
ALUR UTAMA
•	Pengguna membuka halaman login.
•	Memasukkan username dan password.
•	Sistem memvalidasi terhadap database (tabel users).
•	Jika valid: sistem menyimpan session dan mengarahkan ke halaman Menu Transaksi.
•	Jika tidak valid: sistem menampilkan pesan kesalahan tanpa mengarahkan pengguna.
ACCEPTANCE CRITERIA
◦	Login gagal menampilkan pesan error spesifik.
◦	Halaman selain login tidak dapat diakses tanpa sesi aktif.
◦	Password disimpan dalam format terenkripsi (bcrypt).

FR-02 — Menu & Transaksi
DESKRIPSI
Halaman utama kasir untuk memilih menu, mengelola keranjang, menghitung total, dan memproses pembayaran. Layout dua kolom: grid menu di kiri dan panel pesanan di kanan.
SUB-FITUR
•	Tampilan Grid Menu: kartu menu dengan emoji, nama, harga, dan stok tersisa.
◦	Filter kategori: Semua, Coffee, Non-Coffee, Refreshment, Snack.
◦	Kotak pencarian real-time berdasarkan nama menu.
◦	Kartu menu aktif ditandai dengan border coklat dan background pale brown.
•	Keranjang Digital: daftar item yang ditambahkan kasir.
◦	Tambah item: klik kartu menu.
◦	Ubah jumlah: tombol + / − di samping item.
◦	Hapus item: tombol X pada baris item.
•	Kalkulasi Otomatis: subtotal, pajak 10%, dan grand total diperbarui instan.
•	Input Uang Bayar: kasir memasukkan nominal, kembalian dihitung otomatis.
•	Simpan Transaksi: menyimpan ke database dan memicu cetak struk.
ACCEPTANCE CRITERIA
◦	Total selalu sinkron dengan isi keranjang tanpa perlu reload halaman.
◦	Tidak bisa menyimpan transaksi jika keranjang kosong.
◦	Nomor transaksi dibuat otomatis dengan format TRX-YYYYXXXXX.

FR-03 — Cetak Struk (Print Bill)
DESKRIPSI
Sistem menghasilkan struk transaksi yang dapat dicetak ke printer thermal atau disimpan sebagai PDF.
INFORMASI YANG DITAMPILKAN DI STRUK
Kolom Kiri	Kolom Kanan
Nama Coffee Shop	Nomor Transaksi (TRX-ID)
Alamat Toko	Tanggal & Jam Transaksi
Nomor Telepon Toko	Nama Kasir
Daftar Produk + Jumlah	Subtotal per Item
Pajak (10%)	Total Pembayaran
Uang Bayar	Kembalian

ACCEPTANCE CRITERIA
◦	Struk dapat dicetak ulang dari halaman Riwayat.
◦	Struk menyesuaikan lebar kertas thermal 58mm / 80mm.
◦	Output PDF opsional menggunakan fitur print browser.

 
FR-04 — Riwayat Transaksi
DESKRIPSI
Halaman untuk menelusuri, memfilter, dan melihat detail seluruh transaksi yang pernah dilakukan.
FITUR TABEL RIWAYAT
•	Kolom: ID Transaksi, Nama Kasir, Waktu, Total Pembayaran, Status, Aksi.
•	Filter berdasarkan rentang tanggal.
•	Tombol Cetak Ulang pada setiap baris transaksi.
•	Ringkasan harian: total pendapatan dan jumlah transaksi ditampilkan di bawah tabel.
ACCEPTANCE CRITERIA
◦	Data tabel diambil dari database secara real-time.
◦	Filter tanggal tidak menampilkan data di luar rentang yang dipilih.
◦	Cetak ulang menghasilkan struk identik dengan transaksi aslinya.

FR-05 — Pengaturan Sistem
DESKRIPSI
Halaman tiga-panel untuk mengelola akun pengguna, informasi toko, dan daftar menu. Khusus untuk pengguna dengan role Admin atau Kasir yang memiliki akses ke sub-menu masing-masing.
SUB-MODUL PENGATURAN
•	Pengaturan Akun: ubah nama, username, dan password.
◦	Validasi: username tidak boleh duplikat.
◦	Password baru hanya tersimpan jika field diisi.
•	Informasi Toko: ubah nama toko, alamat, dan nomor telepon.
◦	Data toko ditampilkan pada struk transaksi.
•	Kelola Menu: tambah, edit, hapus item menu.
◦	Field: nama menu, kategori (enum), harga, stok.
◦	Hapus menu meminta konfirmasi sebelum eksekusi.
◦	Menu tidak dapat dihapus jika masih ada di transaksi aktif.
ACCEPTANCE CRITERIA
◦	Perubahan tersimpan setelah klik tombol Simpan, bukan real-time.
◦	Validasi sisi server untuk semua input form.

FR-06 — Logout
Menghapus sesi pengguna dan mengarahkan kembali ke halaman login. Setelah logout, tombol back browser tidak dapat mengakses halaman yang dilindungi.

 
4. Kebutuhan Non-Fungsional
No	Aspek	Kebutuhan	Target Metrik
1	Performa	Respon halaman transaksi cepat	< 2 detik waktu muat
2	Keamanan	Proteksi endpoint dengan middleware auth	Semua route dilindungi
3	Keamanan	Password terenkripsi di database	Bcrypt hash rounds >= 10
4	Usability	Antarmuka intuitif untuk kasir non-teknis	Pelatihan < 30 menit
5	Ketersediaan	Sistem berjalan stabil selama jam operasional	Uptime >= 99%
6	Kompatibilitas	Berjalan di browser modern	Chrome, Firefox, Edge
7	Skalabilitas	Mampu menangani ratusan transaksi/hari	> 500 transaksi/hari
8	Maintainability	Kode terstruktur mengikuti konvensi Laravel	PSR-12, MVC pattern

 
5. Desain Database
5.1 Tabel: users
Kolom	Tipe Data	Constraint	Nullable	Keterangan
id	BIGINT UNSIGNED	PK, AI	No	Primary key auto-increment
nama	VARCHAR(100)	-	No	Nama lengkap pengguna
username	VARCHAR(50)	UNIQUE	No	Username untuk login
password	VARCHAR(255)	-	No	Bcrypt hash
role	ENUM	-	No	admin | kasir
created_at	TIMESTAMP	-	Yes	Dibuat otomatis Laravel
updated_at	TIMESTAMP	-	Yes	Diperbarui otomatis Laravel

5.2 Tabel: menus
Kolom	Tipe Data	Constraint	Nullable	Keterangan
id	BIGINT UNSIGNED	PK, AI	No	Primary key
nama_menu	VARCHAR(100)	-	No	Nama item menu
kategori	ENUM	-	No	coffee | noncoffee | refreshment | snack
harga	DECIMAL(10,2)	-	No	Harga satuan dalam rupiah
stok	INT	DEFAULT 0	No	Jumlah stok tersedia
created_at	TIMESTAMP	-	Yes	Dibuat otomatis Laravel
updated_at	TIMESTAMP	-	Yes	Diperbarui otomatis Laravel

5.3 Tabel: transactions
Kolom	Tipe Data	Constraint	Nullable	Keterangan
id	BIGINT UNSIGNED	PK, AI	No	Primary key
user_id	BIGINT UNSIGNED	FK -> users.id	No	Kasir yang memproses
total_harga	DECIMAL(12,2)	-	No	Total setelah pajak
uang_bayar	DECIMAL(12,2)	-	No	Nominal uang pelanggan
kembalian	DECIMAL(12,2)	-	No	Uang kembalian
tanggal	TIMESTAMP	DEFAULT NOW()	No	Waktu transaksi

5.4 Tabel: transaction_details
Kolom	Tipe Data	Constraint	Nullable	Keterangan
id	BIGINT UNSIGNED	PK, AI	No	Primary key
transaction_id	BIGINT UNSIGNED	FK -> transactions.id	No	Referensi transaksi induk
menu_id	BIGINT UNSIGNED	FK -> menus.id	No	Item menu yang dibeli
jumlah	INT	-	No	Kuantitas item
harga	DECIMAL(10,2)	-	No	Harga saat transaksi
subtotal	DECIMAL(12,2)	-	No	jumlah x harga

 
6. Struktur Navigasi
Sistem menggunakan sidebar tetap dengan empat item navigasi utama. Akses setiap halaman tergantung pada role pengguna.
Halaman	URL Route	Akses	Komponen Utama
Login	/login	Public	Form login, validasi
Menu Transaksi	/menu	Admin, Kasir	Grid menu, keranjang, kalkulasi
Riwayat	/history	Admin, Kasir	Tabel riwayat, filter tanggal
Pengaturan Akun	/setting/akun	Admin, Kasir	Form ubah profil
Pengaturan Toko	/setting/toko	Admin	Form informasi toko
Kelola Menu	/setting/menu	Admin	CRUD tabel menu
Logout	/logout	Admin, Kasir	Hapus session, redirect

 
7. User Stories
ID	Aktor	User Story	Acceptance Criteria
US-01	Kasir	Sebagai kasir, saya ingin login agar sistem saya hanya bisa diakses oleh saya.	Login berhasil dengan kredensial valid; gagal dengan pesan jelas jika salah.
US-02	Kasir	Sebagai kasir, saya ingin mencari menu dengan cepat agar transaksi tidak lambat.	Pencarian real-time muncul tanpa reload halaman.
US-03	Kasir	Sebagai kasir, saya ingin keranjang otomatis menghitung total agar saya tidak salah hitung.	Total berubah instan saat item ditambah atau dikurangi.
US-04	Kasir	Sebagai kasir, saya ingin sistem menghitung kembalian agar saya tidak salah memberi uang kembali.	Kembalian dihitung otomatis saat uang bayar dimasukkan.
US-05	Kasir	Sebagai kasir, saya ingin mencetak struk agar pelanggan mendapat bukti pembayaran.	Struk tercetak dengan semua informasi transaksi.
US-06	Kasir	Sebagai kasir, saya ingin melihat riwayat agar saya bisa menelusuri transaksi lama.	Tabel riwayat tampil dengan filter tanggal berfungsi.
US-07	Admin	Sebagai admin, saya ingin menambah menu agar pilihan produk selalu terkini.	Menu baru muncul di grid transaksi setelah disimpan.
US-08	Admin	Sebagai admin, saya ingin mengubah informasi toko agar struk selalu akurat.	Perubahan nama toko muncul pada struk transaksi berikutnya.

 
8. Prioritas & Fase Pengembangan
8.1 MoSCoW Prioritization
Prioritas	Fitur	FR ID	Status
Must Have	Login & autentikasi pengguna	FR-01	Must
Must Have	Menu & transaksi dengan keranjang digital	FR-02	Must
Must Have	Cetak struk transaksi	FR-03	Must
Should Have	Riwayat transaksi & filter tanggal	FR-04	Should
Should Have	Pengaturan akun & toko	FR-05	Should
Could Have	Export struk ke PDF	FR-03	Could
Could Have	Laporan pendapatan per periode	-	Could
Won't Have	Integrasi payment gateway	-	Won't

8.2 Fase Pengembangan
Fase	Nama	Deliverable	Estimasi
1	Foundation	Setup Laravel 13, migrasi database, seeder data dummy, autentikasi	1 minggu
2	Core Transaction	Halaman menu, keranjang, kalkulasi, simpan transaksi	2 minggu
3	Print & History	Cetak struk, halaman riwayat, filter tanggal	1 minggu
4	Settings & Polish	CRUD menu, setting akun & toko, UI refinement	1 minggu
5	Testing & Deploy	Unit test, UAT, bug fix, deployment ke server	1 minggu

 
9. Stack Teknologi
Lapisan	Teknologi	Keterangan
Backend Framework	Laravel 13	PHP framework dengan Eloquent ORM, routing, middleware auth
Bahasa Server	PHP 8.2+	Versi minimum yang mendukung Laravel 13
Database	MySQL 8.x	Relational database untuk penyimpanan data transaksi
Frontend	Blade + CSS Vanilla	Template engine Laravel tanpa framework CSS tambahan
Autentikasi	Laravel Auth / Sanctum	Session-based authentication untuk multi-role
Cetak Struk	Browser Print API	window.print() dengan CSS @media print khusus
Server Dev	XAMPP / Laragon	Lingkungan lokal untuk pengembangan

 
10. Risiko & Mitigasi
No	Risiko	Dampak	Likelihood	Mitigasi
1	Sesi login expired saat transaksi sedang berlangsung	Tinggi	Rendah	Perpanjang session lifetime & simpan draft keranjang ke localStorage sementara
2	Data menu tidak sinkron dengan stok aktual	Sedang	Sedang	Tambahkan pengurangan stok otomatis saat transaksi disimpan
3	Printer thermal tidak kompatibel dengan browser	Sedang	Sedang	Sediakan preview cetak & opsi ukuran kertas 58mm/80mm
4	Akses tidak sah ke halaman admin oleh kasir	Tinggi	Rendah	Middleware role-based menggunakan Gate atau Policy Laravel
5	Database corrupt akibat transaksi parsial	Tinggi	Rendah	Gunakan database transaction (DB::transaction) untuk atomicity


Dokumen ini bersifat living document dan akan diperbarui seiring perkembangan proyek.
