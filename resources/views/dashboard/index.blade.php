@extends('layouts.app')

@section('title', 'Dashboard RUBICON+')

@section('content')

<div class="main-content px-3 py-4" style="margin-left: -10px; padding-right: 10px;">

  <div class="card p-4 mb-4">
    <h6 class="fw-semibold mb-3">Kunjungan Setiap Bulan</h6>
    <div class="chart-container" style="height: 280px;">
      <canvas id="chartMonth"></canvas>
    </div>
  </div>

  <div class="row g-3">
  
    <div class="col-lg-8">
      <div class="card p-4">
        <h6 class="fw-semibold mb-3">Diagnosa Terbanyak</h6>
        <table class="table table-borderless align-middle mb-0">
          <thead>
            <tr><th>No</th><th>Diagnosa</th><th>%</th></tr>
          </thead>
          <tbody>
            @forelse ($diagnosaChart as $index => $item)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $item['diagnosa'] }}</td>
              <td>{{ $item['persen'] }}%</td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted">Belum ada data bulan ini</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    
    <div class="col-lg-4">
      <div class="card p-4">
        <h6 class="fw-semibold mb-3">Kunjungan Setiap Minggu</h6>
        <div class="chart-container week-chart">
    <canvas id="chartWeek"></canvas>
</div>
      </div>
    </div>
  </div>
  <div class="card p-3 mt-4">
  <h6 class="fw-semibold mb-3">Data PRB Terbaru</h6>
  <table class="table table-striped align-middle mb-0">
    <thead>
      <tr>
        <th>ID PRB</th>
        <th>No Kartu BPJS</th>
        <th>Diagnosa</th>
        <th>Status PRB</th>
        <th>Tgl Pelayanan</th>
      </tr>
    </thead>
    <tbody>
  @forelse($dataPrbTerbaru as $data)
  <tr>
    <td>{{ $data->id_diagnosa }}</td>
    <td>{{ $data->no_kartu_bpjs }}</td>
    <td>{{ $data->diagnosa }}</td>
    <td>
      <span class="badge {{ $data->status_prb == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
        {{ $data->status_prb }}
      </span>
    </td>
    <td>{{ \Carbon\Carbon::parse($data->tgl_pelayanan)->format('d M Y') }}</td>
  </tr>
  @empty
  <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data PRB</td></tr>
  @endforelse
</tbody>

  </table>
</div>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

  const ctx1 = document.getElementById('chartMonth').getContext('2d');
  const monthData = @json(array_values($kunjunganPerBulan));
  const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

  new Chart(ctx1, {
      type: 'bar',
      data: {
          labels: monthLabels,
          datasets: [{
              label: 'Kunjungan',
              data: monthData,
              backgroundColor: 'rgba(0, 123, 255, 0.8)',
              borderColor: '#007bff',
              borderWidth: 1,
              borderRadius: 8,
              hoverBackgroundColor: 'rgba(0, 123, 255, 1)'
          }]
      },
      options: {
          maintainAspectRatio: false,
          responsive: true,
          interaction: { mode: 'nearest', intersect: false },
          plugins: {
              tooltip: {
                  enabled: true,
                  backgroundColor: '#333',
                  titleColor: '#fff',
                  bodyColor: '#fff',
                  padding: 10,
                  displayColors: false,
                  callbacks: {
                      label: (ctx) => ` ${ctx.parsed.y} Kunjungan`
                  }
              },
              legend: { display: false }
          },
          scales: {
              x: {
                  grid: { display: false }
              },
              y: {
                  beginAtZero: true,
                  ticks: { stepSize: 20 }
              }
          }
      }
  });

document.addEventListener('DOMContentLoaded', function () {

    const canvasWeek = document.getElementById('chartWeek');
    if (!canvasWeek) {
        console.error('Canvas chartWeek tidak ditemukan');
        return;
    }

    const ctx2 = canvasWeek.getContext('2d');

    const weekData = Array.from(@json($chartMinggu));
    const weekLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    console.log('Data Mingguan:', weekData);
    console.log('Jumlah data:', weekData.length);

    // ✅ CEK DATA KOSONG SETELAH weekData ADA
    if (weekData.every(v => v === 0)) {
        canvasWeek.parentElement.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-bar-chart"></i>
                <p>Belum ada kunjungan minggu ini</p>
            </div>
        `;
        return;
    }

    if (weekData.length === 7) {
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: weekLabels,
                datasets: [{
                    label: 'Kunjungan',
                    data: weekData,
                    backgroundColor: 'rgba(25, 135, 84, 0.8)',
                    borderColor: '#198754',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 5,   // ⬅️ penting kalau data kecil
                        ticks: { stepSize: 1 }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        console.log('Chart mingguan berhasil dirender');

    } else {
        console.error('Data mingguan tidak valid:', weekData);
        canvasWeek.parentElement.innerHTML = `
            <div class="alert alert-warning text-center">
                Data grafik mingguan tidak tersedia
            </div>
        `;
    }
});
</script>

@endsection