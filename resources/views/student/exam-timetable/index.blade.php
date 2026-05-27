@extends('layouts.student')

@section('title', 'My Exam Timetable')

@section('main')
<style>
:root {
    --primary: #4361ee;
    --primary-light: #4895ef;
    --primary-soft: #eef2ff;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --dark: #1e293b;
    --gray: #64748b;
    --light: #f8fafc;
    --border: #e2e8f0;
}

/* Base Styles */
.container-fluid {
    padding: 1.5rem;
    background: #f1f5f9;
    min-height: 100vh;
}

/* Card Styles */
.main-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    background: white;
    border-bottom: 1px solid var(--border);
    padding: 1.25rem 1.5rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 600;
    color: var(--dark);
}

.card-header h3 i {
    color: var(--primary);
}

.card-body {
    padding: 1.5rem;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-light);
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-outline {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--gray);
}

.btn-outline:hover {
    background: var(--light);
    border-color: var(--primary);
    color: var(--primary);
}

/* Alert */
.alert {
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-danger {
    background: #fef2f2;
    color: var(--danger);
    border-left: 4px solid var(--danger);
}

/* Student Info Bar */
.info-bar {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    color: white;
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    align-items: center;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.15);
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.95rem;
}

.info-item i {
    font-size: 1rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.2s;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    border-color: var(--primary);
}

.stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
}

