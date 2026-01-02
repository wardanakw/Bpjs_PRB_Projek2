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
                        @foreach($diagnosaTerbanyak as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $d->diagnosa }}</td>
                                <td>{{ number_format(($d->total / max($totalPrbAktif,1)) * 100, 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Reminder --}}
        <div class="col-lg-4">
            <div class="right-panel shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Reminder</h6>
                    <a href="{{ route('fktp.export.reminder') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-download"></i> Download Excel
                    </a>
                </div>

                @forelse($reminder as $r)
                    <div class="patient-card" style="cursor: pointer;" onclick="showReminderDetail('{{ $r->nama_pasien }}', '{{ $r->no_kartu_bpjs }}', '{{ $r->no_telp }}', '{{ $r->diagnosa }}', '{{ $r->tgl_pelayanan }}', '{{ $r->tgl_pelayanan_lanjutan }}', '{{ $r->type }}')">
                        <strong>{{ $r->nama_pasien }}</strong><br>
                        <span class="badge bg-primary">{{ $r->type }}</span> {{ $r->diagnosa }}<br>
                        <small>Tgl Lanjutan: {{ \Carbon\Carbon::parse($r->tgl_pelayanan_lanjutan)->format('d/m/Y') }}</small>
                    </div>
                @empty
                    <p class="text-muted">Tidak ada data reminder.</p>
                @endforelse
            </div>
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


<div class="modal fade" id="reminderDetailModal" tabindex="-1" aria-labelledby="reminderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reminderDetailModalLabel">Detail Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reminderDetailBody">
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection
