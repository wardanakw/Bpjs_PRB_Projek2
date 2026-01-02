<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f5f7fa; overflow-x: hidden; }

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
            <span class="fw-semibold">Hello, {{ Auth::user()->name }}</span>
            <div class="dropdown">
                <div class="profile-icon" data-bs-toggle="dropdown">
                    <i class="bi bi-person"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end mt-2 shadow">
    <li>
        <a class="dropdown-item" href="#">
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
        <a href="{{ (Auth::user()->role == 'fktp') ? route('fktp.dashboard') : route('dashboard.index') }}">
            <i class="bi bi-house"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- Menu khusus RS --}}
     @if(in_array(Auth::user()->role, ['admin', 'rumah_sakit']))
        <li>
            <a href="{{ route('pasien.index') }}">
                <i class="bi bi-clipboard-data"></i>
                <span>Data Pasien RS</span>
            </a>
        </li>
        
    @endif

    {{-- Menu khusus FKTP --}}
    @if(Auth::user()->role == 'fktp')
        <li>
            <a href="{{ route('fktp.patients.index') }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Input No Kunjungan</span>
            </a>
            
        </li>
        
    @endif

    {{-- Menu lain tetap sama --}}
   <li>
  <a href="#" data-bs-toggle="modal" data-bs-target="#settingAccountModal">
    <i class="bi bi-gear"></i>
    <span>Pengaturan</span>
  </a>
</li>

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
</body>
</html>
