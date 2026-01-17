<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f5f7fa;  }

        .navbar {
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            height: 80px;
        }

        .navbar-brand {
            color: #0078D7;
            font-weight: 600;
        }

        .navbar .search-box {
            background: #fff;           
            border: 2px solid #0078D7;  
            border-radius: 25px;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            width: 350px;
        }

        .navbar .search-box input {
            border: none;
            outline: none;
            background: none;
            width: 100%;
            font-size: 14px;
        }

        .navbar .profile-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: #0078D7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
      
        .sidebar {
            width: 80px;
            background: linear-gradient(180deg, #0078D7, #1888e3ff, #41b7eaff);
            height: calc(100vh - 65px);
            position: fixed;
            top: 65px;
            left: 0;
            transition: all 0.3s ease;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 100;
            overflow: hidden;
        }

        .sidebar.expanded {
            width: 220px;
            align-items: flex-start;
            padding-left: 15px;
        }

        .sidebar ul {
            list-style: none;
            margin-top: 25px;
            padding: 0;
            width: 100%;
        }

        .sidebar ul li {
            padding: 0;
            cursor: pointer;
            border-radius: 8px;
            margin: 5px 0;
            transition: 0.2s;
            display: flex;
            align-items: center;
            color: white;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            color: white;
            width: 100%;
            border-radius: 8px;
            transition: 0.2s;
        }

        .sidebar ul li a:hover { 
            background-color: rgba(255, 255, 255, 0.2); 
            text-decoration: none;
            color: white;
        }

        .sidebar ul li i {
            font-size: 18px;
            color: white;
            margin-right: 10px;
            min-width: 25px;
            text-align: center;
        }

        .sidebar ul li span {
            font-size: 15px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar.expanded ul li span { opacity: 1; }

       
        .main-content {
            margin-left: 80px;
            padding: 85px 20px 20px 20px;
            transition: all 0.3s ease;
            min-height: calc(100vh - 65px);
        }

        .main-content.expanded { margin-left: 220px; }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            background: #fff;
        }

        .navbar-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #0078D7;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .navbar-toggle:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        @media (max-width: 992px) {
            .sidebar { 
                left: -220px; 
                width: 220px;
            }
            .sidebar.expanded { left: 0; }
            .main-content { margin-left: 0 !important; }
            
            .navbar-toggle {
                display: block !important;
            }
        }
        .week-chart {
    position: relative;
    height: 260px !important;
}

#chartWeek {
    height: 260px !important;
    width: 100% !important;
}
       

    </style>
</head>
<body>
  
    <nav class="navbar px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
           
            <button class="navbar-toggle me-3" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="fw-bold fs-4 navbar-brand">RUBICON+</div>
        </div>

        <div class="search-box mx-auto">
        <i class="bi bi-search me-2" style="color: #0078D7; font-size: 1.1rem;"></i>
        <input type="text" placeholder="Search" class="form-control search-input">
        </div>


        <div class="d-flex align-items-center gap-3">
           <span id="navbar-username" class="fw-semibold">
        {{ Auth::user()->name }}

        @if(Auth::user()->role === 'fktp' && Auth::user()->fktp)
            — <span class="text-primary fw-bold">{{ Auth::user()->fktp->nama_fktp }}</span>
        @endif

        @if(Auth::user()->role === 'rs' && Auth::user()->rs)
            — <span class="text-primary fw-bold">{{ Auth::user()->rs->nama_rs }}</span>
        @endif

        @if(Auth::user()->role === 'apotek' && Auth::user()->apotek)
            — <span class="text-primary fw-bold">{{ Auth::user()->apotek->nama_apotek }}</span>
        @endif
    </span>

            <div class="dropdown">
                <div class="profile-icon" data-bs-toggle="dropdown">
                    <i class="bi bi-person"></i>
                </div>
                
                <ul class="dropdown-menu dropdown-menu-end mt-2 shadow">
    <li>
        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingAccountModal">
            <i class="bi bi-gear me-2"></i>Setting Account
        </a>
    </li>

    <li><hr class="dropdown-divider"></li>

    <li>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </button>
        </form>
    </li>
