@extends('layouts.app')

@section('title', 'Dashboard ' . $rumahSakit->nama_faskes)

@section('content')

<div class="main-content px-3 py-4" style="margin-left: -10px; padding-right: 10px;">

  
  <div class="alert alert-info mb-4" style="background-color: #e7f3ff; border-left: 4px solid #0078D7; border-radius: 5px;">
    <h5 style="color: #0078D7; margin-bottom: 5px;"><i class="bi bi-hospital"></i> {{ $rumahSakit->nama_faskes }}</h5>
    <p style="color: #666; margin: 0; font-size: 13px;">
      <strong>Kode:</strong> {{ $rumahSakit->kode_faskes }} | 
      <strong>Jenis:</strong> {{ $rumahSakit->jenis_faskes }} | 
      <strong>Alamat:</strong> {{ $rumahSakit->alamat_faskes }}
    </p>
  </div>

  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card p-3" style="background: linear-gradient(135deg, #0078D7, #1888e3); color: white; border: none;">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="mb-2" style="font-size: 12px; opacity: 0.9;">Total Pasien</p>
            <h3 class="mb-0" id="totalPasienCard">{{ $totalPasien }}</h3>
          </div>
          <i class="bi bi-people" style="font-size: 32px; opacity: 0.7;"></i>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3" style="background: linear-gradient(135deg, #28a745, #3da85e); color: white; border: none;">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="mb-2" style="font-size: 12px; opacity: 0.9;">Total Diagnosa</p>
            <h3 class="mb-0" id="totalDiagnosaCard">{{ $totalDiagnosa }}</h3>
          </div>
          <i class="bi bi-clipboard-check" style="font-size: 32px; opacity: 0.7;"></i>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3" style="background: linear-gradient(135deg, #ff9800, #ffa500); color: white; border: none;">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="mb-2" style="font-size: 12px; opacity: 0.9;">Kunjungan Bulan Ini</p>
            <h3 class="mb-0" id="totalKunjunganCard">{{ $totalKunjunganBulanIni }}</h3>
          </div>
          <i class="bi bi-calendar-check" style="font-size: 32px; opacity: 0.7;"></i>
        </div>
      </div>
    </div>
  </div>

 
  <div class="row">
    <div class="col-lg-8">
      <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-semibold mb-0">Kunjungan Setiap Bulan ({{ date('Y') }})</h6>
          <span class="badge bg-primary">Tahun {{ date('Y') }}</span>
        </div>
        <div class="chart-container" style="height: 280px;">
          <canvas id="chartMonth"></canvas>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-semibold mb-0">Kunjungan Setiap Minggu</h6>
          <small class="text-muted">
            {{ Carbon\Carbon::now()->startOfWeek()->format('d M') }} - 
            {{ Carbon\Carbon::now()->endOfWeek()->format('d M') }}
          </small>
        </div>
        <div class="chart-container" style="height: 280px;">
          <canvas id="chartWeek"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card p-4">
        <h6 class="fw-semibold mb-3">Diagnosa Terbanyak Bulan {{ date('F') }}</h6>
        @if(count($diagnosaChart) > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th width="10%">No</th>
                <th>Diagnosa</th>
                <th width="20%">Jumlah</th>
                <th width="20%">Persentase</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($diagnosaChart as $index => $item)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['diagnosa'] }}</td>
                <td>{{ $item['total'] }}</td>
                <td>
                  <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: {{ $item['persen'] }}%;" 
                         aria-valuenow="{{ $item['persen'] }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                      {{ $item['persen'] }}%
                    </div>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <i class="bi bi-clipboard-x text-muted" style="font-size: 48px;"></i>
          <p class="text-muted mt-2">Belum ada data diagnosa untuk bulan ini</p>
        </div>
        @endif
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card p-4">
        <h6 class="fw-semibold mb-3">Data PRB Terbaru</h6>
        @if($dataPrbTerbaru->count() > 0)
        <div style="max-height: 300px; overflow-y: auto;">
          <div class="list-group list-group-flush">
            @foreach ($dataPrbTerbaru as $item)
            <div class="list-group-item border-0 px-0 py-3">
              <div class="d-flex justify-content-between align-items-start mb-1">
                <div>
                  <h6 class="mb-0" style="font-size: 14px;">{{ $item->nama_pasien }}</h6>
                  <small class="text-muted">{{ $item->no_kartu_bpjs }}</small>
                </div>
                <span class="badge 
                  @if($item->status_prb == 'Disetujui') bg-success
                  @elseif($item->status_prb == 'Pengajuan') bg-warning text-dark
                  @else bg-danger
                  @endif">
                  {{ $item->status_prb }}
                </span>
              </div>
              <p class="mb-1" style="font-size: 13px;">{{ Str::limit($item->diagnosa, 40) }}</p>
              <small class="text-muted">
                <i class="bi bi-calendar"></i> 
                {{ \Carbon\Carbon::parse($item->tgl_pelayanan)->format('d-m-Y') }}
              </small>
            </div>
            @endforeach
          </div>
        </div>
        @else
        <div class="text-center py-4">
          <i class="bi bi-file-earmark-text text-muted" style="font-size: 48px;"></i>
          <p class="text-muted mt-2">Belum ada data PRB terbaru</p>
        </div>
        @endif
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

const initialMonthData = @json($kunjunganPerBulanChart);
const initialWeekData = @json($chartMinggu);

let chartMonth = null;
let chartWeek = null;

function initCharts() {
    
    const ctxMonth = document.getElementById('chartMonth');
    if (ctxMonth) {
        chartMonth = new Chart(ctxMonth, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: initialMonthData,
                    borderColor: '#0078D7',
                    backgroundColor: 'rgba(0, 120, 215, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#0078D7',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

  
    const ctxWeek = document.getElementById('chartWeek');
    if (ctxWeek) {
        chartWeek = new Chart(ctxWeek, {
            type: 'bar',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [{
                    label: 'Kunjungan',
                    data: initialWeekData,
                    backgroundColor: '#28a745',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
}

function updateDashboard() {
    fetch('{{ route("rumahsakit.dashboard.data") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
            
                document.getElementById('totalPasienCard').textContent = data.totalPasien;
                document.getElementById('totalDiagnosaCard').textContent = data.totalDiagnosa;
                document.getElementById('totalKunjunganCard').textContent = data.totalKunjunganBulanIni;

               
                if (chartMonth) {
                    chartMonth.data.datasets[0].data = data.kunjunganPerBulan;
                    chartMonth.update();
                }

               
                if (chartWeek) {
                    chartWeek.data.datasets[0].data = data.kunjunganPerMinggu;
                    chartWeek.update();
                }
            }
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    initCharts();
    
    
    setInterval(updateDashboard, 30000);
});
</script>

<style>
.progress {
    border-radius: 10px;
}
.progress-bar {
    border-radius: 10px;
    font-size: 12px;
    line-height: 20px;
}
.list-group-item {
    border-left: none;
    border-right: none;
}
.list-group-item:first-child {
    border-top: none;
}
.list-group-item:last-child {
    border-bottom: none;
}
</style>

@endsection