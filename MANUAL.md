# Manual Book — Sistem Maika

Dokumen ini menjelaskan cara menggunakan modul-modul yang sudah tersedia pada sistem Maika (Laravel + SB Admin Bootstrap 5).

## 1. Gambaran Umum

Sistem Maika membantu pencatatan operasional harian dan keuangan untuk penjualan konsinyasi ke mitra (titip jual), meliputi:
- Master data (mitra, produk, harga bulanan, kalender operasional).
- Transaksi harian (produksi, pengiriman, laporan penjualan).
- Keuangan (biaya harian, pembayaran mitra).
- Rekap (ringkasan owner dan per mitra).

### 1.1. Struktur Alur Data (ringkas)

Urutan input yang paling aman agar hasil rekap konsisten:
1) Set **Kalender Operasional** (wajib)
2) Input **Mitra** dan **Produk**
3) Set **Harga Bulanan** (untuk dipakai saat pengiriman/laporan bila Anda menginput harga)
4) Harian:
   - Input **Produksi Harian** (stok siap jual)
   - Input **Pengiriman Mitra** (jumlah titip + harga)
   - Input **Laporan Penjualan Mitra** (hasil titip/jual)
   - Input **Pembayaran Mitra** (jika ada pembayaran)
   - Input **Biaya Harian** (operasional)
5) Pantau **Rekap**

## 2. Akses & Navigasi

### 2.1. Halaman Admin

Menu admin tersedia di URL:
- Dashboard: `/admin`
- Master Data:
  - Mitra: `/admin/mitra`
  - Produk: `/admin/produk`
  - Harga Bulanan: `/admin/harga`
  - Kalender Operasional: `/admin/kalender`
- Transaksi:
  - Produksi Harian: `/admin/produksi`
  - Pengiriman Mitra: `/admin/pengiriman`
  - Laporan Penjualan Mitra: `/admin/laporan`
- Keuangan:
  - Biaya Harian: `/admin/biaya`
  - Pembayaran Mitra: `/admin/pembayaran`
- Rekap:
  - Rekap & Laporan: `/admin/rekap`

Catatan: Pada versi saat ini, route admin belum diproteksi login/middleware. Jika nanti ditambahkan login, menu dan akses akan mengikuti hak akses user/role.

### 2.2. Tema Light/Dark

Di topbar terdapat tombol tema. Saat diklik, tema berubah Light/Dark dan disimpan di browser (localStorage) sehingga tetap konsisten ketika halaman di-refresh.

## 3. Aturan Sistem Penting

### 3.1. Gate Kalender Operasional (blok hari libur)

Semua transaksi harian (Produksi, Pengiriman, Laporan, Biaya, Pembayaran) mengikuti aturan:
- Jika tanggal berstatus **libur** di Kalender Operasional, transaksi **ditolak**.
- Jika tanggal belum terdaftar di Kalender Operasional, transaksi **ditolak**.

Pastikan Kalender Operasional sudah terisi untuk tanggal-tanggal yang akan dipakai.

### 3.2. Aturan Unik (mencegah duplikasi)

- Produksi Harian: 1 tanggal hanya boleh 1 header produksi.
- Laporan Penjualan Mitra: 1 mitra + 1 tanggal hanya boleh 1 header laporan.
- Pembayaran Mitra: 1 laporan penjualan hanya boleh punya 1 pembayaran.

## 4. Master Data (Fase 1)

### 4.1. Mitra

Menu: **Master Data → Mitra**

Fungsi:
- Menambah/mengubah/menghapus mitra.
- Menandai mitra aktif/nonaktif.

Field utama:
- Kode Mitra (unik)
- Nama
- Telepon (opsional)
- Alamat (opsional)
- Status aktif

### 4.2. Produk

Menu: **Master Data → Produk**

Fungsi:
- Menambah/mengubah/menghapus produk.
- Menandai produk aktif/nonaktif.