</ul>

            </div>
        </div>
    </nav>

   
    <div class="sidebar" id="sidebar">
       <ul>
    {{-- Dashboard semua role --}}
    <li>
        <a href="{{ (Auth::user()->role == 'fktp') ? route('fktp.dashboard') : ((Auth::user()->role == 'apotek') ? route('apotek.dashboard') : route('dashboard.index')) }}">
            <i class="bi bi-house"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- Menu khusus RS --}}
     @if(in_array(Auth::user()->role, ['admin', 'rumah_sakit']))
        <li>
            <a href="{{ route('pasien.index') }}">
                <i class="bi bi-clipboard-data"></i>
                <span>Data Pasien Rumah Sakit</span>
            </a>
        </li>
        <li>
  <a href="#" data-bs-toggle="modal" data-bs-target="#settingAccountModal">
    <i class="bi bi-gear"></i>
    <span>Pengaturan</span>
  </a>
</li>   
        
    @endif
    @auth('admin')
<li class="nav-item">
    <a href="{{ route('admin.faskes.index') }}"
       class="nav-link {{ request()->routeIs('admin.faskes.*') ? 'active' : '' }}">
        <i class="bi bi-hospital"></i>
        <span>Kelola Faskes</span>
    </a>
</li>
@endauth

    {{-- Menu khusus FKTP --}}
    @if(Auth::user()->role == 'fktp')
        <li>
            <a href="{{ route('fktp.patients.index') }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Data Pasien PRB</span>
            </a>
            
        </li>
        
    @endif

    @if(Auth::user()->role == 'apotek')
            <li>
                <a href="{{ (auth()->user()->role === 'apotek') ? route('apotek.patients.index') : route('fktp.patients.index') }}">
                    <i class="bi bi-eye"></i>
                    <span>Data Pasien</span>
                </a>
            </li>
            <li>
                <a href="{{ route('apotek.obat.riwayat-klaim') }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Obat klaim</span>
                </a>
            </li>
            <li>
                <a href="{{ route('apotek.laporan-obat-keluar') }}">
                    <i class="bi bi-graph-up"></i>
                    <span>Laporan Obat Keluar</span>
                </a>
            </li>
        @endif

    {{-- Menu lain tetap sama --}}

    <li>
        <a href="#">
            <i class="bi bi-info-circle"></i>
            <span>Tentang</span>
        </a>
    </li>
