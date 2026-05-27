@extends('layouts.app')

@section('page-title', 'Exam Hall Management')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color:#640d3c;">Exam Hall Management</h2>
        </div>
        <a href="{{ route('admin.halls.storedhalls') }}" class="btn btn-primary">Allocated Exam Halls</a>
    </div>

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
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th>#</th>
                            <th>Hall Name/Code</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $hall)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $hall->hall_code ?? 'HALL_' . strtoupper($hall->room_number) }}</strong><br>
                                <small class="text-muted">Room: {{ $hall->room_number }}</small>
                            </td>
                           
                            <td>
                                <span class="badge bg-info">{{ $hall->capacity }}</span>
                            </td>
                            <td>
    <div class="btn-group">
        <a href="{{ route('admin.halls.create') }}"
           class="btn btn-sm btn-outline-success"
           title="Create Hall">
            <i class="fas fa-door-open"></i>
        </a>
    </div>
</td>                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

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
</style>
@endpush