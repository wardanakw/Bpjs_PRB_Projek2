@extends('layouts.app')

@section('title', 'Daftar Pasien')

@section('content')

<style>
* { font-family: 'Poppins', sans-serif; }
body { background-color: #f5f7fa; }

.main-content {
    position: relative;
    top: -30px;
    left: -40px;
    padding-right: 15px;
}

.header-section {
    width: calc(100% + 80px);
    margin-bottom: 20px;
}

.card-container {
    width: calc(100% + 80px);
}

.filter-box {
    background: linear-gradient(90deg, #0078D7, #41b7eaff);
    border-radius: 15px;
    padding: 18px 30px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 20px;
    width: calc(100% + 80px);
    position: relative;
}

.left-section {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.search-group {
    width: 220px;
    background: white;
    border-radius: 10px;
    overflow: hidden;
}

.search-group .input-group-text {
    background: white;
    border: none;
    color: #0078D7;
}

.search-group input {
    border: none;
    font-size: 14px;
    padding: 8px 10px;
}

.search-group input:focus {
    outline: none;
    box-shadow: none;
}

.date-filter input[type="date"] {
    border: none;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 14px;
    color: #333;
    width: 160px;
}

.btn-tampilkan {
    background: #0078D7;
    color: white;
    border: none;
    padding: 8px 25px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-tampilkan:hover {
    background: #005ea6;
    color: white;
}

.btn-outline-primary {
    border-color: #0078D7;
    color: #0078D7;
}
.btn-outline-primary:hover {
    background-color: #0078D7;
    color: white;
    border-color: #0078D7;
}

.btn-primary {
    background-color: #0078D7;
    border: none;
    color: white;
}
.btn-primary:hover {
    background-color: #005ea6;
}

.table thead {
    background-color: #f8f9fa;
    font-weight: 600;
}
.table td, .table th { vertical-align: middle; }
.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table th:nth-child(1),
.table td:nth-child(1) { width: 18%; }
.table th:nth-child(2),
.table td:nth-child(2) { width: 18%; }
.table th:nth-child(3),
.table td:nth-child(3) { width: 25%; }
.table th:nth-child(4),
.table td:nth-child(4),
.table th:nth-child(5),
.table td:nth-child(5) {
    width: 12%;
    text-align: center;
}
.table th:nth-child(6),
.table td:nth-child(6) {
    width: 15%;
    text-align: center;
    white-space: nowrap;
}


.empty-text {
    font-style: italic;
    color: #777;
}


.badge {
    border-radius: 10px;
    font-size: 12px;
    padding: 5px 10px;
}
.badge-aktif { background-color: #11871bff; color: white; }
.badge-nonaktif { background-color: #b0b0b0; color: white; }

th.sortable::after {
    content: "⇅";
    font-size: 0.8em;
    color: #aaa;
    margin-left: 6px;
}
th.sortable.asc::after {
    content: "▲";
    color: #000;
}
th.sortable.desc::after {
    content: "▼";
    color: #000;
}

@media (max-width: 768px) {
    .filter-box {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        left: 0;
    }
    .left-section {
        flex-direction: column;
        width: 100%;
    }
    .date-filter {
        width: 100%;
    }
    .btn-tampilkan {
        align-self: flex-end;
        margin-top: 10px;
    }
}

.pagination-wrapper {
    display: flex;
    justify-content: flex-start;
    margin-top: 8px;
}

.pagination {
    margin: 0;
}

.pagination .page-link {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 4px !important;
}

.pagination .page-item.active .page-link {
    background-color: #0078D7;
    border-color: #0078D7;
    color: #fff;
    box-shadow: none;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
}


.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    font-weight: 500;
}

</style>

<div class="main-content" id="mainContent">
<div class="filter-box">
    <form action="{{ route('pasien.index') }}" method="GET" class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="left-section">
            <div class="input-group search-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" name="search" placeholder="Search" class="form-control" value="{{ request('search') }}">
            </div>

            {{-- Filter Role (untuk rumah sakit) --}}
            @if(auth()->user()->role === 'rumah_sakit')
            <div class="input-group search-group">
                <span class="input-group-text">
                    <i class="bi bi-funnel"></i>
                </span>
                <select name="input_role" class="form-select form-control" style="border: none;">
                    <option value="">-- Semua Sumber --</option>
                    <option value="rumah_sakit" {{ request('input_role') === 'rumah_sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                    <option value="fktp" {{ request('input_role') === 'fktp' ? 'selected' : '' }}>FKTP</option>
                    <option value="apotek" {{ request('input_role') === 'apotek' ? 'selected' : '' }}>Apotek</option>
                </select>
            </div>
            @endif

            <div class="date-filter d-flex align-items-center gap-2 flex-wrap">
                <label class="fw-semibold mb-0 text-white">Periode Tanggal s/d</label>
                <input type="date" id="startDate" name="start_date" class="form-control" value="{{ request('start_date') }}">
                <input type="date" id="endDate" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
        </div>

        <button type="submit" class="btn-tampilkan">Tampilkan</button>
    </form>
</div>


    <div class="header-section d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Daftar Pasien</h5>
        <div>
            
         <a href="{{ route('exports.laporan', [
    'start_date' => request('start_date'),
    'end_date' => request('end_date')
]) }}" class="btn btn-outline-primary">
    <i class="bi bi-file-earmark-excel"></i> Export Excel
</a>

            <a href="{{ route('pasien.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Tambah Data
            </a>
        </div>
    </div>


    <div class="card-container">
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-3">
                @if($pasiens->total() > 0)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">
                        Menampilkan {{ $pasiens->firstItem() }} - {{ $pasiens->lastItem() }} dari {{ $pasiens->total() }} data
                    </small>
                    <small class="text-muted">
                        Halaman {{ $pasiens->currentPage() }} dari {{ $pasiens->lastPage() }}
                    </small>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'no_sep', 'direction' => request('direction') === 'asc' && request('sort') === 'no_sep' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                        No SRB
                                        @if(request('sort') === 'no_sep')
                                            <i class="bi bi-chevron-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'no_kartu_bpjs', 'direction' => request('direction') === 'asc' && request('sort') === 'no_kartu_bpjs' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                        No Kartu BPJS
                                        @if(request('sort') === 'no_kartu_bpjs')
                                            <i class="bi bi-chevron-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Diagnosa</th>
                                <th>Status PRB</th>
                                <th>Tgl Pelayanan</th>
                                @if(auth()->user()->role === 'rumah_sakit')
                                <th>Input By</th>
                                @endif
                                @if(auth()->user()->role === 'admin')
                                <th class="text-center">Bukti Bayar Apotek</th>
                                @endif
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pasienTableBody">
                            @forelse ($pasiens as $pasien)
                                @if($pasien->diagnosaPrb->isNotEmpty())
                                    @foreach($pasien->diagnosaPrb as $diagnosa)
                                        <tr>
                                            <td>{{ $diagnosa->no_sep ?? $pasien->no_sep }}</td>
                                            <td>{{ $pasien->no_kartu_bpjs }}</td>
                                            <td>{{ $diagnosa->diagnosa ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $diagnosa->status_prb == 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                                                    {{ $diagnosa->status_prb ?? 'Tidak Aktif' }}
                                                </span>
                                            </td>
                                            <td>{{ $diagnosa->tgl_pelayanan ? \Carbon\Carbon::parse($diagnosa->tgl_pelayanan)->format('d/m/Y') : '-' }}</td>
                                            @if(auth()->user()->role === 'rumah_sakit')
                                            <td>
                                                <span class="badge {{ $pasien->creator->role === 'rumah_sakit' ? 'bg-danger' : ($pasien->creator->role === 'fktp' ? 'bg-primary' : 'bg-success') }}">
                                                    {{ ucfirst($pasien->creator->role ?? 'Unknown') }}
                                                </span>
                                            </td>
                                            @endif
                                            @if(auth()->user()->role === 'admin')
                                            <td class="text-center">
                                                @if($diagnosa->obatPrb->first() && $diagnosa->obatPrb->first()->bukti_bayar_pdf)
                                                    <a href="{{ route('laporan.download.pdf', urlencode($diagnosa->obatPrb->first()->bukti_bayar_pdf)) }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                                        <i class="bi bi-file-pdf"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            @endif
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <a href="{{ route('pasien.show', $pasien->id_pasien) }}"
                                                       class="btn btn-sm btn-outline-primary me-1"
                                                       title="Detail Pasien" aria-label="Detail Pasien">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <a href="{{ route('pasien.edit', $pasien->id_pasien) }}"
                                                       class="btn btn-sm btn-primary"
                                                       title="Edit Pasien" aria-label="Edit Pasien">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                   <tr>
                                    <tr>
                                        <td>{{ $pasien->no_sep ?? '-' }}</td>
                                        <td>{{ $pasien->no_kartu_bpjs }}</td>
                                        <td><span class="empty-text">Belum ada diagnosa</span></td>
                                        <td>
                                            <span class="badge badge-nonaktif">Tidak Aktif</span>
                                        </td>
                                        <td>-</td>
                                        @if(auth()->user()->role === 'rumah_sakit')
                                        <td>
                                            <span class="badge {{ $pasien->creator->role === 'rumah_sakit' ? 'bg-danger' : ($pasien->creator->role === 'fktp' ? 'bg-primary' : 'bg-success') }}">
                                                {{ ucfirst($pasien->creator->role ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        @endif
                                        @if(auth()->user()->role === 'admin')
                                        <td class="text-center"><span class="text-muted">-</span></td>
                                        @endif
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('pasien.show', $pasien->id_pasien) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('pasien.edit', $pasien->id_pasien) }}" class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="{{ (auth()->user()->role === 'admin' || auth()->user()->role === 'rumah_sakit') ? 8 : 7 }}" class="text-center text-muted">Belum ada data pasien</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <nav class="pagination-wrapper" aria-label="Pagination Navigation">
    {{ $pasiens->appends(request()->query())->links('pagination::bootstrap-5') }}
</nav>


                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('exportForm').addEventListener('submit', function (e) {
    document.getElementById('exportStartDate').value = document.getElementById('startDate').value;
    document.getElementById('exportEndDate').value = document.getElementById('endDate').value;
});


document.getElementById('searchInput').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        this.closest('form').submit();
    }
});
</script>
@endsection
