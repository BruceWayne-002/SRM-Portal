<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>School Admin Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      min-height: 100vh;
      display: flex;
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .sidebar {
      width: 250px;
      background-color: #640d3c; /* primary */
      color: white;
      flex-shrink: 0;
      transition: all 0.3s;
    }

    .sidebar .nav-link {
      color: #E2CEFF; /* secondary */
      padding: 10px 20px;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background-color: #5a3c99;
      color: white;
    }

    .content {
      flex-grow: 1;
      padding: 20px;
      background-color: #f8f9fa;
    }

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
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="p-3">
      <h4 class="text-white">School Admin</h4>
      <hr class="text-white">
      <ul class="nav flex-column">
        <!-- Dashboard -->
        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}"
             class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> Dashboard
          </a>
        </li>

        <!-- Student Management -->
        <li class="nav-item">
          <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/students*') ? 'active' : '' }}"
             data-bs-toggle="collapse" href="#studentMenu" role="button"
             aria-expanded="{{ request()->is('admin/students*') ? 'true' : 'false' }}" aria-controls="studentMenu">
            <span><i class="bi bi-people"></i> Student Management</span>
            <i class="bi bi-caret-down-fill small"></i>
          </a>
          <div class="collapse {{ request()->is('admin/students*') ? 'show' : '' }}" id="studentMenu">
            <ul class="nav flex-column ms-3">
              <li class="nav-item">
                <a href="{{ route('students.index') }}" class="nav-link">All Students</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('students.create', ['class'=>1, 'section'=>'A']) }}" class="nav-link">Add Student</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Class Sections</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Timetable</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Books</a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Teacher Management -->
        <li class="nav-item">
          <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/teachers*') ? 'active' : '' }}"
             data-bs-toggle="collapse" href="#teacherMenu" role="button"
             aria-expanded="{{ request()->is('admin/teachers*') ? 'true' : 'false' }}" aria-controls="teacherMenu">
            <span><i class="bi bi-person-lines-fill"></i> Teacher Management</span>
            <i class="bi bi-caret-down-fill small"></i>
          </a>
          <div class="collapse {{ request()->is('admin/teachers*') ? 'show' : '' }}" id="teacherMenu">
            <ul class="nav flex-column ms-3">
              <li class="nav-item">
                <a href="{{ route('teachers.index') }}" class="nav-link">All Teachers</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('teachers.create') }}" class="nav-link">Add Teacher</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Timetable</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Attendance</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Leave Approvals</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Notices / Events</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Books</a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Logout -->
        <li class="nav-item mt-3">
          <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
              <i class="bi bi-box-arrow-right"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>

  <div class="overlay" id="overlay"></div>

  <!-- Content -->
  <div class="content">
    <nav class="navbar navbar-light bg-light mb-4 shadow-sm rounded">
      <div class="container-fluid">
        <button class="btn btn-dark d-lg-none" id="sidebarToggle">
          <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h4">Dashboard</span>
      </div>
    </nav>

    <!-- Alerts -->
    <div class="container mt-2">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
    </div>

    <!-- Dashboard Cards -->
    <div class="container">
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Total Students</h5>
              <p class="card-text display-6">{{ $totalStudents ?? 0 }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Total Teachers</h5>
              <p class="card-text display-6">{{ $totalTeachers ?? 0 }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-white bg-info shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Current Date & Time</h5>
              <p class="card-text" id="clock"></p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
    });

    function updateClock() {
      const now = new Date();
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
                        hour: '2-digit', minute: '2-digit', second: '2-digit' };
      document.getElementById('clock').textContent = now.toLocaleString('en-US', options);
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>

</body>
</html>
