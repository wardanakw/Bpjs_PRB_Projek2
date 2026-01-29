@extends('layouts.app')

@section('title', 'Edit Data Pasien PRB')

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

{{-- ALERT --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mt-3">
    {{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="main-content p-4">
    <div class="card shadow-sm rounded-4">
        <div class="card-body">

            {{-- ===================== DATA PASIEN ===================== --}}
            <h4 class="mb-4"><b>Edit Data Pasien PRB</b></h4>
            <h6 class="section-title">Detail Pasien</h6>

            <form id="formPasien" action="{{ route('pasien.update', $pasien->id_pasien) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>No SRB</label>
                        <input type="text" name="no_sep" class="form-control" value="{{ $pasien->no_sep }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No Kartu BPJS</label>
                        <input type="text" name="no_kartu_bpjs" class="form-control" value="{{ $pasien->no_kartu_bpjs }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Nama Pasien</label>
                        <input type="text" name="nama_pasien" class="form-control" value="{{ $pasien->nama_pasien }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ $pasien->tanggal_lahir }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No Telepon</label>
                        <input type="text" name="no_telp" class="form-control" value="{{ $pasien->no_telp }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>FKTP Asal</label>
                        <select id="fktp_asal" name="fktp_asal" class="form-control" required>
                            <option value="">-- Pilih FKTP Asal --</option>
                            @foreach($fktp as $row)
                               <option 
    value="{{ $row->nama_fktp }}"
    data-apotek="{{ $row->nama_apotek }}"
    data-kode="{{ $row->kode_fktp ?? $row->fktp_kode ?? '' }}"
    data-kode-apotek="{{ $row->kode_apotek }}"
    {{ old('fktp_asal', $pasien->fktp_asal) == $row->nama_fktp ? 'selected' : '' }}
>
    {{ $row->nama_fktp }}
</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Nama Apotek</label>
                        <input type="text" id="nama_apotek" name="nama_apotek"
       class="form-control"
       value="{{ old('nama_apotek', $pasien->nama_apotek) }}"
       readonly>
                    </div>

                   <input type="hidden" id="fktp_kode" name="fktp_kode"
       value="{{ old('fktp_kode', $pasien->fktp_kode) }}">

<input type="hidden" id="kode_apotek" name="kode_apotek"
       value="{{ old('kode_apotek', $pasien->kode_apotek) }}">
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('pasien.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Pasien
                    </button>
                </div>

            </form>

            <hr class="section-divider">

            {{-- ===================== DIAGNOSA PRB ===================== --}}
            <h6 class="section-title">Diagnosa PRB</h6>

            @php
                $listDiagnosa = ['Diabetes Melitus','Hipertensi','Penyakit Jantung','Asma','PPOK','Epilepsi','Skizofrenia','Stroke','SLE'];
                $selectedDiagnosa = old('diagnosa', $diagnosa->diagnosa ?? '');
                $diagnosaLain = old('diagnosa_lain', $diagnosa->diagnosa_lain ?? '');
            @endphp

            <form id="formDiagnosa"
                action="{{ isset($diagnosa) ? route('pasien.diagnosa.update', $diagnosa->id_diagnosa) : route('pasien.diagnosa.store') }}"
                method="POST" enctype="multipart/form-data">

                @csrf
                @if(isset($diagnosa)) @method('PUT') @endif

                <input type="hidden" name="id_pasien" value="{{ $pasien->id_pasien }}">

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
                        <input type="date" name="tgl_pelayanan" class="form-control"
                               value="{{ $diagnosa->tgl_pelayanan ?? '' }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Status PRB</label>
                        <input type="text" class="form-control" name="status_prb"
                               value="{{ $diagnosa->status_prb ?? 'Belum Aktif' }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No Telp PIC</label>
                        <input type="text" name="no_telp_pic" class="form-control"
                               value="{{ $diagnosa->no_telp_pic ?? '' }}" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control">{{ $diagnosa->catatan ?? '' }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Upload Hasil Pemeriksaan</label>
                        <input type="file" class="form-control" name="file_upload">
                        
                        @if(isset($diagnosa) && $diagnosa->file_upload)
                            <a href="{{ route('pasien.diagnosa.file', $diagnosa->id_diagnosa) }}"
                               class="btn btn-outline-primary btn-sm mt-2" target="_blank">
                                Lihat File Aman
                            </a>
                        @endif
                    </div>

                    @if(auth()->user()->role === 'admin')
                    <div class="col-md-12 mb-3">
                        <label>Upload Bukti Bayar Apotek (PDF)</label>
                        <input type="file" class="form-control" name="bukti_bayar_pdf" accept="application/pdf">
                        <small class="text-muted">Maksimal 5MB, format PDF</small>
                        
                        @if(isset($diagnosa) && $diagnosa->obatPrb->first() && $diagnosa->obatPrb->first()->bukti_bayar_pdf)
                            <div class="mt-2">
                                <a href="{{ route('laporan.download.pdf', urlencode($diagnosa->obatPrb->first()->bukti_bayar_pdf)) }}"
                                   class="btn btn-outline-info btn-sm" target="_blank">
                                    <i class="bi bi-file-pdf"></i> Lihat Bukti Bayar
                                </a>
                            </div>
                        @endif
                    </div>
                    @endif

                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        {{ isset($diagnosa) ? 'Update Diagnosa' : 'Simpan Diagnosa' }}
                    </button>
                </div>

            </form>

            <hr class="section-divider">

            {{-- ===================== OBAT PRB ===================== --}}
            <h6 class="section-title">Obat PRB</h6>

            {{-- Form Tambah Obat --}}
            <form action="{{ route('pasien.obat.store') }}" method="POST" class="mb-3">
                @csrf

                <input type="hidden" name="id_diagnosa" value="{{ $diagnosa->id_diagnosa ?? '' }}">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label>Nama Obat</label>
                        <input type="text" name="nama_obat" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah_obat" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label>Satuan</label>
                        <input type="text" name="satuan" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>Dosis</label>
                        <input type="text" name="dosis_obat" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>Aturan Pakai</label>
                        <input type="text" name="aturan_pakai" class="form-control">
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-success w-100">+</button>
                    </div>

                </div>
            </form>

            {{-- List Obat --}}
            @if($obat->count() > 0)

                @foreach($obat as $o)
                    <form action="{{ route('pasien.obat.update', $o->id_obat) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id_diagnosa" value="{{ $diagnosa->id_diagnosa ?? '' }}">

                        <div class="row g-2">

                            <div class="col-md-3">
                                <input type="text" name="nama_obat" class="form-control" value="{{ $o->nama_obat }}">
                            </div>

                            <div class="col-md-2">
                                <input type="number" name="jumlah_obat" class="form-control" value="{{ $o->jumlah_obat }}">
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="satuan" class="form-control" value="{{ $o->satuan }}">
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="dosis_obat" class="form-control" value="{{ $o->dosis_obat }}">
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="aturan_pakai" class="form-control" value="{{ $o->aturan_pakai }}">
                            </div>

                            <div class="col-md-1">
                                <button class="btn btn-primary w-100">Simpan</button>
                            </div>

                        </div>

                    </form>
                @endforeach

            @else
                <p class="text-muted">Belum ada obat.</p>
            @endif

            <div class="mt-4 text-end">
                <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Kembali</a>
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


    const $fktp = $('#fktp_asal').select2({
        placeholder: "-- Pilih FKTP Asal --",
        allowClear: true,
        width: "100%"
    });

    $fktp.on('change', function () {
        let selected = $(this).find(':selected');

        let apotek = selected.data('apotek') || "-";
        let fktpKode = selected.data('kode') || "";
        let kodeApotek = selected.data('kode-apotek') || "";

        $('#nama_apotek').val(apotek);
        $('#fktp_kode').val(fktpKode);
        $('#kode_apotek').val(kodeApotek);
    });


    let fktpValue = "{{ old('fktp_asal', $pasien->fktp_asal ?? '') }}";

    if (fktpValue) {
        $fktp.val(fktpValue).trigger('change');
    }


    document.getElementById('diagnosaSelect')?.addEventListener('change', function () {
        const inputLain = document.getElementById('diagnosaLain');
        inputLain.style.display = this.value === 'Lainnya' ? 'block' : 'none';
    });

});
</script>
@endsection
