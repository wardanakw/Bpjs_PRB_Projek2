@extends('layouts.app')

@section('title', 'Riwayat Obat Diklaim')

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

.back-arrow {
    font-size: 28px;
    color: #0078D7;
    text-decoration: none;
    margin-bottom: 15px;
    display: inline-block;
}
.back-arrow:hover {
    color: #005ea6;
}

.badge { border-radius: 10px; font-size: 12px; padding: 5px 10px; }
.badge-klaim { background-color: #11871bff; color: white; }

.table thead { background-color: #f8f9fa; font-weight: 600; }
.table td, .table th { vertical-align: middle; }
.table-responsive { width: 100%; overflow-x: auto; }

.empty-text { font-style: italic; color: #777; }

/* Pagination Styling */
.pagination {
    margin: 20px 0;
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
</style>

<div class="main-content" id="mainContent">
    <div class="mb-3">
        <a href="javascript:history.back()" class="back-arrow">
            <i class="bi bi-arrow-90deg-left"></i>
        </a>
    </div>

    <div class="header-section">
        <h5 class="fw-bold mb-0">Riwayat Obat yang Diklaim</h5>
    </div>

    
    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" action="{{ route('apotek.obat.riwayat-klaim') }}" class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari nama pasien, no kartu BPJS, atau nama obat..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('apotek.obat.riwayat-klaim') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if(request('search'))
        <div class="mb-3">
            <p class="text-muted">Menampilkan {{ $obatKlaim->count() }} dari {{ $obatKlaim->total() }} hasil untuk "<strong>{{ request('search') }}</strong>"</p>
        </div>
    @endif

    <div class="card-container">
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-3">
                @if($obatKlaim->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No Pasien</th>
                                    <th>Nama Pasien</th>
                                    <th>No Kartu BPJS</th>
                                    <th>Nama Obat</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Dosis</th>
                                    <th>Aturan Pakai</th>
                                    <th>Tanggal Klaim</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($obatKlaim as $obat)
                                    <tr>
                                        <td>{{ $obat->diagnosaPrb->patient->no_sep }}</td>
                                        <td>{{ $obat->diagnosaPrb->patient->nama_pasien }}</td>
                                        <td>{{ $obat->diagnosaPrb->patient->no_kartu_bpjs }}</td>
                                        <td><strong>{{ $obat->nama_obat }}</strong></td>
                                        <td>{{ $obat->jumlah_obat ?? '-' }}</td>
                                        <td>{{ $obat->satuan ?? 'tablet' }}</td>
                                        <td>{{ $obat->dosis_obat ?? '-' }}</td>
                                        <td>{{ $obat->aturan_pakai ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-klaim">
                                                {{ \Carbon\Carbon::parse($obat->tanggal_klaim)->format('d M Y H:i') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

    
                    <div class="mt-4">
                        {{ $obatKlaim->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #ddd;"></i><br>
                            Belum ada obat yang diklaim
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
