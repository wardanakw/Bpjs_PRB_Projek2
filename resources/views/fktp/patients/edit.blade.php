@extends('layouts.app')

@section('title', 'Edit Data Pasien FKTP')

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
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
 }
.section-title { 
    font-size: 18px;
    font-weight: 600; 
    margin-bottom: 15px; 
    }
hr.section-divider { 
    border-top: 2px solid #0078D7;
    opacity: .4; margin: 25px 0; 
    }
.btn-primary, .btn-success {
    background:#0078D7;
    border:none;
    border-radius:8px;
     }
.btn-primary:hover, .btn-success:hover { 
    background:#0056b3;
 }
.btn-outline-primary { 
    border-radius:8px;
    border:1.5px solid #0078D7; 
    color:#0078D7; 
    font-weight:500; 
}
.btn-outline-primary:hover { 
    background:#0078D7;
    color:#fff; 
}
.form-control:focus { 
    border-color:#0078D7; 
    box-shadow:0 0 0 .15rem rgba(0,120,215,.25);
}
label { 
    font-weight:500;
}
</style>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3">
    {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mt-3">
    {{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="main-content p-4">
    <div class="card shadow-sm rounded-4">
        <div class="card-body">

            <h4 class="mb-4"><b>Edit Data Pasien PRB - FKTP</b></h4>
            <h6 class="section-title">Detail Pasien</h6>

            <form id="formPasien"
                action="{{ (auth()->user()->role === 'apotek') ? route('apotek.patients.update', $patient->id_pasien) : route('fktp.patients.update', $patient->id_pasien) }}"
                method="POST">
                @csrf @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3"><label>No SRB</label>
                        <input type="text" name="no_sep" class="form-control" value="{{ $patient->no_sep }}" required>
                    </div>

                    <div class="col-md-6 mb-3"><label>No Kartu BPJS</label>
                        <input type="text" name="no_kartu_bpjs" class="form-control" value="{{ $patient->no_kartu_bpjs }}" required>
                    </div>

                    <div class="col-md-6 mb-3"><label>No Kunjungan</label>
                        <input type="text" name="no_kunjungan" class="form-control" value="{{ $patient->no_kunjungan }}" required>
                    </div>

                    <div class="col-md-6 mb-3"><label>Nama Pasien</label>
                        <input type="text" name="nama_pasien" class="form-control" value="{{ $patient->nama_pasien }}" required>
                    </div>

                    <div class="col-md-6 mb-3"><label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ $patient->tanggal_lahir }}" required>
                    </div>

                    <div class="col-md-6 mb-3"><label>No Telepon</label>
                        <input type="text" name="no_telp" class="form-control" value="{{ $patient->no_telp }}">
                    </div>

                    <div class="col-md-12 mb-3"><label>FKTP Asal</label>
                        <input type="text" name="fktp_asal" class="form-control" value="{{ $patient->fktp_asal }}">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update Pasien</button>
                </div>
            </form>

            <div class="mt-4 text-end">
                <a href="{{ (auth()->user()->role === 'apotek') ? route('apotek.patients.index') : route('fktp.patients.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
