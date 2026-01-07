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
          <tbody id="diagnosaBody">
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
        <div class="chart-container" style="height: 250px;">
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
        @auth('admin')
        <th>Rumah Sakit</th>
        @endauth
      </tr>
    </thead>
    <tbody id="prbBody">
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
    @auth('admin')
    <td>{{ $data->rumah_sakit }}</td>
    @endauth
  </tr>
  @empty
  <tr><td colspan="{{ auth('admin') ? 6 : 5 }}" class="text-center text-muted py-3">Belum ada data PRB</td></tr>
  @endforelse
</tbody>

  </table>
</div>


</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ========= BULANAN ========= */
    const monthCanvas = document.getElementById('chartMonth');
    if (monthCanvas) {
        try {
            const monthData = @json(array_values($kunjunganPerBulan));
            const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

            new Chart(monthCanvas, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        data: monthData,
                        backgroundColor: '#0d6efd',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { display: false } }
                }
            });
        } catch (error) {
            console.error('Error rendering monthly chart:', error);
        }
    }

    /* ========= MINGGUAN ========= */
    const weekCanvas = document.getElementById('chartWeek');
    if (weekCanvas) {
        try {
            const weekData = @json(array_values($kunjunganPerMinggu));

            new Chart(weekCanvas, {
                type: 'bar',
                data: {
                    labels: ['Sen','Sel','Rab','Kam','Jum','Sab','Min'],
                    datasets: [{
                        data: weekData,
                        backgroundColor: '#198754',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { display: false } }
                }
            });
        } catch (error) {
            console.error('Error rendering weekly chart:', error);
        }
    }

});
</script>


@endsection
