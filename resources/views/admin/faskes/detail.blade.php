@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-2">{{ $faskes->nama_faskes }}</h3>
            <p class="text-muted mb-0">
                <i class="bi bi-geo-alt"></i> {{ $faskes->alamat_faskes }}
            </p>
        </div>
        <a href="{{ route('admin.faskes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama Faskes:</strong> {{ $faskes->nama_faskes }}</p>
                            <p><strong>Jenis Faskes:</strong> {{ $faskes->jenis_faskes ?? '-' }}</p>
                            <p><strong>Kode Faskes:</strong> {{ $faskes->kode_faskes ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Username:</strong> {{ $faskes->user->username ?? '-' }}</p>
                            <p><strong>Kecamatan:</strong> {{ $faskes->kecamatan ?? '-' }}</p>
                            <p><strong>Kabupaten:</strong> {{ $faskes->kabupaten ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Pasien {{ $faskes->nama_faskes }}</h5>
        </div>
        <div class="card-body">
            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.faskes.detail', $faskes->id) }}" class="row g-3">
                        <div class="col-md-5">
                            <label for="search" class="form-label">Cari Nama/No Pasien</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="No SRB, No BPJS, atau Nama..." 
                                   value="{{ request('search') }}">
                        </div>

                        <div class="col-md-4">
                            <label for="input_role" class="form-label">Filter Input By</label>
                            <select class="form-select" id="input_role" name="input_role">
                                <option value="">-- Semua Sumber --</option>
                                <option value="rumah_sakit" {{ request('input_role') === 'rumah_sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                                <option value="fktp" {{ request('input_role') === 'fktp' ? 'selected' : '' }}>FKTP</option>
                                <option value="apotek" {{ request('input_role') === 'apotek' ? 'selected' : '' }}>Apotek</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <a href="{{ route('admin.faskes.detail', $faskes->id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No SRB</th>
                            <th>No Kartu BPJS</th>
                            <th>Nama Pasien</th>
                            <th>Diagnosa</th>
                            <th>Status</th>
                            <th>Input By</th>
                            <th>Tgl Pelayanan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasiens as $pasien)
                            @if($pasien->diagnosaPrb->isNotEmpty())
                                @foreach($pasien->diagnosaPrb as $diagnosa)
                                    <tr>
                                        <td><strong>{{ $diagnosa->no_sep ?? $pasien->no_sep }}</strong></td>
                                        <td>{{ $pasien->no_kartu_bpjs }}</td>
                                        <td>{{ $pasien->nama_pasien }}</td>
                                        <td>{{ $diagnosa->diagnosa ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $diagnosa->status_prb === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $diagnosa->status_prb ?? 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $pasien->creator->role === 'rumah_sakit' ? 'bg-danger' : ($pasien->creator->role === 'fktp' ? 'bg-primary' : 'bg-warning') }}">
                                                {{ ucfirst($pasien->creator->role ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td>{{ $diagnosa->tgl_pelayanan ? \Carbon\Carbon::parse($diagnosa->tgl_pelayanan)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('pasien.show', $pasien->id_pasien) }}" class="btn btn-sm btn-info" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><strong>{{ $pasien->no_sep ?? '-' }}</strong></td>
                                    <td>{{ $pasien->no_kartu_bpjs }}</td>
                                    <td>{{ $pasien->nama_pasien }}</td>
                                    <td><span class="text-muted"><em>Belum ada diagnosa</em></span></td>
                                    <td>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $pasien->creator->role === 'rumah_sakit' ? 'bg-danger' : ($pasien->creator->role === 'fktp' ? 'bg-primary' : 'bg-warning') }}">
                                            {{ ucfirst($pasien->creator->role ?? 'Unknown') }}
                                        </span>
                                    </td>
                                    <td>-</td>
                                    <td class="text-center">
                                        <a href="{{ route('pasien.show', $pasien->id_pasien) }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox"></i> Tidak ada data pasien ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $pasiens->count() }}</h5>
                            <small class="text-muted">Total Pasien</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $pasiens->filter(fn($p) => $p->creator->role === 'rumah_sakit')->count() }}</h5>
                            <small class="text-muted">Input Rumah Sakit</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $pasiens->filter(fn($p) => $p->creator->role === 'fktp')->count() }}</h5>
                            <small class="text-muted">Input FKTP</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $pasiens->filter(fn($p) => $p->creator->role === 'apotek')->count() }}</h5>
                            <small class="text-muted">Input Apotek</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive {
        border-radius: 0.25rem;
    }
</style>
@endsection
