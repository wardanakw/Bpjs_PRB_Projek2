@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<style>
body {
    background: #f4f7fe;
    font-family: 'Poppins', sans-serif;
}

.patient-card:hover {
    background-color: transparent !important;
    box-shadow: none !important;
}

.card-stat {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    font-weight: 600;
    font-size: 18px;
    color: #333;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    transition: transform .2s;
}
.card-stat:hover { transform: translateY(-4px); }
.card-stat small {
    display: block;
    font-size: 13px;
    color: #888;
    font-weight: 400;
}

.bg-white.rounded {
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.right-panel {
    background: #fff;
    border-radius: 20px;
    padding: 20px;
    height: 100%;
    overflow-y: auto;
    max-height: 85vh;
}
.right-panel h6 {
    font-weight: 600;
    color: #0078D7;
}
.right-panel::-webkit-scrollbar { width: 6px; }
.right-panel::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
}

.patient-card {
    border-radius: 12px;
    padding: 10px 15px;
    margin-top: 8px;
    background: #f7faff;
    border: 1px solid #e0e7ff;
    transition: all .3s ease;
}
.patient-card:hover { background: #e9f3ff; }
.patient-card.active {
    background: #0078D7;
    color: #fff;
}
.patient-card strong { font-size: 15px; }
.patient-card small { color: #aaa; }

h6 {
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}
.small-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.small-card:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.small-card.active-filter {
    background: #0078D7;
    color: white;
    border-color: #0056b3;
}

.small-card.active-filter small {
    color: rgba(255,255,255,0.9);
}

.reminder-list {
    max-height: 400px;
    overflow-y: auto;
}

/* Warna untuk badge reminder */
.badge.bg-info { background-color: #17a2b8 !important; }
.badge.bg-warning { background-color: #ffc107 !important; color: #212529 !important; }
.badge.bg-danger { background-color: #dc3545 !important; }
</style>

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col">
            <h4 class="mb-0">Dashboard FKTP</h4>
        
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4 text-center">
        <div class="col">
            <div class="card-stat">
                {{ $totalPrbAktif }}
                <small>PRB Aktif</small>
            </div>
        </div>
        <div class="col">
            <div class="card-stat">
                {{ $totalPasien }}
                <small>Jumlah Pasien</small>
            </div>
        </div>
        <div class="col">
            <div class="card-stat">
                {{ $totalDiagnosa }}
                <small>Total Diagnosa</small>
            </div>
        </div>
        <div class="col">
            <div class="card-stat">
                {{ $totalObat }}
                <small>Jumlah Obat PRB</small>
            </div>
        </div>
        <div class="col">
            <div class="card-stat">
                {{ $resepBulanIni }}
                <small>Resep Bulan Ini</small>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Chart dan Diagnosa --}}
        <div class="col-lg-8">
            <div class="bg-white rounded p-4 mb-4">
                <h6>Kunjungan Setiap Bulan</h6>
                <canvas id="chartKunjungan" height="120"></canvas>
            </div>

            <div class="bg-white rounded p-4">
                <h6>Diagnosa Terbanyak</h6>
                <table class="table table-borderless align-middle">
                    <thead>
                        <tr>
                            <th style="width: 10%">No</th>
                            <th>Diagnosa</th>
                            <th style="width: 20%">%</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse ($reminder as $r)
        <tr>
            <td>{{ $r->nama_pasien }}</td>
            <td>{{ $r->diagnosa }}</td>
            <td>{{ \Carbon\Carbon::parse($r->tgl_pelayanan_lanjutan)->format('d/m/Y') }}</td>
            <td>
                @if ($r->days_left == 0)
                    <span class="badge bg-danger">H-0</span>
                @else
                    <span class="badge bg-warning">
                        H-{{ $r->days_left }}
                    </span>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center text-muted">
                Tidak ada reminder (H-5 s/d H-0)
            </td>
        </tr>
    @endforelse
</tbody>

                </table>
            </div>
        </div>

       {{-- Reminder Section --}}
<div class="col-lg-4">
    <div class="right-panel shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Reminder</h6>
            <div class="d-flex align-items-center">
                {{-- Filter Dropdown --}}
                <div class="dropdown me-2">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        @if($reminderFilter == 'all')
                            Semua (H-5 sampai H+1)
                        @elseif($reminderFilter == 'h0')
                            Hari Ini (H-0)
                        @else
                            H-{{ str_replace('h', '', $reminderFilter) }}
                        @endif
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ $reminderFilter == 'all' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['reminder_filter' => 'all']) }}">
                            Semua (H-5 sampai H+1)
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item {{ $reminderFilter == 'h5' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['reminder_filter' => 'h5']) }}">
                            H-5 (5 hari lagi)
                        </a></li>
                        <li><a class="dropdown-item {{ $reminderFilter == 'h4' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['reminder_filter' => 'h4']) }}">
                            H-4 (4 hari lagi)
                        </a></li>
                        <li><a class="dropdown-item {{ $reminderFilter == 'h3' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['reminder_filter' => 'h3']) }}">
                            H-3 (3 hari lagi)
                        </a></li>
                        <li><a class="dropdown-item {{ $reminderFilter == 'h2' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['reminder_filter' => 'h2']) }}">
                            H-2 (2 hari lagi)
                        </a></li>
                        <li><a class="dropdown-item {{ $reminderFilter == 'h1' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['reminder_filter' => 'h1']) }}">
                            H-1 (1 hari lagi)
                        </a></li>
                        <li><a class="dropdown-item {{ $reminderFilter == 'h0' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['reminder_filter' => 'h0']) }}">
                            Hari Ini (H-0)
                        </a></li>
                    </ul>
                </div>
                
                {{-- Export Button dengan filter yang aktif --}}
                <a href="{{ route('fktp.export.reminder', ['filter' => $reminderFilter]) }}" 
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-download"></i> Excel
                </a>
            </div>
        </div>

        {{-- Counter untuk setiap kategori --}}
        <div class="row g-2 mb-3 text-center">
            @php
                $counts = [
                    'h5' => 0, 'h4' => 0, 'h3' => 0, 
                    'h2' => 0, 'h1' => 0, 'h0' => 0
                ];
                
                foreach($reminder as $r) {
                    if($r->days_left == 5) $counts['h5']++;
                    elseif($r->days_left == 4) $counts['h4']++;
                    elseif($r->days_left == 3) $counts['h3']++;
                    elseif($r->days_left == 2) $counts['h2']++;
                    elseif($r->days_left == 1) $counts['h1']++;
                    elseif($r->days_left === 0) $counts['h0']++;

                }
            @endphp
            
            @foreach(['h5', 'h4', 'h3', 'h2', 'h1', 'h0'] as $filterKey)
                <div class="col">
                    <div class="small-card {{ $reminderFilter == $filterKey ? 'active-filter' : '' }}" 
                         onclick="location.href='{{ request()->fullUrlWithQuery(['reminder_filter' => $filterKey]) }}'">
                        <div class="fw-bold">{{ $counts[$filterKey] }}</div>
                        <small>
                            @if($filterKey == 'h0')
                                H-0
                            @else
                                H-{{ str_replace('h', '', $filterKey) }}
                            @endif
                        </small>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- List Reminder --}}
        <div class="reminder-list">
            @forelse($reminder as $r)
                @php
                    $bgClass = 'bg-info';
                    if($r->days_left <= 0) $bgClass = 'bg-warning';
                    if($r->days_left <= -1) $bgClass = 'bg-danger';
                @endphp
                
                <div class="patient-card" >
                        <div>
                            <strong>{{ $r->nama_pasien }}</strong><br>
                            <small class="text-muted">{{ $r->diagnosa }}</small><br>
                            <small>Tgl Lanjutan: {{ \Carbon\Carbon::parse($r->tgl_pelayanan_lanjutan)->format('d/m/Y') }}</small>
                        </div>
                        <span class="badge {{ $bgClass }}">{{ $r->type }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="bi bi-bell-slash display-6 text-muted"></i>
                    <p class="text-muted mt-2">Tidak ada reminder untuk filter ini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById("chartKunjungan");

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        datasets: [{
            label: 'Jumlah Kunjungan',
            data: @json($chartData),
            backgroundColor: '#0078D7',
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 5 } } }
    }
});


setInterval(function() {
    location.reload();
}, 300000); 
</script>

<script>
function showReminderDetail(nama, bpjs, telp, diagnosa, tglAwal, tglLanjutan, type) {
    const modalBody = document.getElementById('reminderDetailBody');
    modalBody.innerHTML = `
        <p><strong>Nama Pasien:</strong> ${nama}</p>
        <p><strong>No Kartu BPJS:</strong> ${bpjs}</p>
        <p><strong>No Telepon:</strong> ${telp}</p>
        <p><strong>Diagnosa:</strong> ${diagnosa}</p>
        <p><strong>Tgl Pelayanan Awal:</strong> ${tglAwal}</p>
        <p><strong>Tgl Pelayanan Lanjutan:</strong> ${tglLanjutan}</p>
    `;
    const modal = new bootstrap.Modal(document.getElementById('reminderDetailModal'));
    modal.show();
}
</script>



@endsection
