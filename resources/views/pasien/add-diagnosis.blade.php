@extends('layouts.app')

@section('title', 'Tambah Diagnosis Baru')

@section('content')
<style>
    body {
        background-color: #f7faff;
        font-family: 'Poppins', sans-serif;
    }

    .card {
        border: none;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    hr.section-divider {
        border-top: 2px solid #0078D7;
        opacity: .4;
        margin: 25px 0;
    }

    .btn-primary,
    .btn-success {
        background: #0078D7;
        border: none;
        border-radius: 8px;
    }

    .btn-primary:hover,
    .btn-success:hover {
        background: #0056b3;
    }

    .btn-outline-primary {
        border-radius: 8px;
        border: 1.5px solid #0078D7;
        color: #0078D7;
        font-weight: 500;
    }

    .btn-outline-primary:hover {
        background: #0078D7;
        color: #fff;
    }

    .form-control:focus {
        border-color: #0078D7;
        box-shadow: 0 0 0 .15rem rgba(0, 120, 215, .25);
    }

    label {
        font-weight: 500;
    }
</style>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-info-circle"></i> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-exclamation-circle"></i> Ada beberapa kesalahan:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="main-content p-4">
    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <h4 class="mb-4"><b>Tambah Diagnosis Baru</b></h4>

            <div class="alert alert-info">
                <h6><i class="bi bi-person-circle"></i> Informasi Pasien</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nama:</strong> {{ $pasien->nama_pasien }}<br>
                        <strong>No Kartu BPJS:</strong> {{ $pasien->no_kartu_bpjs }}
                    </div>
                    <div class="col-md-6">
                        <strong>Tanggal Lahir:</strong> {{ $pasien->tanggal_lahir ?? '-' }}<br>
                        <strong>FKTP Asal:</strong> {{ $pasien->fktp_asal }}
                    </div>
                </div>
                <hr>
                <small class="text-muted">Anda akan menambahkan diagnosis baru untuk pasien ini. Riwayat sebelumnya akan tetap tersimpan.</small>
            </div>

            {{-- ===================== DIAGNOSA PRB ===================== --}}
            <h6 class="section-title">Diagnosa PRB</h6>

            @php
                $listDiagnosa = ['Diabetes Melitus','Hipertensi','Penyakit Jantung','Asma','PPOK','Epilepsi','Skizofrenia','Stroke','SLE'];
                $selectedDiagnosa = old('diagnosa');
                $diagnosaLain = old('diagnosa_lain');
            @endphp

            <form id="formDiagnosa" action="{{ route('pasien.storeDiagnosis', $pasien->id_pasien) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                   <div class="col-md-6 mb-3">
    <label>Diagnosa</label>
    <select name="diagnosa" id="diagnosaSelect" class="form-select" required>
        <option value="">-- Pilih Diagnosa --</option>

        @foreach ($listDiagnosa as $item)
            <option value="{{ $item }}" 
                {{ old('diagnosa', $selectedDiagnosa) == $item ? 'selected' : '' }}>
                {{ $item }}
            </option>
        @endforeach

        <option value="Lainnya" 
            {{ old('diagnosa', $selectedDiagnosa) == 'Lainnya' ? 'selected' : '' }}>
            Lainnya
        </option>
    </select>

    <input type="text"
        id="diagnosaLain"
        name="diagnosa_lain"
        class="form-control mt-2"
        placeholder="Masukkan diagnosa lain"
        value="{{ old('diagnosa_lain', $diagnosaLain) }}"
        style="display: {{ old('diagnosa', $selectedDiagnosa) == 'Lainnya' ? 'block' : 'none' }};">
</div>


                    <div class="col-md-6 mb-3">
                        <label>Tanggal Pemeriksaan</label>
                        <input type="date" name="tgl_pemeriksaan" class="form-control"
                               value="{{ old('tgl_pemeriksaan', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Status PRB</label>
                        <select name="status_prb" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            @if($isCorrectFktp)
                                <option value="Aktif" {{ old('status_prb') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            @else
                                <option value="" disabled style="color: #6c757d;">Aktif (Hanya bisa diaktifkan oleh FKTP {{ $pasien->fktp_asal }})</option>
                            @endif
                            <option value="Tidak Aktif" {{ old('status_prb') === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @if(!$isCorrectFktp)
                            <small class="text-warning">
                                <i class="bi bi-info-circle"></i> Status "Aktif" hanya bisa dipilih oleh FKTP {{ $pasien->fktp_asal }}
                            </small>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No Telp PIC</label>
                        <input type="text" name="no_telp_pic" class="form-control"
                               value="{{ old('no_telp_pic') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No SEP</label>
                        <input type="text" name="no_sep" class="form-control"
                               value="{{ old('no_sep') }}" required>
                        <small class="text-muted">Nomor Surat Eligibilitas Peserta</small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Catatan</label>
                        <textarea name="catatan_tambahan" class="form-control">{{ old('catatan_tambahan') }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Upload Hasil Pemeriksaan</label>
                        <input type="file" class="form-control" name="file_upload" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Maksimal 2MB, format PDF, JPG, PNG</small>
                    </div>

                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Diagnosis Baru
                    </button>
                </div>

            </form>

            <hr class="section-divider">

            {{-- ===================== OBAT PRB ===================== --}}
            <h6 class="section-title">Obat PRB</h6>

            {{-- Form Tambah Obat --}}
            <form action="#" method="POST" class="mb-3">
                @csrf

                <div class="row g-3">

                    <div class="col-md-3">
                        <label>Nama Obat</label>
                        <input type="text" name="nama_obat" class="form-control" value="{{ old('nama_obat') }}">
                    </div>

                    <div class="col-md-2">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah_obat" class="form-control" value="{{ old('jumlah_obat') }}" min="1">
                    </div>

                    <div class="col-md-2">
                        <label>Dosis</label>
                        <input type="text" name="dosis_obat" class="form-control" value="{{ old('dosis_obat') }}">
                    </div>

                    <div class="col-md-2">
                        <label>Aturan Pakai</label>
                        <input type="text" name="aturan_pakai" class="form-control" value="{{ old('aturan_pakai') }}">
                    </div>

                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="text-muted small">Obat akan disimpan bersama diagnosis</div>
                    </div>

                </div>
            </form>
            <div class="mt-4 text-end">
                <a href="{{ route('pasien.show', $pasien->id_pasien) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Detail Pasien
                </a>
            </div>

        </div>
    </div>
</div>

{{-- ASSET JS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    setTimeout(() => {
        $('.alert').alert('close');
    }, 4000);

    document.getElementById('diagnosaSelect').addEventListener('change', function () {
        const inputLain = document.getElementById('diagnosaLain');
        inputLain.style.display = this.value === 'Lainnya' ? 'block' : 'none';
    });

});
</script>
@endsection