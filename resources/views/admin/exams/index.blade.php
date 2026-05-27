@extends('layouts.app')

@section('title', 'Exam Schedule')

@section('main')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-calendar-alt"></i> Exam Schedule
    </h4>
    <div>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Schedule New Exam
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary {{ !request('filter') ? 'active' : '' }}">
                        All
                    </a>
                    <a href="{{ route('admin.exams.index', ['filter' => 'upcoming']) }}" class="btn btn-outline-secondary {{ request('filter') == 'upcoming' ? 'active' : '' }}">
                        Upcoming
                    </a>
                    <a href="{{ route('admin.exams.index', ['filter' => 'today']) }}" class="btn btn-outline-secondary {{ request('filter') == 'today' ? 'active' : '' }}">
                        Today
                    </a>
                    <a href="{{ route('admin.exams.index', ['filter' => 'this-week']) }}" class="btn btn-outline-secondary {{ request('filter') == 'this-week' ? 'active' : '' }}">
                        This Week
                    </a>
                    <a href="{{ route('admin.exams.index', ['filter' => 'past']) }}" class="btn btn-outline-secondary {{ request('filter') == 'past' ? 'active' : '' }}">
                        Past
                    </a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Subject Details</th>
                        <th>Exam Type</th>
                        <th>Date & Time</th>
                        <th>Session</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td>{{ $exam->id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $exam->subject_code }}</span>
                                    <span class="text-muted small">{{ $exam->subject_name }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $typeColors = [
                                        'midterm' => 'info',
                                        'final' => 'danger',
                                        'quiz' => 'success',
                                        'assignment' => 'secondary'
                                    ];
                                    $typeColor = $typeColors[$exam->exam_type] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $typeColor }}">
                                    {{ ucfirst($exam->exam_type) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <span class="fw-bold">{{ $exam->exam_date->format('d M Y') }}</span>
                                    <br>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $exam->time_session == 'FN' ? 'warning' : 'dark' }}">
                                    {{ $exam->time_session }} ({{ $exam->session_text }})
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $exam->formatted_duration }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.exams.show', $exam) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.exams.edit', $exam) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Delete"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $exam->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $exam->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this exam?</p>
                                                <div class="alert alert-warning">
                                                    <strong>{{ $exam->subject_code }}</strong> - {{ $exam->subject_name }}<br>
                                                    {{ $exam->exam_date->format('d M Y') }} at {{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete Exam</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No exams scheduled yet</h5>
                                    <p class="text-muted mb-3">Get started by scheduling your first exam</p>
                                    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus-circle"></i> Schedule New Exam
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="text-muted mb-0">
                    Showing {{ $exams->firstItem() ?? 0 }} to {{ $exams->lastItem() ?? 0 }} of {{ $exams->total() }} exams
                </p>
            </div>
            <div>
                {{ $exams->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection