<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('page-title', 'Admin Dashboard')</title>

  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/srmlogo1.jpg') }}">

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">

  <!-- Datepicker CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">

  <style>
    body {
      min-height: 100vh;
      display: flex;
      margin: 0;
      overflow-x: hidden;
    }

    .sidebar {
      width: 250px;
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

    .sidebar h4 {
      font-size: 1.1rem;
    }

    .sidebar .nav-link {
      color: #EEDFFF;
      padding: 10px 16px;
      display: flex;
      align-items: center;
      font-size: 15px;
      border-radius: 6px;
      margin-bottom: 3px;
      transition: 0.3s;
    }

    .sidebar .nav-link i {
      margin-right: 8px;
      font-size: 1rem;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background-color: #5a3d87;
      color: #ffffff !important;
      font-weight: 600;
    }

    .content {
      flex-grow: 1;
      padding: 20px;
      margin-left: 250px;
      width: calc(100% - 250px);
    }

    .brand-logo {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #e5e5e5;
      background-color: #fff;
    }

    .logo-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    @media (max-width: 992px) {
      .sidebar {
        left: -260px;
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
        background: rgba(0,0,0,0.5);
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
        width: 100%;
        padding-top: 60px;
      }
    }

    .dropdown-menu,
    .datepicker-dropdown {
      max-width: 100vw !important;
      max-height: 90vh !important;
      overflow: auto !important;
      z-index: 2000 !important;
    }

    @media (max-width: 576px) {
      .dropdown-menu,
      .datepicker-dropdown {
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
      }
    }

    .select2-container--bootstrap-5 .select2-dropdown,
    .ui-datepicker {
      max-width: 100vw !important;
      box-sizing: border-box;
      z-index: 2000;
    }

    .bs-tooltip-top,
    .bs-tooltip-bottom,
    .bs-tooltip-auto {
      max-width: 90vw;
    }
  </style>
</head>

<body>

<button class="btn btn-primary d-lg-none" id="sidebarToggle">
  <i class="bi bi-list"></i>
</button>

<div class="sidebar" id="sidebar">
  <div class="p-3">
    <div class="logo-wrapper text-center mb-3">
      <img src="{{ asset('images/srmlogo1.jpg') }}" alt="SRM College Logo" class="brand-logo">
    </div>

    <ul class="nav flex-column">

      <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.classes.index') }}"
           class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
          <i class="bi bi-people-fill"></i> Class Creation
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.students.index') }}"
           class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
          <i class="bi bi-people-fill"></i> Student Management
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.teachers.index') }}"
           class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
          <i class="bi bi-person-lines-fill"></i> Teacher Management
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.subjects.index') }}"
           class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
          <i class="bi bi-person-check-fill"></i> Subjects
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.exams.index') }}"
           class="nav-link {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
          <i class="bi bi-person-check-fill"></i> Exams
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.halls.index') }}"
           class="nav-link {{ request()->routeIs('admin.halls.*') ? 'active' : '' }}">
          <i class="bi bi-person-check-fill"></i> Hall Creation
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.exam-timetable.index') }}"
           class="nav-link {{ request()->routeIs('admin.exam-timetable.*') ? 'active' : '' }}">
          <i class="bi bi-calendar3"></i> Exam Timetable
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.attendance.index') }}"
           class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
          <i class="bi bi-person-check-fill"></i> Student Attendance
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.marks.index') }}"
           class="nav-link {{ request()->routeIs('admin.marks.*') ? 'active' : '' }}">
          <i class="bi bi-clipboard2-data-fill"></i> Marks
        </a>
      </li>

      <li class="nav-item mt-3">
        <form action="{{ route('logout') }}" method="POST">
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

<div class="content">
  @yield('main')
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {

  $('.select2').select2({
    theme: 'bootstrap4',
    width: '100%',
    dropdownAutoWidth: true,
    placeholder: 'Select an option',
    allowClear: true
  });

  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const toggleBtn = document.getElementById('sidebarToggle');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.add('show');
      overlay.classList.add('show');
      toggleBtn.style.display = 'none';
    });
  }

  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');

      if (toggleBtn) {
        toggleBtn.style.display = 'inline-block';
      }
    });
  }

  document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(el) {
    new bootstrap.Dropdown(el, {
      popperConfig: function(defaultBsPopperConfig) {
        return {
          ...defaultBsPopperConfig,
          modifiers: [
            {
              name: 'preventOverflow',
              options: {
                boundary: 'viewport'
              }
            }
          ]
        };
      }
    });
  });

  if ($.fn.datepicker) {
    $('#dob, #admission_date').datepicker({
      format: "yyyy-mm-dd",
      orientation: "auto",
      container: 'body',
      autoclose: true,
      todayHighlight: true
    });

    $('#dob, #admission_date').on('show', function() {
      var $dpWidget = $(this).datepicker('widget');

      if ($dpWidget.length) {
        var rightEdge = $dpWidget.offset().left + $dpWidget.outerWidth();
        var viewportWidth = $(window).width();

        if (rightEdge > viewportWidth) {
          $dpWidget.css('left', viewportWidth - $dpWidget.outerWidth() - 10 + 'px');
        }
      }
    });
  }

});
</script>

@yield('scripts')
@stack('scripts')

</body>
</html>