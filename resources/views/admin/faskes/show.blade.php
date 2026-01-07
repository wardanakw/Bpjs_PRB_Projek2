@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Detail Fasilitas Kesehatan</h4>
        <a href="{{ route('admin.faskes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>{{ $faskes->nama_faskes }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kode Faskes:</strong> {{ $faskes->kode_faskes ?? '-' }}</p>
                    <p><strong>Nama Faskes:</strong> {{ $faskes->nama_faskes }}</p>
                    <p><strong>Jenis Faskes:</strong> {{ $faskes->jenis_faskes }}</p>
                    <p><strong>Alamat:</strong> {{ $faskes->alamat_faskes }}</p>
                    <p><strong>Kecamatan:</strong> {{ $faskes->kecamatan }}</p>
                    <p><strong>Kabupaten:</strong> {{ $faskes->kabupaten }}</p>
                    <p><strong>Provinsi:</strong> {{ $faskes->provinsi }}</p>
                    <p><strong>Kode Pos:</strong> {{ $faskes->kode_pos }}</p>
                    <p><strong>Nomor PIC:</strong> {{ $faskes->nomor_pic ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Username:</strong> {{ $faskes->user->username ?? '-' }}</p>
                    <p><strong>Nama User:</strong> {{ $faskes->user->name ?? '-' }}</p>
                    <p><strong>Role:</strong> {{ $faskes->user->role ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection