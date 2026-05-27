@extends('layouts.app')

@section('page-title', 'Class Management')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color:#640d3c;">Class Management</h2>
            <p class="text-muted mb-0">Manage classes and their sections</p>
        </div>

        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary-custom">
            <i class="fas fa-plus"></i> Add New Class
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary-custom">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Classes</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search Class</label>
                    <input type="text" id="searchClass" class="form-control" placeholder="Search class name...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Filter Section</label>
                    <select id="filterSection" class="form-control">
                        <option value="">All Sections</option>
                        @foreach(range('A', 'Z') as $letter)
                            <option value="{{ $letter }}">{{ $letter }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Filter Status</label>
                    <select id="filterStatus" class="form-control">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button" id="resetFilter" class="btn btn-secondary w-100">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="classesTable">
                    <thead style="background-color:#f8f9fa;">
                        <tr>
                            <th>#</th>
                            <th>Class Name</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($classes as $classItem)
                            <tr class="class-row"
                                data-name="{{ strtolower($classItem->name) }}"
                                data-section="{{ $classItem->section_name }}"
                                data-status="{{ $classItem->is_active ? 'active' : 'inactive' }}">
                                <td class="row-number">
                                    {{ method_exists($classes, 'firstItem') ? $classes->firstItem() + $loop->index : $loop->iteration }}
                                </td>

                                <td>
                                    <strong>{{ $classItem->name }}</strong>
                                    @if($classItem->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($classItem->description, 30) }}</small>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-primary">{{ $classItem->section_name }}</span>
                                </td>

                                <td>
                                    <span class="badge {{ $classItem->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $classItem->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.classes.show', $classItem->id) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.classes.edit', $classItem->id) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.classes.toggle-status', $classItem->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-{{ $classItem->is_active ? 'danger' : 'success' }}"
                                                    title="{{ $classItem->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas {{ $classItem->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.classes.destroy', $classItem->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this class section?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                                        <p>No classes found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="noFilterData" style="display:none;">
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <p class="mb-0">No matching class found.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $classes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchClass = document.getElementById('searchClass');
    const filterSection = document.getElementById('filterSection');
    const filterStatus = document.getElementById('filterStatus');
    const resetFilter = document.getElementById('resetFilter');
    const rows = document.querySelectorAll('.class-row');
    const noFilterData = document.getElementById('noFilterData');

    function applyFilter() {
        const searchValue = searchClass.value.toLowerCase().trim();
        const sectionValue = filterSection.value;
        const statusValue = filterStatus.value;

        let visibleCount = 0;

        rows.forEach(function (row) {
            const name = row.dataset.name || '';
            const section = row.dataset.section || '';
            const status = row.dataset.status || '';

            const matchName = name.includes(searchValue);
            const matchSection = sectionValue === '' || section === sectionValue;
            const matchStatus = statusValue === '' || status === statusValue;

            if (matchName && matchSection && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        noFilterData.style.display = visibleCount === 0 && rows.length > 0 ? '' : 'none';
    }

    searchClass.addEventListener('input', applyFilter);
    filterSection.addEventListener('change', applyFilter);
    filterStatus.addEventListener('change', applyFilter);

    resetFilter.addEventListener('click', function () {
        searchClass.value = '';
        filterSection.value = '';
        filterStatus.value = '';
        applyFilter();
    });
});
</script>
@endpush

@push('styles')
<style>
.btn-primary-custom {
    background: linear-gradient(135deg, #640d3c, #EEDFFF);
    border: none;
    color: white;
    padding: 10px 25px;
    border-radius: 30px;
    font-weight: 500;
}

.btn-primary-custom:hover {
    color: white;
    box-shadow: 0 5px 15px rgba(100, 13, 60, 0.3);
}

.text-primary-custom {
    color: #640d3c;
}

.badge {
    font-size: 0.85em;
    padding: 0.35em 0.65em;
}

.table > :not(caption) > * > * {
    vertical-align: middle;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.form-control:focus {
    border-color: #640d3c;
    box-shadow: 0 0 0 0.2rem rgba(100, 13, 60, 0.15);
}
</style>
@endpush