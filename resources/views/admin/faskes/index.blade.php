@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Kelola Fasilitas Kesehatan</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFaskesModal">
            + Tambah Faskes
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Search Form --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.faskes.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Cari Nama/Kode Faskes</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Cari nama rumah sakit, FKTP, atau Apotek..." 
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-4">
                    <label for="role" class="form-label">Filter Berdasarkan Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">-- Semua Role --</option>
                        <option value="rumah_sakit" {{ request('role') === 'rumah_sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                        <option value="fktp" {{ request('role') === 'fktp' ? 'selected' : '' }}>FKTP</option>
                        <option value="apotek" {{ request('role') === 'apotek' ? 'selected' : '' }}>Apotek</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.faskes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Alamat</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($faskes as $item)
                <tr>
                    <td>{{ $item->kode_faskes ?? '-' }}</td>
                    <td>{{ $item->nama_faskes }}</td>
                   <td>
                        <span class="badge bg-{{ $item->user->role === 'rumah_sakit' ? 'danger' : ($item->user->role === 'fktp' ? 'primary' : 'success') }}">
                            {{ $item->user->role ?? 'Tidak ada user' }}
                        </span>
                    </td>

                    <td>{{ $item->alamat_faskes }}</td>
                    <td>{{ $item->user->username ?? 'Tidak ada username' }}</td>
                    <td>
                        @if($item->user->role === 'rumah_sakit')
                            <a href="{{ route('admin.faskes.detail', $item->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        @endif
                        <button class="btn btn-sm btn-info" disabled>Edit</button>
                 <form action="{{ route('admin.faskes.destroy', $item->id) }}" 
      method="POST" class="d-inline"
      onsubmit="return confirm('Yakin ingin menghapus faskes ini?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">Hapus</button>
</form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-3">
                        <span class="text-muted">Tidak ada data faskes ditemukan</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Create --}}
@include('admin.faskes.modal-create')

@endsection
