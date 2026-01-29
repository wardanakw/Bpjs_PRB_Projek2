@extends('layouts.app')

@section('title', 'Tambah Data Pasien PRB')

@section('content')
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

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
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
            <h4 class="mb-4"><b>Tambah Data Pasien PRB</b></h4>
            
            <div class="alert alert-info alert-dismissible">
                <i class="bi bi-info-circle"></i>
                <strong>Sistem Deteksi Pasien</strong><br>
                Sistem akan otomatis mendeteksi apakah pasien dengan nomor kartu BPJS yang sama sudah pernah terdaftar. 
                Jika ya, diagnosis baru akan ditambahkan ke riwayat pasien yang sudah ada tanpa membuat data pasien duplikat.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <ul class="nav nav-tabs mb-3" id="tambahDataTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">Detail Pasien</button>
                </li>
            </ul>

            <div class="tab-content">
              
                <div class="tab-pane fade show active" id="detail" role="tabpanel">
                    <form id="formPasien" action="{{ route('pasien.store') }}" method="POST">
    @csrf
    <div class="row">

        <div class="col-md-6 mb-3">
            <label>No SRB</label>
            <input type="text" name="no_sep" class="form-control"
                   value="{{ old('no_sep') }}" required>
        </div>

        <div class="col-md-6 mb-3">
            <label>No Kartu BPJS</label>
            <input type="text" name="no_kartu_bpjs" class="form-control"
                   value="{{ old('no_kartu_bpjs') }}" required>
        </div>

        <div class="col-md-6 mb-3">
            <label>Nama Lengkap Pasien</label>
            <input type="text" name="nama_pasien" class="form-control"
                   value="{{ old('nama_pasien') }}" required>
        </div>

        <div class="col-md-3 mb-3">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
            <div class="input-group" style="max-width: 220px;">
                <input 
                    type="text" 
                    name="tanggal_lahir" 
                    id="tanggal_lahir" 
                    class="form-control form-control-sm"
                    placeholder="YYYY-MM-DD"
                    value="{{ old('tanggal_lahir') }}"
                    required
                >
                <button 
                    class="btn btn-outline-secondary btn-sm" 
                    type="button" 
                    id="btnTanggalLahir"
                >
                    <i class="bi bi-calendar-event"></i>
                </button>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label>No Telepon</label>
            <input type="text" name="no_telp" class="form-control"
                   value="{{ old('no_telp') }}">
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
                {{ old('fktp_asal', $patient->fktp_asal ?? '') == $row->nama_fktp ? 'selected' : '' }}
            >
                {{ $row->nama_fktp }}
            </option>
        @endforeach
    </select>
</div>

<input type="hidden" id="fktp_kode" name="fktp_kode" value="{{ old('fktp_kode', $patient->fktp_kode ?? '') }}">
<input type="hidden" id="kode_apotek" name="kode_apotek" value="{{ old('kode_apotek', $patient->kode_apotek ?? '') }}">


</div>

<div class="col-md-6 mb-3">
    <label>Nama Apotek</label>
    
    <input  type="text" id="nama_apotek" name="nama_apotek"class="form-control" value="{{ old('nama_apotek', $patient->nama_apotek ?? '') }}"  readonly>
</div>


    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">Simpan Pasien</button>
    </div>
    <div class="mt-3 text-end">
                <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
</form>

                </div>

            </div>

            
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    const dateInput = flatpickr("#tanggal_lahir", {
        dateFormat: "Y-m-d",
        allowInput: true,
        clickOpens: false
    });

    $('#btnTanggalLahir').on('click', function () {
        dateInput.open();
    });

    setTimeout(() => {
        $('.alert').each(function () {
            const bsAlert = new bootstrap.Alert(this);
            bsAlert.close();
        });
    }, 4000);


    $('#fktp_asal').select2({
        placeholder: "-- Pilih FKTP Asal --",
        allowClear: true,
        width: '100%'
    });

   function updateApotekDanKode() {
    let selected = $('#fktp_asal').find(':selected');

    let apotek       = selected.data('apotek') || '';
    let kodeFktp     = selected.data('kode') || '';
    let kodeApotek   = selected.data('kode-apotek') || '';

    $('#nama_apotek').val(apotek);
    $('#fktp_kode').val(kodeFktp);
    $('#kode_apotek').val(kodeApotek); 
}

    
  $('#fktp_asal').on('change', function () {
    updateApotekDanKode();
});


    updateApotekDanKode();

    @if(old('fktp_asal'))
        $('#fktp_asal').val('{{ old('fktp_asal') }}').trigger('change');
    @endif


    @if(session('warning'))
        $('#formPasien').on('submit', function(e) {
            var confirmMessage = 'Pasien dengan nomor kartu BPJS ini sudah terdaftar sebelumnya. ' +
                               'Apakah Anda yakin ingin menambahkan diagnosis baru ke riwayat pasien ini?';
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    @endif

});


</script>

@endsection