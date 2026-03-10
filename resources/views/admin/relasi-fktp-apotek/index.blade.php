@extends('layouts.app')

@section('content')
<div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Kelola Relasi FKTP - Apotek</h4>
        <div>
            <a href="{{ route('admin.relasi-fktp-apotek.create', ['mode' => 'apotek']) }}" class="btn btn-primary me-2">
                + Tambah Apotek
            </a>
            <a href="{{ route('admin.relasi-fktp-apotek.create', ['mode' => 'fktp']) }}" class="btn btn-success">
                + Tambah FKTP
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.relasi-fktp-apotek.index') }}" class="row g-2 mb-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari relasi FKTP/Apotek..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">Cari</button>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('admin.relasi-fktp-apotek.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <p class="mb-2 text-muted">
                Menampilkan {{ $relasis->firstItem() ?? 0 }} - {{ $relasis->lastItem() ?? 0 }} dari {{ $relasis->total() }} data.
            </p>

            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Kode FKTP</th>
                        <th>Nama FKTP</th>
                        <th>Nama Apotek</th>
                        <th>Kode Apotek</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($relasis as $relasi)
                        <tr>
                            <td>{{ $relasi->kode_fktp }}</td>
                            <td>{{ $relasi->nama_fktp }}</td>
                            <td>{{ $relasi->nama_apotek }}</td>
                            <td>{{ $relasi->kode_apotek }}</td>
                            <td>
                                <form action="{{ route('admin.relasi-fktp-apotek.destroy', $relasi->id_relasi) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus relasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">
                                <span class="text-muted">Tidak ada data relasi ditemukan</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Halaman {{ $relasis->currentPage() }} dari {{ $relasis->lastPage() }} ({{ $relasis->perPage() }} per halaman)</small>
                <nav>
                    {{ $relasis->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection