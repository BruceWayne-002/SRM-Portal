<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('page-title', 'Teacher Dashboard')</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/srmlogo1.jpg') }}">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <style>
    body {
      min-height: 100vh;
      display: flex;
      margin: 0;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #640d3c;
      color: white;
      flex-shrink: 0;
      transition: all 0.3s;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      overflow-y: auto;
      z-index: 1030;
    }
    .sidebar h4 {
      font-size: 1.1rem;
    }
    .sidebar .nav-link {
      color: #EEDFFF;
      padding: 3px 16px;   /* reduced vertical padding */
      display: flex;
      align-items: center;
      font-size: 0.88rem;  /* slightly smaller */
    }

    .sidebar .nav-link i {
      margin-right: 6px;   /* smaller gap between icon and text */
      font-size: 1rem;
    }

    .sidebar .nav-item {
      margin-bottom: 2px;  /* reduce space between items */
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background-color: #5a3d87;
      color: white;
    }

    /* Content */
    .content {
      flex-grow: 1;
      padding: 20px;
      margin-left: 250px;
    }

    /* Mobile Sidebar */
    /* Mobile Sidebar */
    @media (max-width: 992px) {
      .sidebar {
        position: fixed;
        top: 0;
        left: -260px;
        width: 250px;
        height: 100%;  /* instead of 100vh */
        max-height: 100vh; /* prevent overflow */
        overflow-y: auto;
        z-index: 1030;
        padding-bottom: 80px; /* ensures logout button isn’t cut off */
      }
      .sidebar.show {
        left: 0;
      }
      .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1020;
      }
      .overlay.show {
        display: block;
      }
      #sidebarToggle {
        position: fixed;
        top: 10px;
        left: 10px;
        z-index: 1040;
      }
      .content {
        margin-left: 0;
        padding-top: 60px;
      }
    }
    .brand-logo {
    width: 150px;          /* adjust size if needed */
    height: 150px;
    object-fit: cover;    /* prevents image distortion */
    border-radius: 50%;   /* makes it perfectly round */
    border: 2px solid #e5e5e5; /* optional border */
    background-color: #fff;
}

/* Center logo inside sidebar */
.logo-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
}


  </style>
</head>
<body>

  <!-- Mobile Sidebar Toggle -->
  <button class="btn btn-primary d-lg-none" id="sidebarToggle">
    <i class="bi bi-list"></i>
  </button>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="p-3">
<div class="logo-wrapper text-center mb-3">
    <img src="{{ asset('images/srmlogo1.jpg') }}" 
         alt="SRM College Logo" 
         class="brand-logo">
</div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i> Dashboard
          </a>
        </li>

         <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('teacher.halls.*') ? 'active' : '' }}" 
       href="{{ route('teacher.halls.index') }}">
        <i class="bi bi-building"></i> My Exams
    </a>
</li>

        <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('teacher.subjects.*') ? 'active' : '' }}" 
       href="{{ route('teacher.subjects.index') }}">
        <i class="bi bi-book"></i> My Subjects
    </a>
</li>

        <li class="nav-item">
          <a href="{{ route('teacher.profile.show') }}"
            class="nav-link {{ request()->routeIs('teacher.profile.show') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> My Profile
          </a>
        </li>
        
        <!-- <li class="nav-item">
          <a href="{{ route('teacher.timetable.index') }}" class="nav-link {{ request()->routeIs('teacher.timetable.index') ? 'active' : '' }}">
            <i class="bi bi-calendar-week-fill"></i> My Timetable
          </a>
        </li> -->

        <!-- Logout -->
        <li class="nav-item mt-3">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger w-100">
              <i class="bi bi-box-arrow-right"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>

  <!-- Overlay -->
  <div class="overlay" id="overlay"></div>

  <!-- Main Content -->
  <div class="content">
    @yield('main')
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if(toggleBtn){
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');

        // Hide toggle button when sidebar is open
        if(sidebar.classList.contains('show')){
            toggleBtn.style.display = 'none';
        } else {
            toggleBtn.style.display = 'inline-block';
        }
      });
    }

    if(overlay){
      overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        toggleBtn.style.display = 'inline-block';
      });
    }
  </script>

  @yield('scripts')
  @stack('scripts')
  @stack('styles')
</body>
</html>
