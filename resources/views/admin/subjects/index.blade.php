@extends('layouts.app')

@section('title', 'Subjects Management')

@section('main')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-book me-2"></i>Subjects Management
            </h4>
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-light">
                <i class="bi bi-plus-circle"></i> Add Subject
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search subjects...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterClass">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} - {{ $class->section_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="subjectsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th>Code</th>
                            <th>Class & Section</th>
                            <th>Semester</th>
                            <th>Marks</th>
                            <th>Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
@forelse($subjects as $subject)
    @php
        $classSections = $subject->class_section_json ?? [];

        if (is_string($classSections)) {
            $classSections = json_decode($classSections, true) ?? [];
        }

        $filterClassIds = collect($classSections)->pluck('class_id')->implode(',');

        if (empty($filterClassIds) && $subject->class_id) {
            $filterClassIds = $subject->class_id;
        }
    @endphp

    <tr data-class-ids="{{ $filterClassIds }}">
        <td>{{ $loop->iteration }}</td>

        <td>
            <strong>{{ $subject->name }}</strong>
        </td>

        <td>
            <span class="badge bg-info">{{ $subject->code }}</span>
        </td>

        <td>
            @if(!empty($classSections))
                @foreach($classSections as $item)
                    <span class="badge bg-primary mb-1">
                        {{ $item['class_name'] ?? '' }} - Section {{ $item['section'] ?? '' }}
                    </span>
                    <br>
                @endforeach
            @elseif($subject->class_name)
                {{ $subject->class_name }}
                @if($subject->section)
                    - Section {{ $subject->section }}
                @endif
            @else
                <span class="text-muted">Not Assigned</span>
            @endif
        </td>

        <td>
            @if($subject->semester)
                <span class="badge bg-secondary">Sem {{ $subject->semester }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>

        <td>
            @php
                $dist = $subject->marks_distribution;
            @endphp
            <div class="d-flex flex-column">
                <small>Total: {{ $subject->total_marks }}</small>
                <small>Pass: {{ $subject->passing_marks }} ({{ $subject->passing_percentage }}%)</small>
                <div class="progress mt-1" style="height: 5px;">
                    <div class="progress-bar bg-info" style="width: {{ $dist['internal_percent'] }}%"></div>
                    <div class="progress-bar bg-warning" style="width: {{ $dist['external_percent'] }}%"></div>
                </div>
            </div>
        </td>

        <td>
            @if($subject->teacher)
                {{ $subject->teacher->name }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>

        <td>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i>
                </a>

                <button type="button"
                        class="btn btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal{{ $subject->id }}"
                        {{ $subject->timetables()->count() > 0 ? 'disabled' : '' }}>
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <div class="modal fade" id="deleteModal{{ $subject->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Delete Subject</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <p>Are you sure you want to delete <strong>{{ $subject->name }}</strong>?</p>
                            <div class="alert alert-danger">
                                This action cannot be undone!
                            </div>
                        </div>

                        <div class="modal-footer">
                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-danger">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-4">
            <i class="bi bi-book display-4 text-muted"></i>
            <h5 class="mt-2">No subjects found</h5>
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary mt-2">
                Add Your First Subject
            </a>
        </td>
    </tr>
@endforelse
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const classFilter = document.getElementById('filterClass').value;

        const rows = document.querySelectorAll('#subjectsTable tbody tr');

        rows.forEach(row => {
            let show = true;

            if (search) {
                const text = row.textContent.toLowerCase();
                if (!text.includes(search)) {
                    show = false;
                }
            }

            if (show && classFilter) {
                const classIds = row.getAttribute('data-class-ids') || '';
                const classIdArray = classIds.split(',');

                if (!classIdArray.includes(classFilter)) {
                    show = false;
                }
            }

            row.style.display = show ? '' : 'none';
        });
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterClass').value = '';

        const rows = document.querySelectorAll('#subjectsTable tbody tr');
        rows.forEach(row => row.style.display = '');
    }

    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('filterClass').addEventListener('change', filterTable);
</script>
@endpush