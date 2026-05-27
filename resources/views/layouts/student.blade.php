<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('page-title', 'Student Dashboard')</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/srmlogo1.jpg') }}">


  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    body {
      min-height: 100vh;
      display: flex;
      margin: 0;
    }

    /* Sidebar */
    .sidebar {
      width: 240px;
      background-color: #640d3c;
      color: white;
      flex-shrink: 0;
      transition: all 0.3s;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      overflow-y: auto;
      z-index: 1030;
    }

    .sidebar .nav-link {
      color: #EEDFFF;
      padding: 6px 16px;
      display: flex;
      align-items: center;
      font-size: 0.88rem;
    }
    .sidebar .nav-link i {
      margin-right: 8px;
      font-size: 1rem;
    }
    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background-color: #5a3d87;
      color: white;
    }
    .sidebar .nav-item {
      margin-bottom: 2px;
    }

    /* Content */
    .content {
      flex-grow: 1;
      padding: 20px;
      margin-left: 240px;
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


    /* Mobile Sidebar */
    @media (max-width: 992px) {
      .sidebar {
        position: fixed;
        top: 0;
        left: -260px;
        height: 100%;
        z-index: 1030;
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
        <a href="{{ route('student.dashboard') }}"
           class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('student.profile.show') }}"
           class="nav-link {{ request()->routeIs('student.profile.show') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Profile
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('student.marks.index') }}"
           class="nav-link {{ request()->routeIs('student.marks.index') ? 'active' : '' }} {{ request()->routeIs('student.marks.show') ? 'active' : '' }}">
            <i class="bi bi-info-circle"></i> Marks
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('student.exam-timetable.index') }}"
           class="nav-link {{ request()->routeIs('student.exam-timetable*') ? 'active' : '' }}">
            <i class="bi bi-calendar4-week"></i> Exam Time Table
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('student.hall-tickets.index') }}"
           class="nav-link {{ request()->routeIs('student.hall-tickets*') ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated"></i> Hall Tickets
        </a>
    </li>

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
  @stack('styles')
  @stack('scripts')

</body>
</html>
