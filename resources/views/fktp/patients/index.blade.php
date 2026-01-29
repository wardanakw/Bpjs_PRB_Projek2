@extends('layouts.app')

@section('title', 'Daftar Pasien FKTP')

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

.header-section { width: calc(100% + 80px); margin-bottom: 20px; }
.card-container { width: calc(100% + 80px); }

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

.btn-detail {
    background-color: #0078D7;
    border: 1px solid #0078D7;
    color: white;
    border-radius: 8px;
    padding: 2px 8px;
    font-size: 12px;
    text-decoration: none;
}
.btn-detail:hover {
    background-color: #005ea6;
    border-color: #005ea6;
    color: white;
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

.btn-tampilkan:hover { background: #005ea6; color: white; }

.btn-primary { background-color: #0078D7; border: none; color: white; }
.btn-primary:hover { background-color: #005ea6; }

.btn-edit {
    background-color: #0078D7;
    border: none;
    color: white;
    border-radius: 8px;
    padding: 3px 12px;
    font-size: 12px;
    text-decoration: none;
}
.btn-edit:hover { background-color: #005ea6; color: white; }

.table thead { background-color: #f8f9fa; font-weight: 600; }
.table td, .table th { vertical-align: middle; }
.table-responsive { width: 100%; overflow-x: auto; }

.badge { border-radius: 10px; font-size: 12px; padding: 5px 10px; }
.badge-aktif { background-color: #11871bff; color: white; }
.badge-nonaktif { background-color: #b0b0b0; color: white; }

.empty-text { font-style: italic; color: #777; }

/* Pagination Styling */
.pagination-wrapper {
    display: flex;
    justify-content: flex-start;
    margin-top: 8px;
}

.pagination {
    margin: 0;
}

.pagination .page-link {
    color: #0078D7;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px !important;
    margin: 0 2px;
    padding: 8px 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    color: #005ea6;
    background-color: #f8f9fa;
    border-color: #0078D7;
}

.pagination .page-item.active .page-link {
    background-color: #0078D7;
    border-color: #0078D7;
    color: white;
    box-shadow: 0 2px 4px rgba(0,120,215,0.3);
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    font-weight: 500;
}
</style>

<div class="main-content" id="mainContent">
<div class="filter-box">
    <form action="{{ auth()->user()->role === 'apotek' ? route('apotek.patients.index') : route('fktp.patients.index') }}" method="GET" class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="left-section">
            <div class="input-group search-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" name="search" placeholder="Search" class="form-control" value="{{ request('search') }}">
            </div>

            <div class="date-filter d-flex align-items-center gap-2 flex-wrap">
                <label class="fw-semibold mb-0 text-white">Periode Tanggal s/d</label>
                <input type="date" id="startDate" name="start_date" class="form-control" value="{{ request('start_date') }}">
                <input type="date" id="endDate" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-tampilkan">Tampilkan</button>
            <a href="{{ auth()->user()->role === 'apotek' ? route('apotek.patients.index') : route('fktp.patients.index') }}" class="btn btn-sm btn-secondary" style="padding: 8px 25px; border-radius: 25px; font-weight: 600;">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>
</div>


    <div class="header-section d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Daftar Pasien</h5>
        <div>
           <a href="{{ route('exports.laporan') }}" target="_blank" class="btn btn-outline-primary me-2">
    <i class="bi bi-printer"></i> Cetak Laporan
</a>
        </div>
    </div>


    <div class="card-container">
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-3">
                @if($patients->total() > 0)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">
                        Menampilkan {{ $patients->firstItem() }} - {{ $patients->lastItem() }} dari {{ $patients->total() }} data
                    </small>
                    <small class="text-muted">
                        Halaman {{ $patients->currentPage() }} dari {{ $patients->lastPage() }}
                    </small>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table align-middle sortable-table">
                        <thead>
                            <tr>
                                <th class="sortable">No SRB</th>
                                <th class="sortable">No Kartu BPJS</th>
                                <th class="sortable">Diagnosa</th>
                                <th class="sortable">Status PRB</th>
                                <th class="sortable">Tgl Pelayanan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pasienTableBody">
                           @forelse ($patients as $patient)
    @php
        if (auth()->user()->role === 'apotek') {
            $diagnosas = $patient->diagnosaPrb->filter(function($d) {
                return $d->obatPrb && $d->obatPrb->where('is_klaim', false)->count() > 0;
            });
        } else {
            $diagnosas = $patient->diagnosaPrb;
        }
    @endphp
    @if($diagnosas->isNotEmpty())
        @foreach($diagnosas as $diagnosa)
            <tr>
                <td>{{ $diagnosa->no_sep ?? $patient->no_sep }}</td>
                <td>{{ $patient->no_kartu_bpjs }}</td>
                <td>{{ $diagnosa->diagnosa ?? '-' }}</td>
                <td>
                    <span class="badge {{ $diagnosa->status_prb == 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                        {{ $diagnosa->status_prb ?? 'Tidak Aktif' }}
                    </span>
                </td>
                <td>{{ $diagnosa->tgl_pelayanan ? \Carbon\Carbon::parse($diagnosa->tgl_pelayanan)->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    <a href="{{ (auth()->user()->role === 'apotek') ? route('apotek.patients.show', $patient->id_pasien) : route('fktp.patients.show', $patient->id_pasien) }}" class="btn-detail btm-sm"> Detail</a>
                    @if(auth()->user()->role === 'apotek')
                        @php
                            // Cek apakah ada obat belum diklaim pada diagnosa ini
                            $obatBelumKlaim = $diagnosa->obatPrb->where('is_klaim', false)->count();
                        @endphp
                        @if($obatBelumKlaim > 0)
                            @if(!empty($diagnosa->obatPrb->where('is_klaim', false)->first()->bukti_bayar_pdf))
                                <form action="{{ route('apotek.diagnosa.klaim', $diagnosa->id_diagnosa) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm" style="background-color: #4CAF50; color: white; border: none; border-radius: 8px; padding: 3px 12px;" onclick="return confirm('Apakah Anda yakin ingin klaim semua obat untuk pasien ini?')">
                                        <i class="bi bi-check-circle"></i> Klaim ({{ $obatBelumKlaim }} obat)
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-warning openUploadModal" data-diagnosa-id="{{ $diagnosa->id_diagnosa }}" data-pasien-name="{{ $patient->nama_pasien }}">
                                    <i class="bi bi-upload"></i> Upload PDF
                                </button>
                            @endif
                        @else
                            <span class="badge bg-secondary">Sudah Diklaim</span>
                        @endif
                    @else
                        <a href="{{ route('fktp.patients.edit', $patient->id_pasien) }}" class="btn-edit btn-sm">Edit</a>
                    @endif
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td>{{ $patient->no_sep ?? '-' }}</td>
            <td>{{ $patient->no_kartu_bpjs }}</td>
            <td><span class="empty-text">Belum ada diagnosa</span></td>
            <td><span class="badge badge-nonaktif">Tidak Aktif</span></td>
            <td>-</td>
                <td class="text-center">
                    @if(auth()->user()->role !== 'apotek')
                        <a href="{{ route('fktp.patients.edit', $patient->id_pasien) }}" class="btn-edit btn-sm">Edit</a>
                    @endif
            </td>
        </tr>
    @endif
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">Belum ada data pasien</td>
    </tr>
@endforelse

                        </tbody>
                    </table>
                    <nav class="pagination-wrapper" aria-label="Pagination Navigation">
                        {{ $patients->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function () {
    const tables = document.querySelectorAll(".sortable-table");

    tables.forEach(table => {
        table.querySelectorAll("th.sortable").forEach((header, columnIndex) => {
            header.style.cursor = "pointer";

            header.addEventListener("click", () => {
                const rows = Array.from(table.querySelectorAll("tbody tr"));

                table.querySelectorAll("th.sortable").forEach(th => {
                    if (th !== header) th.classList.remove("asc", "desc");
                });

                const isAsc = !header.classList.contains("asc");
                header.classList.toggle("asc", isAsc);
                header.classList.toggle("desc", !isAsc);

                const direction = isAsc ? 1 : -1;

                const sortedRows = rows.sort((a, b) => {
                    const aText = a.children[columnIndex].innerText.trim().toLowerCase();
                    const bText = b.children[columnIndex].innerText.trim().toLowerCase();

                    const aVal = parseFloat(aText) || Date.parse(aText) || aText;
                    const bVal = parseFloat(bText) || Date.parse(bText) || bText;

                    if (aVal > bVal) return direction;
                    if (aVal < bVal) return -direction;
                    return 0;
                });

                const tbody = table.querySelector("tbody");
                tbody.innerHTML = "";
                sortedRows.forEach(row => tbody.appendChild(row));
            });
        });
    });
});

document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#pasienTableBody tr");

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>
@endsection