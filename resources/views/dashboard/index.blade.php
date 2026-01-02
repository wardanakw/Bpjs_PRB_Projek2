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
<script>

    const ctx1 = document.getElementById('chartMonth').getContext('2d');
    const monthData = @json(array_values($kunjunganPerBulan));
  const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    const chartMonth = new Chart(ctx1, {
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


  const ctx2 = document.getElementById('chartWeek').getContext('2d');
    const weekData = @json(array_values($kunjunganPerMinggu));

    const chartWeek = new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        datasets: [{
          label: 'Kunjungan',
          data: weekData,
          backgroundColor: ['#4CAF50', '#FF9800', '#F44336', '#2196F3', '#9C27B0', '#00BCD4', '#FFC107'],
          borderRadius: 6,
          hoverBackgroundColor: ['#43A047', '#FB8C00', '#E53935', '#1976D2', '#8E24AA', '#00ACC1', '#FFB300']
        }]
      },
      options: {
          maintainAspectRatio: false,
          responsive: true,
          plugins: {
              tooltip: {
                  enabled: true,
                  backgroundColor: '#333',
                  titleColor: '#fff',
                  bodyColor: '#fff',
                  displayColors: false,
                  callbacks: {
                      label: (ctx) => ` ${ctx.parsed.y} Kunjungan`
                  }
              },
              legend: { display: false }
          },
          scales: {
              y: { beginAtZero: true, ticks: { stepSize: 5 } },
              x: { grid: { display: false } }
          }
      }
  });

 
  async function fetchDashboardData() {
    try {
      const res = await fetch("/dashboard/data");
      if (!res.ok) return;
      const data = await res.json();

      if (data.kunjunganPerBulan) {
        chartMonth.data.datasets[0].data = Object.values(data.kunjunganPerBulan);
        chartMonth.update();
      }

   
      if (data.kunjunganPerMinggu) {

        const wk = Object.values(data.kunjunganPerMinggu);
        chartWeek.data.datasets[0].data = wk;
        chartWeek.update();
      }

      if (data.diagnosaChart) {
        const tbody = document.getElementById('diagnosaBody');
        if (tbody) {
          tbody.innerHTML = '';
          if (data.diagnosaChart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Belum ada data bulan ini</td></tr>';
          } else {
            data.diagnosaChart.forEach((item, idx) => {
              const tr = `<tr><td>${idx+1}</td><td>${item.diagnosa}</td><td>${item.persen}%</td></tr>`;
              tbody.insertAdjacentHTML('beforeend', tr);
            });
          }
        }
      }

      if (data.dataPrbTerbaru) {
        const tbody = document.getElementById('prbBody');
        if (tbody) {
          tbody.innerHTML = '';
          const isAdmin = @json(auth('admin')->check());
          const colspan = isAdmin ? 6 : 5;
          if (data.dataPrbTerbaru.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted py-3">Belum ada data PRB</td></tr>`;
          } else {
            data.dataPrbTerbaru.forEach(row => {
              const badgeClass = row.status_prb === 'Aktif' ? 'bg-success' : 'bg-secondary';
              const tgl = new Date(row.tgl_pelayanan).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
              let tr = `<tr><td>${row.id_diagnosa}</td><td>${row.no_kartu_bpjs}</td><td>${row.diagnosa}</td><td><span class="badge ${badgeClass}">${row.status_prb}</span></td><td>${tgl}</td>`;
              if (isAdmin) {
                tr += `<td>${row.rumah_sakit || ''}</td>`;
              }
              tr += '</tr>';
              tbody.insertAdjacentHTML('beforeend', tr);
            });
          }
        }
      }

    } catch (err) {
      console.error('Gagal mengambil data dashboard', err);
    }
  }

 
  setInterval(fetchDashboardData, 8000);

  setInterval(function() {
      location.reload();
  }, 300000); 

</script>
@endsection
