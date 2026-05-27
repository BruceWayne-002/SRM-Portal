@extends('layouts.app')

@section('title', 'Exam Timetable - Classes')

@section('main')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Class-wise Exam Timetable
                </h4>
                <div>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-clipboard-list me-1"></i> View All Exams
                    </a>
                    <a href="{{ route('admin.exam-timetable.download-all') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download me-1"></i> Download All Classes PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" 
                               placeholder="Search classes by name or section...">
                    </div>
                </div>

            </div>

            <!-- Classes Grid -->
            @if($classes->count() > 0)
                <div class="row" id="classesGrid">
                    @foreach($classes as $class)
                        <div class="col-md-4 mb-4 class-card" 
     data-name="{{ strtolower($class->name) }}"
     data-section="{{ strtolower($class->section_name) }}">
    <div class="card h-100 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-chalkboard me-2"></i>{{ $class->name }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row text-center mb-3">
                <div class="col-6">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <h3 class="text-primary mb-1">{{ $class->subjects_count }}</h3>
                            <small class="text-muted">Subjects</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <h3 class="text-success mb-1">{{ $class->exams_count }}</h3>
                            <small class="text-muted">Exams</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <ul class="list-unstyled mb-3">
                <li class="mb-2">
                    <i class="fas fa-layer-group me-2 text-info"></i>
                    Section: <strong>{{ $class->section_name }}</strong>
                </li>
                <li class="mb-2">
                    <i class="fas fa-graduation-cap me-2 text-primary"></i>
                    Semesters: <strong>
                        @php
                            $subjectSemesters = App\Models\Subject::where('class_id', $class->id)
                                ->whereNotNull('semester')
                                ->distinct()
                                ->pluck('semester')
                                ->sort()
                                ->values();
                        @endphp
                        @if($subjectSemesters->count() > 0)
                            {{ $subjectSemesters->map(function($sem) {
                                return $sem . ($sem == 1 ? 'st' : ($sem == 2 ? 'nd' : ($sem == 3 ? 'rd' : 'th')));
                            })->implode(', ') }}
                        @else
                            N/A
                        @endif
                    </strong>
                </li>
                <li>
                    <i class="fas fa-users me-2 text-success"></i>
                    Students: <strong>{{ $class->students_count }}</strong>
                </li>
            </ul>
        </div>
        <div class="card-footer bg-light">
            <div class="d-grid">
                <a href="{{ route('admin.exam-timetable.class-exams', $class) }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-calendar-alt me-1"></i> View Exam Timetable
                </a>
            </div>
        </div>
    </div>
</div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                    <h5>No Classes Found</h5>
                    <p class="text-muted">No classes have been created yet.</p>
                    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Create New Class
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const classCards = document.querySelectorAll('.class-card');
        
        classCards.forEach(card => {
            const className = card.dataset.name;
            const classSection = card.dataset.section;
            
            if (className.includes(searchValue) || classSection.includes(searchValue)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Filter by academic year
    document.getElementById('academicYearFilter').addEventListener('change', function() {
        const selectedYear = this.value;
        const classCards = document.querySelectorAll('.class-card');
        
        classCards.forEach(card => {
            const cardYear = card.dataset.year;
            
            if (!selectedYear || cardYear === selectedYear) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script>
@endpush