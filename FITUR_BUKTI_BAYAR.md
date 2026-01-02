# Fitur Upload Bukti Bayar Apotek untuk Admin

## Ringkasan
Menambahkan kolom untuk upload bukti bayar apotek (PDF) pada diagnosa PRB. Fitur ini memungkinkan admin untuk menyimpan dan menampilkan bukti pembayaran dari apotek, sementara rumah sakit sudah menggunakan kolom "Upload Hasil Pemeriksaan" yang ada.

## Perubahan yang Dilakukan

### 1. Database Migration
**File**: `database/migrations/2025_12_12_000001_add_bukti_bayar_columns_to_diagnosa_prb_table.php`

Ditambahkan satu kolom baru di tabel `diagnosa_prb`:
- `bukti_bayar_pdf` (string, nullable) - Path file bukti bayar dalam format PDF dari apotek
- `asal_klaim` (enum: 'rumah_sakit', 'apotek', nullable) - Tidak digunakan, hanya untuk fleksibilitas di masa depan

### 2. Model Update
**File**: `app/Models/DiagnosaPrb.php`

Diperbarui `$fillable` dengan kolom baru:
```php
protected $fillable = [
    'id_pasien',
    'diagnosa',
    'status_prb',
    'no_telp_pic',
    'tgl_pelayanan',
    'catatan',
    'file_upload',
    'bukti_bayar_pdf'  // BARU - untuk bukti bayar apotek
];
```

### 3. Controller Update
**File**: `app/Http/Controllers/PasienController.php`

Method `updateDiagnosa()` diperbarui untuk:
- Validasi file bukti bayar PDF (max 5MB)
- Handle upload file bukti bayar ke folder `storage/bukti_bayar/`
- Delete file lama jika ada file baru yang diupload

### 4. Views Update

#### `resources/views/pasien/index.blade.php`
- Tambah kolom "Bukti Bayar Apotek" untuk role admin
- Kolom hanya tampil jika user adalah admin
- Menampilkan icon PDF jika file ada, atau "-" jika tidak ada
- Kolom file_upload (hasil pemeriksaan RS) tetap ada

#### `resources/views/pasien/show.blade.php`
- Tambah kolom "Bukti Bayar Apotek" di tabel PRB Aktif untuk admin
- Tambah kolom "Bukti Bayar Apotek" di tabel Riwayat PRB untuk admin
- Gunakan warna button kuning (btn-outline-warning) untuk bukti bayar apotek
- File hanya tampil jika bukti_bayar_pdf ada

#### `resources/views/pasien/edit.blade.php`
- Tambah form upload bukti bayar apotek untuk role admin:
  - Input file dengan accept PDF saja
  - Tampilan file yang sudah diupload dengan link untuk preview
- Form hanya muncul jika user adalah admin

## Cara Penggunaan

### Untuk Admin

1. **Buka halaman daftar pasien** (`/pasien`)
   - Lihat kolom "Bukti Bayar Apotek"
   - Klik icon PDF untuk melihat bukti bayar

2. **Buka halaman detail pasien**
   - Tab "PRB Aktif" dan "Riwayat PRB" menampilkan kolom bukti bayar
   - Lihat bukti bayar apotek dari diagnosa

3. **Edit diagnosa untuk upload bukti bayar apotek**
   - Buka halaman edit pasien
   - Di bagian "Diagnosa PRB", cari form "Upload Bukti Bayar Apotek (PDF)"
   - Pilih file PDF (max 5MB)
   - Klik "Update Diagnosa" untuk simpan

### Perbedaan Kolom Upload File

- **Kolom "Upload Hasil Pemeriksaan"** (file_upload) = Untuk dokumentasi pemeriksaan dari Rumah Sakit
- **Kolom "Bukti Bayar Apotek"** (bukti_bayar_pdf) = Untuk bukti pembayaran dari Apotek

## Validasi & Keamanan

- File bukti bayar harus berformat PDF
- Ukuran maksimal: 5 MB
- File disimpan di `storage/bukti_bayar/` dengan nama unik (timestamp + nama original)
- File lama otomatis dihapus jika ada file baru
- Upload hanya bisa dilakukan oleh role admin
- Akses file aman melalui route terenkripsi (private disk)

## Testing Checklist

- [ ] Admin bisa lihat kolom "Bukti Bayar Apotek" di daftar pasien
- [ ] Admin bisa lihat kolom "Bukti Bayar Apotek" di detail pasien (tab PRB Aktif)
- [ ] Admin bisa lihat kolom "Bukti Bayar Apotek" di detail pasien (tab Riwayat PRB)
- [ ] Admin bisa upload file PDF untuk bukti bayar apotek
- [ ] File lama terhapus saat upload file baru
- [ ] Non-admin tidak bisa lihat form upload bukti bayar apotek
- [ ] Non-admin tetap bisa lihat kolom file upload hasil pemeriksaan
- [ ] Icon PDF muncul jika ada file bukti bayar
- [ ] Tanda "-" muncul jika tidak ada file