</ul>

    </div>

    <div class="main-content" id="mainContent">
        @yield('content')
    </div>

   @include('setting.index')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('expanded');
            mainContent.classList.toggle('expanded');
        }

    
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const isMobile = window.innerWidth <= 992;
            
            if (isMobile && sidebar.classList.contains('expanded') && 
                !sidebar.contains(event.target) && 
                !event.target.closest('.navbar-toggle')) {
                sidebar.classList.remove('expanded');
                mainContent.classList.remove('expanded');
            }
        });

        

    </script>
  
        

        <script>
                
                document.addEventListener('DOMContentLoaded', function () {
                        try {
                    
                                @if(Auth::check() && Auth::user()->role === 'apotek')
                                fetch('/apotek/notifications')
                                        .then(r => r.json())
                                        .then(json => {
                                                if (!json || !json.data || json.data.length === 0) return;
                                                const list = document.getElementById('pickupReminderList');
                                                list.innerHTML = '';
                                                json.data.forEach(item => {
                                                        const badge = item.type === 'H-0' ? '<span class="badge bg-danger me-2">H-0</span>' : (item.type === 'H-1' ? '<span class="badge bg-warning text-dark me-2">H-1</span>' : (item.type === 'H-2' ? '<span class="badge bg-info me-2">H-2</span>' : (item.type === 'H-3' ? '<span class="badge bg-primary me-2">H-3</span>' : (item.type === 'H-4' ? '<span class="badge bg-secondary me-2">H-4</span>' : '<span class="badge bg-dark me-2">H-5</span>'))));
                                                        const html = `<div class="mb-2">${badge} <strong>${item.nama_pasien}</strong> (No Kartu BPJS: ${item.no_kartu_bpjs}, Telp: ${item.no_telp}) — Diagnosa: ${item.diagnosa} — Obat: ${item.nama_obat} — Tgl Pelayanan Awal: ${item.tgl_pelayanan} — Tgl Pengambilan Obat: ${item.pickup_date}</div>`;
                                                        list.insertAdjacentHTML('beforeend', html);
                                                });
                                                var modalEl = document.getElementById('pickupReminderModal');
                                                var modal = new bootstrap.Modal(modalEl);
                                                modal.show();
                                        })
                                        .catch(err => console.error('Gagal ambil notifikasi', err));
                                @endif

                                
                                @if(Auth::check() && Auth::user()->role === 'fktp')
                                fetch('/fktp/notifications')
                                        .then(r => r.json())
                                        .then(json => {
                                                if (!json || !json.data || json.data.length === 0) return;
                                                const list = document.getElementById('fktpReminderList');
                                                list.innerHTML = '';
                                                json.data.forEach(item => {
                                                        const badge = item.type === 'H-0' ? '<span class="badge bg-danger me-2">H-0</span>' : (item.type === 'H-1' ? '<span class="badge bg-warning text-dark me-2">H-1</span>' : (item.type === 'H-2' ? '<span class="badge bg-info me-2">H-2</span>' : (item.type === 'H-3' ? '<span class="badge bg-primary me-2">H-3</span>' : (item.type === 'H-4' ? '<span class="badge bg-secondary me-2">H-4</span>' : '<span class="badge bg-dark me-2">H-5</span>'))));
                                                        const html = `<div class="mb-2">${badge} <strong>${item.nama_pasien}</strong> (No Kartu BPJS: ${item.no_kartu_bpjs}, Telp: ${item.no_telp}) — Diagnosa: ${item.diagnosa} — Tgl Pelayanan Awal: ${item.tgl_pelayanan} — Tgl Pelayanan Lanjutan: ${item.tgl_pelayanan_lanjutan}</div>`;
                                                        list.insertAdjacentHTML('beforeend', html);
                                                });
                                                var modalEl = document.getElementById('fktpReminderModal');
                                                var modal = new bootstrap.Modal(modalEl);
                                                modal.show();
                                        })
                                        .catch(err => console.error('Gagal ambil notifikasi FKTP', err));
                                @endif
        
                        } catch (e) {
                                console.error(e);
                        }
                });
        </script>

        @if(Auth::check() && Auth::user()->role === 'apotek')
                <!-- Upload PDF modal for apotek -->
                <div class="modal fade" id="uploadPdfModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Upload PDF Klaim</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="uploadPdfForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div id="uploadPdfInfo" class="mb-2"></div>
                                    <div class="mb-3">
                                        <input type="file" name="pdf" accept="application/pdf" required class="form-control" />
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                        document.addEventListener('DOMContentLoaded', function () {
                                document.querySelectorAll('.openUploadModal').forEach(btn => {
                                        btn.addEventListener('click', function (e) {
                                                const diagId = this.getAttribute('data-diagnosa-id');
                                                const pasienName = this.getAttribute('data-pasien-name') || '';
                                                const form = document.getElementById('uploadPdfForm');
                                                const info = document.getElementById('uploadPdfInfo');
                                               
                                                form.action = `/apotek/diagnosa/${diagId}/upload-pdf`;
                                                info.innerHTML = `<strong>Pasien:</strong> ${pasienName} <br> <strong>ID Diagnosa:</strong> ${diagId}`;
                                                const modalEl = document.getElementById('uploadPdfModal');
                                                const modal = new bootstrap.Modal(modalEl);
                                                modal.show();
                                        });
                                });
                        });
                </script>
        @endif

      
    @if(session('success') || session('error'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="sessionToast" class="toast align-items-center text-bg-{{ session('success') ? 'success' : 'danger' }} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') ?? session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastEl = document.getElementById('sessionToast');
            if (toastEl) {
                var bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
                bsToast.show();
            }
        });
    </script>
    @endif
</body>
</html>