Field utama:
- Kode Produk (unik)
- Nama
- Satuan (opsional)
- Harga Modal per Unit (opsional, dipakai untuk analisis margin jika diperlukan)
- Status aktif

### 4.3. Harga Bulanan (per Mitra & Produk)

Menu: **Master Data → Harga Bulanan**

Fungsi:
- Menyimpan harga jual per mitra per produk per bulan.

Field utama:
- Mitra
- Produk
- Tahun, Bulan
- Harga Jual
- Margin per Unit (opsional; jika diisi akan dipakai untuk perhitungan margin di laporan)

Aturan:
- Kombinasi Mitra + Produk + Tahun + Bulan tidak boleh duplikat.

### 4.4. Kalender Operasional

Menu: **Master Data → Kalender Operasional**

Fungsi:
- Menentukan hari operasional/libur.
- Menjadi “gerbang” untuk semua transaksi.

Field:
- Tanggal (unik)
- Status: operasional / libur
- Keterangan (opsional)

## 5. Transaksi (Fase 2)

### 5.1. Produksi Harian

Menu: **Transaksi → Produksi Harian**

Konsep:
- Header: tanggal + catatan
- Detail: per produk

Cara input:
1) Klik **Tambah** → isi Tanggal → Simpan.
2) Pada halaman edit, tambahkan detail per produk:
   - Stok Awal
   - Jumlah Produksi
   - Stok Layak Jual Kembali
3) Sistem menghitung:
   - Stok Siap Jual = stok_awal + jumlah_produksi + stok_layak_jual_kembali

Catatan:
- 1 tanggal hanya boleh 1 header produksi.
- 1 produk tidak boleh diinput dua kali pada detail produksi tanggal yang sama.

### 5.2. Pengiriman Mitra

Menu: **Transaksi → Pengiriman Mitra**

Konsep:
- Header: tanggal + mitra + catatan
- Detail: per produk yang dititip

Cara input:
1) Klik **Tambah** → isi Tanggal & Mitra → Simpan.
2) Pada halaman edit, tambahkan detail:
   - Produk
   - Jumlah Titip
   - Harga Jual
   - Margin/Unit (opsional)

Catatan:
- 1 produk tidak boleh diinput dua kali pada detail pengiriman yang sama.

### 5.3. Laporan Penjualan Mitra

Menu: **Transaksi → Laporan Penjualan Mitra**

Konsep:
- Header: tanggal + mitra + catatan
- Detail: hasil penjualan per produk

Cara input:
1) Klik **Tambah** → isi Tanggal & Mitra → Simpan.
2) Pada halaman edit, tambahkan detail:
   - Produk
   - Jumlah Titip
   - Sisa Barang
   - Stok Layak Jual Kembali (bagian dari sisa barang)
   - Harga Jual
   - Margin/Unit (opsional)

Perhitungan otomatis (disimpan ke tabel detail):
- Jumlah Terjual = jumlah_titip - sisa_barang
- Stok Tidak Layak Jual = sisa_barang - stok_layak_jual_kembali
- Total Penjualan = jumlah_terjual × harga_jual
- Total Margin = jumlah_terjual × margin_per_unit (jika margin kosong, dianggap 0)

Validasi:
- Sisa Barang tidak boleh lebih besar dari Jumlah Titip.
- Stok Layak Jual Kembali tidak boleh lebih besar dari Sisa Barang.
- 1 mitra + 1 tanggal tidak boleh duplikat header laporan.
- 1 produk tidak boleh duplikat di detail laporan yang sama.

## 6. Keuangan (Fase 3)

### 6.1. Biaya Harian

Menu: **Keuangan → Biaya Harian**

Konsep:
- Header: tanggal + catatan
- Detail: item biaya (qty × harga)

Cara input:
1) Klik **Tambah** → isi Tanggal → Simpan.
2) Pada halaman edit, tambah detail biaya:
   - Nama Item
   - Qty (opsional)
   - Satuan (opsional)
   - Harga Satuan (opsional)

