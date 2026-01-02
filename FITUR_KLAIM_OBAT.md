# Fitur Klaim Obat untuk Role Apotek

## Ringkasan
Fitur ini memungkinkan role **apotek** untuk menandai obat sebagai "sudah diklaim" dari daftar pasien. Ketika obat diklaim, akan:
1. Menghilang dari daftar pasien aktif (hanya menampilkan obat yang belum diklaim)
2. Masuk ke halaman "Riwayat Obat Diklaim" dengan informasi lengkap dan waktu klaim

## Perubahan yang Dilakukan

### 1. Database Migration
**File**: `database/migrations/2025_12_09_000001_add_is_klaim_to_obat_prb_table.php`
- Menambah kolom `is_klaim` (boolean, default: false) ke tabel `obat_prb`
- Menambah kolom `tanggal_klaim` (timestamp, nullable) ke tabel `obat_prb`

### 2. Model Update
**File**: `app/Models/ObatPrb.php`
- Menambah `is_klaim` dan `tanggal_klaim` ke array `$fillable`

### 3. Controller
**File**: `app/Http/Controllers/FktpPatientController.php`

Ditambahkan 2 method baru:

#### `klaimObat($idObat)`
- **Akses**: Role apotek saja
- **Fungsi**: Menandai obat sebagai sudah diklaim
- **Validasi**: Memastikan pasien terkait dengan apotek yang sedang login
- **Hasil**: Update `is_klaim = true` dan `tanggal_klaim = now()`

#### `riwayatObatKlaim()`
- **Akses**: Role apotek saja
- **Fungsi**: Menampilkan semua obat yang sudah diklaim
- **Filter**: Hanya obat dari pasien yang terkait dengan apotek yang sedang login
- **Urutan**: Diurutkan dari yang paling baru diklaim
- **Pagination**: 15 item per halaman

### 4. Routes
**File**: `routes/web.php`

Ditambahkan di route group apotek:
```php
Route::post('/obat/{idObat}/klaim', [FktpPatientController::class, 'klaimObat'])->name('apotek.obat.klaim');
Route::get('/obat/riwayat-klaim', [FktpPatientController::class, 'riwayatObatKlaim'])->name('apotek.obat.riwayat-klaim');
```

### 5. Views

#### `resources/views/fktp/patients/index.blade.php`
- Tambah tombol "Klaim" (tombol hijau) di kolom Aksi untuk role apotek
- Tombol hanya muncul jika ada obat belum diklaim
- Menampilkan badge "Sudah Diklaim" jika semua obat sudah diklaim

#### `resources/views/fktp/patients/show.blade.php`
- Filter obat yang ditampilkan untuk role apotek (hanya obat dengan `is_klaim = false`)
- Tambah tombol "Klaim" (hijau) atau badge "Sudah Diklaim" di setiap diagnosis

#### `resources/views/fktp/obat/riwayat-klaim.blade.php` (BARU)
- Halaman riwayat obat yang sudah diklaim
- Menampilkan: No Pasien, Nama Pasien, No BPJS, Nama Obat, Jumlah, Satuan, Dosis, Aturan Pakai, Tanggal Klaim
- Pagination 15 item per halaman

### 6. Navigation
**File**: `resources/views/layouts/app.blade.php`
- Tambah menu "Riwayat Obat Diklaim" di sidebar (hanya untuk role apotek)
- Ikon: `bi-clock-history`
- Route: `apotek.obat.riwayat-klaim`

## Cara Penggunaan

### Untuk Apotek:
1. Login sebagai user dengan role `apotek`
2. Buka menu "Data Pasien (View Only)"
3. Cari pasien dan tekan tombol "Detail"
4. Di halaman detail, lihat tab "PRB Aktif"
5. Lihat obat-obatan yang belum diklaim
6. Tekan tombol "Klaim" untuk menandai obat sebagai sudah diklaim
7. Obat akan hilang dari daftar pasien aktif dan masuk ke "Riwayat Obat Diklaim"
8. Untuk melihat riwayat lengkap, buka menu "Riwayat Obat Diklaim" di sidebar

### Data yang Dicatat:
- `is_klaim`: Status apakah obat sudah diklaim (true/false)
- `tanggal_klaim`: Timestamp kapan obat diklaim

## Validasi & Keamanan
- Hanya role `apotek` yang bisa mengakses fitur klaim
- Apotek hanya bisa klaim obat dari pasien yang terkait dengan FKTP mereka
- Riwayat hanya menampilkan obat yang diklaim oleh apotek yang sedang login

## Testing Checklist
- [ ] Migration berhasil menambah kolom
- [ ] Tombol "Klaim" muncul di daftar pasien (role apotek)
- [ ] Tombol "Klaim" muncul di halaman detail pasien (role apotek)
- [ ] Setelah klik "Klaim", obat hilang dari daftar aktif
- [ ] Obat muncul di "Riwayat Obat Diklaim"
- [ ] Tanggal klaim tercatat dengan benar
- [ ] Hanya role apotek yang bisa akses fitur ini
- [ ] Pagination bekerja dengan baik di halaman riwayat
