@extends('layouts.app')

@section('title', 'Laporan Obat Keluar Per Bulan')

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
    .badge-primary { background-color: #0078D7; color: white; }

    .table thead { background-color: #f8f9fa; font-weight: 600; }
    .table td, .table th { vertical-align: middle; }
    .table-responsive { width: 100%; overflow-x: auto; }

    .empty-text { font-style: italic; color: #777; }

    .filter-section {
        background: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .stat-card {
        background: linear-gradient(135deg, #0078D7, #1888e3);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 32px;
        font-weight: bold;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .obat-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .obat-item:last-child {
        border-bottom: none;
    }

    .obat-name {
        font-weight: 600;
        color: #333;
    }

    .obat-count {
        background: #0078D7;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: bold;
    }

    .progress-bar {
        background-color: #0078D7;
        height: 6px;
        border-radius: 3px;
        margin-top: 8px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-left: 10px;
        border-left: 4px solid #0078D7;
    }

    .chart-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .btn-filter {
        background-color: #0078D7;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s;
    }

    .btn-filter:hover {
        background-color: #005ea6;
        color: white;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .input-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }

    .input-group select, .input-group input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 10px;
        opacity: 0.5;
    }

    
    .pagination {
        margin: 0;
    }

    .pagination .page-link {
        color: #0078D7;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin: 0 3px;
        padding: 8px 12px;
        font-size: 14px;
    }

    .pagination .page-link:hover {
        background-color: #0078D7;
        color: white;
        border-color: #0078D7;
    }

    .pagination .page-item.active .page-link {
        background-color: #0078D7;
        border-color: #0078D7;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #ccc;
        cursor: not-allowed;
    }
</style>

<div class="main-content">
    <div class="header-section">
        <a href="{{ route('apotek.dashboard') }}" class="back-arrow">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="mb-0">Laporan Obat Keluar Per Bulan</h2>
       
    </div>

    <div class="filter-section">
        <form method="GET" action="{{ route('apotek.laporan-obat-keluar') }}" class="row">
            <div class="col-md-3">
                <div class="input-group">
                    <label for="bulan">Pilih Bulan:</label>
                    <select name="bulan" id="bulan" class="form-control">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ $namaBulan[$i] }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <label for="tahun">Pilih Tahun:</label>
                    <select name="tahun" id="tahun" class="form-control">
                        @for ($y = Carbon\Carbon::now()->year - 5; $y <= Carbon\Carbon::now()->year; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-3" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn-filter" style="width: 100%;">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
            <div class="col-md-3" style="display: flex; align-items: flex-end;">
                <a href="{{ route('apotek.laporan-obat-keluar.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn-filter" style="width: 100%; background-color: #28a745; text-decoration: none; text-align: center;">
                    <i class="bi bi-download"></i> Download Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Card -->
    <div class="row">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-label">Total Jenis Obat</div>
                <div class="stat-number">{{ count($obatKeluar) }}</div>
                <div class="stat-label" style="margin-top: 5px;">Jenis obat yang keluar bulan {{ $namaBulan[$bulan] }} {{ $tahun }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-label">Total Obat Keluar</div>
                <div class="stat-number">{{ $totalObat }}</div>
                <div class="stat-label" style="margin-top: 5px;">Total unit obat yang diklaim</div>
            </div>
        </div>
    </div>

    <!-- Obat Keluar -->
    <div class="card-container">
        <div class="card">
            <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #0078D7; padding: 15px;">
                <h5 class="section-title mb-0">Daftar Obat Keluar - {{ $namaBulan[$bulan] }} {{ $tahun }}</h5>
            </div>
            <div class="card-body">
                @if(count($obatKeluar) > 0)
                    @php
                        $maxCount = $obatKeluar->max('total');
                    @endphp
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 60%;">Nama Obat</th>
                                    <th style="width: 20%; text-align: center;">Jumlah</th>
                                    <th style="width: 15%; text-align: center;">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $startNo = ($obatKeluar->currentPage() - 1) * 10 + 1;
                                @endphp
                                @foreach($obatKeluar as $index => $obat)
                                    @php
                                        $percentage = ($obat->total / $totalObat) * 100;
                                    @endphp
                                    <tr>
                                        <td>{{ $startNo + $index }}</td>
                                        <td>{{ $obat->nama_obat }}</td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-primary">{{ $obat->total }}</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 5px;">
                                                <div style="width: 60px; background-color: #eee; border-radius: 3px; overflow: hidden;">
                                                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <span style="font-weight: 600; color: #0078D7; min-width: 50px;">{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($obatKeluar->hasPages())
                        <div style="display: flex; justify-content: center; margin-top: 20px;">
                            {{ $obatKeluar->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="no-data">
                        <i class="bi bi-inbox"></i>
                        <p>Tidak ada data obat yang keluar pada periode ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart 12 Bulan -->
    <div class="chart-container" style="margin-top: 20px;">
        <h5 class="section-title">Tren Obat Keluar 12 Bulan Terakhir</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th style="text-align: center;">Total Obat</th>
                        <th style="text-align: center;">Grafik</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxCountMonthly = max(array_column($obatKeluar12Bulan, 'total'));
                    @endphp
                    @foreach($obatKeluar12Bulan as $data)
                        <tr>
                            <td>{{ $data['bulan'] }} {{ $data['tahun'] }}</td>
                            <td style="text-align: center;">
                                <span class="badge badge-primary">{{ $data['total'] }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 100%; background-color: #eee; border-radius: 3px; overflow: hidden; height: 25px;">
                                        <div class="progress-bar" style="width: {{ $maxCountMonthly > 0 ? ($data['total'] / $maxCountMonthly) * 100 : 0 }}%; height: 100%;"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