.stat-icon.total { background: var(--primary-soft); color: var(--primary); }
.stat-icon.subjects { background: #e0f2fe; color: #0284c7; }
.stat-icon.upcoming { background: #fef3c7; color: var(--warning); }
.stat-icon.completed { background: #dcfce7; color: var(--success); }

.stat-card h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.25rem;
    color: var(--dark);
}

.stat-card p {
    font-size: 0.85rem;
    color: var(--gray);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Filter Bar */
.filter-bar {
    background: white;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1rem;
}

.filter-label {
    font-weight: 600;
    color: var(--dark);
    font-size: 0.9rem;
}

.filter-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 0.4rem 1rem;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid var(--border);
    background: white;
    color: var(--gray);
    cursor: pointer;
    transition: all 0.2s;
}

.filter-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.filter-btn.upcoming.active { background: var(--success); border-color: var(--success); }
.filter-btn.completed.active { background: var(--gray); border-color: var(--gray); }

.filter-info {
    margin-left: auto;
    font-size: 0.85rem;
    color: var(--gray);
}

/* Date Sections */
.date-section {
    border: 1px solid var(--border);
    border-radius: 12px;
    margin-bottom: 1.25rem;
    overflow: hidden;
    background: white;
}

.date-header {
    background: #f8fafc;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.date-header.upcoming { border-left: 4px solid var(--success); }
.date-header.completed { border-left: 4px solid var(--gray); }

.date-title {
    font-weight: 600;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.date-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 30px;
    font-weight: 500;
}

.badge-today { background: var(--warning); color: white; }
.badge-upcoming { background: var(--success); color: white; }
.badge-completed { background: var(--gray); color: white; }

/* Exam Tables */
.exam-table {
    width: 100%;
    border-collapse: collapse;
}

.exam-table th {
    text-align: left;
    padding: 1rem 1.25rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 2px solid var(--border);
}

.exam-table td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.95rem;
}

.exam-table tr:last-child td {
    border-bottom: none;
}

.exam-table tbody tr:hover {
    background: var(--light);
}

.subject-name {
    font-weight: 600;
    color: var(--dark);
}

.subject-code {
    font-size: 0.85rem;
    color: var(--gray);
    background: var(--light);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    margin-left: 0.5rem;
}

.time-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.time-icon {
    color: var(--primary);
}

/* Session Header */
.session-header {
    background: var(--light);
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    color: var(--dark);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border);
}

.session-count {
    background: white;
    padding: 0.25rem 0.75rem;
    border-radius: 30px;
    font-size: 0.8rem;
    color: var(--primary);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 12px;
    border: 2px dashed var(--border);
}

.empty-state i {
    font-size: 3rem;
    color: var(--border);
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--gray);
    margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .filter-info {
        margin-left: 0;
    }
    
    .info-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .date-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="container-fluid">
    <!-- Main Card -->
    <div class="main-card">
        <!-- Header -->
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3>
                    <i class="fas fa-calendar-alt me-2"></i>
                    Exam Timetable
                </h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('student.exam-timetable.download') }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    <a href="{{ route('student.exam-timetable.upcoming') }}" class="btn btn-success">
                        <i class="fas fa-clock"></i> Upcoming Exams
                    </a>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Student Info Bar -->
            <div class="info-bar">
                <div class="info-item">
                    <i class="fas fa-user-graduate"></i>
                    <span>{{ $student->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-id-card"></i>
                    <span>Roll No: {{ $student->roll_no ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-school"></i>
                    <span>{{ $class->name ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>{{ $stats['total_exams'] ?? 0 }}</h4>
                    <p>Total Exams</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon subjects">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>{{ $stats['total_subjects'] ?? 0 }}</h4>
                    <p>Subjects</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon upcoming">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <h4>{{ $stats['upcoming_exams'] ?? 0 }}</h4>
                    <p>Upcoming</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4>{{ $stats['completed_exams'] ?? 0 }}</h4>
                    <p>Completed</p>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <span class="filter-label">
                    <i class="fas fa-filter me-1"></i> Filter:
                </span>
                <div class="filter-buttons">
                    <button class="filter-btn active" onclick="filterExams('all')" id="filter-all">
                        All Exams
                    </button>
                    <button class="filter-btn upcoming" onclick="filterExams('upcoming')" id="filter-upcoming">
                        Upcoming ({{ $stats['upcoming_exams'] ?? 0 }})
                    </button>
                    <button class="filter-btn completed" onclick="filterExams('completed')" id="filter-completed">
                        Completed ({{ $stats['completed_exams'] ?? 0 }})
                    </button>
                </div>
                <span class="filter-info">
                    <i class="fas fa-calendar me-1"></i>
                    Showing: <span id="showing-count">{{ $exams->count() }}</span> exams
                </span>
            </div>

            <!-- Exam Schedule -->
            @if($exams->isEmpty())
                <div class="empty-state" id="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>No exams scheduled for your class yet.</p>
                </div>
            @else
                @php
                    $today = now()->toDateString();
                @endphp

                @foreach($groupedByDate as $date => $typeGroups)
                    @php
                        $isUpcoming = $date >= $today;
                    @endphp

                    <div class="date-section exam-date-card" 
                         data-date="{{ $date }}" 
                         data-status="{{ $isUpcoming ? 'upcoming' : 'completed' }}">
                        
                        <!-- Date Header -->
                        <div class="date-header {{ $isUpcoming ? 'upcoming' : 'completed' }}">
                            <div class="date-title">
                                <i class="fas fa-calendar-day"></i>
                                {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                            </div>
                            <div>
                                @if($date == $today)
                                    <span class="date-badge badge-today">
                                        <i class="fas fa-sun"></i> Today
                                    </span>
                                @elseif($isUpcoming)
                                    <span class="date-badge badge-upcoming">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($date)->diffInDays(now()) }} days left
                                    </span>
                                @else
                                    <span class="date-badge badge-completed">
                                        <i class="fas fa-check"></i> Completed
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Exam Sections -->
                        @foreach($typeGroups as $examType => $typeExams)
                            <div class="session-header">
                                <span>
                                    <i class="fas fa-clock me-2" style="color: var(--primary);"></i>
                                    {{ ucfirst($examType) }} Session
                                </span>
                                <span class="session-count">
                                    {{ $typeExams->count() }} exam{{ $typeExams->count() > 1 ? 's' : '' }}
                                </span>
                            </div>

                            <table class="exam-table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Code</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($typeExams as $exam)
                                        <tr>
                                            <td>
                                                <div class="time-cell">
                                                    <i class="fas fa-clock time-icon"></i>
                                                    {{ $exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') : 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="subject-name">
                                                    {{ $exam->subject->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="subject-code">
                                                    {{ $exam->subject->code ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-hourglass-half me-1" style="color: var(--warning);"></i>
                                                {{ $exam->duration ?? '3 Hours' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterExams(filterType) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById(`filter-${filterType}`).classList.add('active');
    
    const today = new Date().toISOString().split('T')[0];
    let visibleCount = 0;
    let hasVisible = false;
    
    // Filter date sections
    document.querySelectorAll('.exam-date-card').forEach(card => {
        const status = card.dataset.status;
        
        if (filterType === 'all' || status === filterType) {
            card.style.display = 'block';
            hasVisible = true;
            
            // Count exams in visible cards
            const examsInCard = card.querySelectorAll('.exam-table tbody tr').length;
            visibleCount += examsInCard;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update count
    document.getElementById('showing-count').textContent = visibleCount;
    
    // Handle empty state
    const emptyState = document.getElementById('empty-state');
    const existingFilterEmpty = document.getElementById('filter-empty-state');
    
    if (existingFilterEmpty) {
        existingFilterEmpty.remove();
    }
    
    if (!hasVisible && filterType !== 'all') {
        const container = document.querySelector('.card-body');
        const emptyDiv = document.createElement('div');
        emptyDiv.className = 'empty-state';
        emptyDiv.id = 'filter-empty-state';
        emptyDiv.innerHTML = `
            <i class="fas fa-${filterType === 'upcoming' ? 'clock' : 'check-circle'}"></i>
            <p>No ${filterType} exams found.</p>
            <button class="btn btn-outline" onclick="filterExams('all')">
                <i class="fas fa-eye me-2"></i>View All Exams
            </button>
        `;
        container.appendChild(emptyDiv);
    }
}
</script>
@endpush
@endsection