Perhitungan:
- Total item = qty × harga_satuan (jika salah satu kosong, total item = 0)
- Total harian = SUM(total item) dan ditampilkan pada header.

### 6.2. Pembayaran Mitra

Menu: **Keuangan → Pembayaran Mitra**

Konsep:
- Pembayaran dihubungkan ke 1 **Laporan Penjualan Mitra**.

Cara input:
1) Klik **Tambah**.
2) Isi:
   - Tanggal
   - Mitra
   - Laporan Penjualan (hanya menampilkan laporan yang belum punya pembayaran)
   - Jumlah Bayar
   - Metode (opsional)
   - Status: Draft/Confirmed
3) Simpan.

Aturan:
- Satu laporan hanya boleh memiliki satu pembayaran.
- Laporan yang dipilih harus sesuai mitra dan tanggal pembayaran yang diinput.
- Rekap pembayaran hanya menghitung yang status **confirmed**.

## 7. Rekap (Fase 4)

Menu: **Rekap → Rekap & Laporan** (`/admin/rekap`)

### 7.1. Rekap Harian Owner

Menampilkan ringkasan per tanggal dalam range:
- Omzet: SUM(detail_laporan_penjualan_mitra.total_penjualan)
- Margin: SUM(detail_laporan_penjualan_mitra.total_margin)
- Biaya: biaya_harian.total_biaya
- Net: margin - biaya
- Pembayaran: SUM(pembayaran_mitra_harian.jumlah_bayar) status confirmed

### 7.2. Rekap Per Mitra

Menampilkan ringkasan per mitra dalam range:
- Omzet
- Margin
- Pembayaran (confirmed)
- Selisih = omzet - pembayaran (indikasi piutang jika positif)

## 8. Setup & Operasional Teknis (untuk Admin/IT)

### 8.1. Menjalankan aplikasi (Laragon)

1) Jalankan Apache & MySQL dari Laragon.
2) Pastikan `.env` sudah benar (DB host, user, password).
3) Jalankan migrasi dan seed jika database baru:
   - `php artisan migrate`
   - `php artisan db:seed`

Seeder yang tersedia:
- RoleSeeder (Owner, Admin)
- KalenderOperasionalSeeder (membuat tanggal operasional untuk tahun ini dan tahun depan)

### 8.2. Build asset (CSS/JS)

Project memakai Laravel Mix (Webpack) untuk asset.
Jika perlu rebuild:
- `npm install`
- `npm run dev`

Catatan lingkungan:
- Jika node/npm tidak terbaca dari PATH Windows, gunakan node bawaan Laragon atau pastikan PATH sudah benar.

## 9. Troubleshooting

### 9.1. Tidak bisa input transaksi karena “Tanggal libur”

Penyebab:
- Tanggal berstatus libur di Kalender Operasional.

Solusi:
- Ubah status tanggal tersebut menjadi `operasional` di menu Kalender Operasional.

### 9.2. Tidak bisa input transaksi karena “Tanggal belum terdaftar”

Penyebab:
- Tanggal belum ada di tabel Kalender Operasional.

Solusi:
- Tambahkan tanggal tersebut di Kalender Operasional sebagai `operasional`.

### 9.3. Pembayaran tidak muncul di rekap

Penyebab:
- Status pembayaran masih `draft`.

Solusi:
- Edit pembayaran lalu ubah status menjadi `confirmed`.

## 10. Catatan Pengembangan Lanjutan

Jika ingin sistem lebih “rapi produksi”:
- Tambah autentikasi (login) dan proteksi route `/admin`.
- Terapkan role/permission (Owner/Admin) untuk membatasi menu tertentu.
- Otomasi pengisian harga (ambil dari Harga Bulanan saat input pengiriman/laporan).
- Tambah rekap stok layak/tidak layak dan laporan piutang per mitra/per tanggal.

