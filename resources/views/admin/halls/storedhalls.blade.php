@extends('layouts.app')

@section('page-title', 'Exam Hall Management')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color:#640d3c;">Exam Hall Management</h2>
            <p class="text-muted">Manage exam halls and seating arrangements</p>
        </div>
        <div>
            <a href="{{ route('admin.halls.index') }}" class="btn btn-primary-custom">
                <i class="fas fa-door-open me-2"></i>Exam Halls
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary-custom">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Exam Halls</li>
        </ol>
    </nav>

    {{-- Halls Table --}}
    <div class="card shadow border-0">
        <div class="card-body">
            @if($halls->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Exam Halls Found</h4>
                <p class="text-muted">Convert classrooms to exam halls to get started.</p>
                
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th>#</th>
                            <th>Hall Details</th>
                            <th>Exam Date/Time</th>
                            <th>Capacity</th>
                            <th>Supervisor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($halls as $hall)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $hall->hall_name }}</strong><br>
                                <small class="text-muted">{{ $hall->exam_ids }}</small><br>
                                <small class="text-muted">{{ $hall->building }} - Floor {{ $hall->floor }}</small>
                            </td>
                            <td>
                                <strong>{{ $hall->exam_date->format('d M Y') }}</strong><br>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($hall->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($hall->end_time)->format('h:i A') }}
                                </small><br>
                                <span class="badge bg-info">{{ $hall->exam_type }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-primary mb-1">Capacity: {{ $hall->capacity }}</span>
                                </div>
                            </td>
                            <td>
                                @if($hall->teacher)
                                    <strong>{{ $hall->teacher->name }}</strong><br>
                                    <small class="text-muted">{{ $hall->teacher->employee_id }}</small>
                                @else
                                    <span class="badge bg-warning">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.halls.show', $hall->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.halls.seating', $hall->id) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Seating Arrangement">
                                        <i class="fas fa-chair"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.halls.edit', $hall->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit Hall">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.halls.destroy', $hall->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this exam hall? This will unassign all exams from this hall.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Hall">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $halls->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.btn-primary-custom {
    background: linear-gradient(135deg, #640d3c, #9D71C9);
    border: none;
    color: white;
    padding: 10px 25px;
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(100, 13, 60, 0.3);
    color: white;
}

.badge {
    font-size: 0.85em;
    padding: 5px 10px;
}

.btn-group .btn {
    border-radius: 5px !important;
    margin-right: 5px;
}
</style>
@endpush