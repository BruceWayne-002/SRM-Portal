@extends('layouts.app')

@section('page-title', 'Exam Hall Management')

@section('main')
<div class="hall-management-wrapper">
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header Section with Gradient -->
    <div class="hall-header p-4 p-md-5 mb-4 rounded-4 shadow-lg">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-center text-md-start">
                <h2 class="display-6 fw-bold text-white mb-2">
                    <i class="fas fa-door-open me-2"></i>Exam Hall Management
                </h2>
                <p class="text-white opacity-75 mb-0 fs-5">
                    <i class="fas fa-info-circle me-1"></i>Manage exam halls, seating capacity & layout
                </p>
            </div>

            <button class="btn btn-light btn-lg rounded-pill px-4 px-md-5 shadow-sm hover-scale"
                    data-bs-toggle="modal"
                    data-bs-target="#hallModal"
                    onclick="openCreateModal()"
                    style="background: white; color: #640d3c;">
                <i class="fas fa-plus-circle me-2" style="color: #640d3c;"></i>Create New Hall
            </button>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <!--<div class="row g-3 g-md-4 mb-4">-->
    <!--    <div class="col-6 col-md-3">-->
    <!--        <div class="stat-card bg-white p-3 p-md-4 rounded-4 shadow-sm">-->
    <!--            <div class="d-flex align-items-center gap-3">-->
    <!--                <div class="stat-icon" style="background: rgba(100, 13, 60, 0.1);">-->
    <!--                    <i class="fas fa-building fs-4" style="color: #640d3c;"></i>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <span class="text-muted small text-uppercase">Total Halls</span>-->
    <!--                    <h3 class="fw-bold mb-0" style="color: #640d3c;">{{ $halls->count() }}</h3>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="col-6 col-md-3">-->
    <!--        <div class="stat-card bg-white p-3 p-md-4 rounded-4 shadow-sm">-->
    <!--            <div class="d-flex align-items-center gap-3">-->
    <!--                <div class="stat-icon" style="background: rgba(157, 113, 201, 0.1);">-->
    <!--                    <i class="fas fa-users fs-4" style="color: #9D71C9;"></i>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <span class="text-muted small text-uppercase">Total Capacity</span>-->
    <!--                    <h3 class="fw-bold mb-0" style="color: #9D71C9;">{{ $halls->sum('capacity') }}</h3>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="col-6 col-md-3">-->
    <!--        <div class="stat-card bg-white p-3 p-md-4 rounded-4 shadow-sm">-->
    <!--            <div class="d-flex align-items-center gap-3">-->
    <!--                <div class="stat-icon" style="background: rgba(100, 13, 60, 0.1);">-->
    <!--                    <i class="fas fa-th fs-4" style="color: #640d3c;"></i>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <span class="text-muted small text-uppercase">Avg. Rows</span>-->
    <!--                    <h3 class="fw-bold mb-0" style="color: #640d3c;">{{ round($halls->avg('rows'), 1) }}</h3>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="col-6 col-md-3">-->
    <!--        <div class="stat-card bg-white p-3 p-md-4 rounded-4 shadow-sm">-->
    <!--            <div class="d-flex align-items-center gap-3">-->
    <!--                <div class="stat-icon" style="background: rgba(157, 113, 201, 0.1);">-->
    <!--                    <i class="fas fa-layer-group fs-4" style="color: #9D71C9;"></i>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <span class="text-muted small text-uppercase">Avg. Columns</span>-->
    <!--                    <h3 class="fw-bold mb-0" style="color: #9D71C9;">{{ round($halls->avg('columns'), 1) }}</h3>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Table Card with DataTable -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <table class="table align-middle hall-table" id="hallsTable">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Hall Details</th>
                        <th>Location</th>
                        <th class="text-center">Capacity</th>
                        <th class="text-center">Layout</th>
                        <th class="text-center" width="300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($halls as $hall)
                    <tr>
                        <td>
                            <span class="fw-semibold" style="color: #9D71C9;">#{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="hall-icon rounded-3 p-2" style="background: rgba(100, 13, 60, 0.1);">
                                    <i class="fas fa-door-open fs-4" style="color: #640d3c;"></i>
                                </div>
                                <div>
                                    <strong class="d-block" style="color: #640d3c;">{{ $hall->hall_name }}</strong>
                                    <small class="text-muted">ID: H{{ str_pad($hall->id, 4, '0', STR_PAD_LEFT) }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div>
                                <span class="fw-semibold d-block">{{ $hall->building }}</span>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1" style="font-size: 10px; color: #9D71C9;"></i>
                                    Floor: {{ $hall->floor ?? 'Ground' }}
                                </small>
                            </div>
                        </td>

                        <td class="text-center">
                            <span class="capacity-badge" style="background: linear-gradient(135deg, #640d3c, #9D71C9);">
                                <i class="fas fa-users me-1"></i>
                                {{ number_format($hall->capacity) }}
                            </span>
                        </td>

                        <td class="text-center">
                            <div>
                                <span class="layout-badge d-inline-block mb-1" style="background: rgba(100, 13, 60, 0.1); color: #640d3c;">
                                    <i class="fas fa-th me-1"></i>
                                    {{ $hall->rows }} × {{ $hall->columns }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-chair me-1" style="color: #9D71C9;"></i>
                                    {{ $hall->students_per_table }} per table
                                </small>
                            </div>
                        </td>

                        <td class="text-center">
    <!-- First Row: Edit & Delete -->
                            <div class="d-flex justify-content-center gap-2 mb-2">
                                <!-- Edit Button -->
                                <button class="btn action-btn edit-btn" 
                                        data-bs-toggle="modal"
                                        data-bs-target="#hallModal"
                                        onclick="openEditModal({{ $hall }})"
                                        title="Edit Hall"
                                        style="background: rgba(100, 13, 60, 0.1); color: #640d3c; min-width: 80px;">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                        
                                <!-- Delete Button -->
                                <form action="{{ route('admin.halls.destroy', $hall->id) }}"
                                      method="POST"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="btn action-btn delete-btn"
                                            onclick="confirmDelete(this)"
                                            title="Delete Hall"
                                            style="background: rgba(220, 53, 69, 0.1); color: #dc3545; min-width: 80px;">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        
                            <!-- Second Row: Allocate & View -->
                            <div class="d-flex justify-content-center gap-2">
                                <!-- Allocate Button -->
                                <a href="{{ route('admin.exam-allocation.create', ['hall_id' => $hall->id]) }}"
                                   class="btn action-btn allocate-btn"
                                   title="Create Exam Allocation"
                                   style="background: rgba(40, 167, 69, 0.1); color: #28a745; min-width: 80px;">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Allocate
                                </a>
                        
                                <!-- View Button -->
                                <a href="{{ route('admin.exam-allocation.view', ['hall_id' => $hall->id]) }}"
                                   class="btn action-btn view-btn"
                                   title="View Allocation"
                                   style="background: rgba(157, 113, 201, 0.1); color: #9D71C9; min-width: 80px;">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-door-open fs-1 mb-3" style="color: #9D71C9;"></i>
                                <h5 style="color: #640d3c;">No Halls Found</h5>
                                <p class="text-muted small mb-3">Get started by creating your first exam hall</p>
                                <button class="btn rounded-pill px-4"
                                        data-bs-toggle="modal"
                                        data-bs-target="#hallModal"
                                        onclick="openCreateModal()"
                                        style="background: linear-gradient(135deg, #640d3c, #9D71C9); color: white;">
                                    <i class="fas fa-plus me-2"></i>Create Hall
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="hallModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">

            <form id="hallForm" method="POST">
                @csrf
                <input type="hidden" id="methodField" name="_method">

                <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #640d3c, #9D71C9);">
                    <h5 class="modal-title text-white fw-bold" id="modalTitle">
                        <i class="fas fa-plus-circle me-2"></i>Create New Hall
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #640d3c;">
                                <i class="fas fa-door-open me-1"></i>Hall Name
                            </label>
                            <input type="text" name="hall_name"
                                   id="hall_name"
                                   class="form-control form-control-lg rounded-3"
                                   placeholder="e.g., Main Hall, Hall A"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #640d3c;">
                                <i class="fas fa-building me-1"></i>Building
                            </label>
                            <input type="text" name="building"
                                   id="building"
                                   class="form-control form-control-lg rounded-3"
                                   placeholder="e.g., Science Block"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #640d3c;">
                                <i class="fas fa-layer-group me-1"></i>Floor (Optional)
                            </label>
                            <input type="text" name="floor"
                                   id="floor"
                                   class="form-control form-control-lg rounded-3"
                                   placeholder="e.g., 1st Floor">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #640d3c;">
                                <i class="fas fa-users me-1"></i>Students Per Table
                            </label>
                            <input type="number" 
                                   name="students_per_table"
                                   id="students_per_table"
                                   class="form-control form-control-lg rounded-3"
                                   min="1"
                                   max="10"
                                   placeholder="2"
                                   required>
                        </div>

                        <div class="col-12">
                            <div class="p-4 rounded-4" style="background: rgba(157, 113, 201, 0.05);">
                                <label class="form-label fw-semibold mb-3" style="color: #640d3c;">
                                    <i class="fas fa-th me-1"></i>Hall Layout
                                </label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Number of Rows</label>
                                        <input type="number" name="rows"
                                               id="rows"
                                               class="form-control form-control-lg rounded-3"
                                               min="1"
                                               placeholder="5"
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Number of Columns</label>
                                        <input type="number" name="columns"
                                               id="columns"
                                               class="form-control form-control-lg rounded-3"
                                               min="1"
                                               placeholder="4"
                                               required>
                                    </div>
                                </div>
                                <div class="mt-3 small">
                                    <i class="fas fa-info-circle me-1" style="color: #9D71C9;"></i>
                                    Total capacity will be: <span id="totalCapacityPreview" style="color: #640d3c; font-weight: 600;">Rows × Columns × Students per table</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn rounded-pill px-5" 
                            style="background: linear-gradient(135deg, #640d3c, #9D71C9); color: white; border: none;">
                        <i class="fas fa-save me-2"></i>Save Hall
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
</div>

<style>
/* DataTables Customization */
@import url('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css');
@import url('https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css');

.hall-management-wrapper {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

/* Header Gradient - Using your colors */
.hall-management-wrapper .hall-header {
    background: linear-gradient(135deg, #640d3c, #9D71C9) !important;
    position: relative;
    overflow: hidden;
}

.hall-management-wrapper .hall-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Hover Scale Effect */
.hover-scale {
    transition: transform 0.3s ease;
}

.hover-scale:hover {
    transform: scale(1.05);
}

/* Stat Cards */
.stat-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(100, 13, 60, 0.1) !important;
}

.stat-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
}

/* Table Styles */
.hall-management-wrapper .hall-table {
    font-size: 0.95rem;
}

/* DataTable Customization */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid rgba(100, 13, 60, 0.2) !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
}

.dataTables_wrapper .dataTables_length select:focus,
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #9D71C9 !important;
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(157, 113, 201, 0.1) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #640d3c, #9D71C9) !important;
    border: none !important;
    color: white !important;
    border-radius: 8px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: rgba(100, 13, 60, 0.1) !important;
    border: 1px solid #640d3c !important;
    color: #640d3c !important;
}

.dataTables_wrapper .dataTables_info {
    color: #640d3c !important;
    padding-top: 15px !important;
}

/* Action Buttons */
.hall-management-wrapper .action-btn {
    padding: 8px 16px !important;
    border-radius: 25px !important;
    border: none !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
    min-width: 90px !important;
}

.hall-management-wrapper .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.hall-management-wrapper .action-btn i {
    font-size: 0.9rem;
}

/* Capacity Badge */
.hall-management-wrapper .capacity-badge {
    color: white;
    padding: 6px 16px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 10px rgba(157, 113, 201, 0.3);
}

/* Layout Badge */
.hall-management-wrapper .layout-badge {
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Hall Icon */
.hall-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

/* Empty State */
.empty-state {
    padding: 40px 20px;
    border-radius: 20px;
    max-width: 400px;
    margin: 0 auto;
}

/* Form Controls */
.hall-management-wrapper .form-control {
    border: 1px solid rgba(100, 13, 60, 0.2);
    transition: all 0.3s ease;
}

.hall-management-wrapper .form-control:focus {
    border-color: #9D71C9;
    box-shadow: 0 0 0 3px rgba(157, 113, 201, 0.1);
}

.hall-management-wrapper .form-label {
    font-weight: 600;
    margin-bottom: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .action-group {
        flex-direction: column;
        align-items: center;
    }
    
    .hall-management-wrapper .action-btn {
        width: 100%;
        margin-bottom: 5px;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: left !important;
        margin-bottom: 15px !important;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 15px !important;
        text-align: center !important;
    }
}
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#hallsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "<i class='fas fa-search me-1' style='color: #640d3c;'></i> Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ halls",
            infoEmpty: "Showing 0 to 0 of 0 halls",
            infoFiltered: "(filtered from _MAX_ total halls)",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            }
        },
        buttons: [
            {
                text: '<i class="fas fa-copy me-1"></i> Copy',
                extend: 'copy',
                className: 'btn btn-sm rounded-pill',
                style: 'background: rgba(100, 13, 60, 0.1); color: #640d3c;'
            },
            {
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                extend: 'excel',
                className: 'btn btn-sm rounded-pill',
                style: 'background: rgba(40, 167, 69, 0.1); color: #28a745;'
            },
            {
                text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                extend: 'pdf',
                className: 'btn btn-sm rounded-pill',
                style: 'background: rgba(220, 53, 69, 0.1); color: #dc3545;'
            },
            {
                text: '<i class="fas fa-print me-1"></i> Print',
                extend: 'print',
                className: 'btn btn-sm rounded-pill',
                style: 'background: rgba(157, 113, 201, 0.1); color: #9D71C9;'
            }
        ],
        dom: '<"row"<"col-md-6"B><"col-md-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row"<"col-md-5"i><"col-md-7"p>>',
        initComplete: function() {
            // Style the buttons after initialization
            $('.dt-buttons button').each(function() {
                $(this).css({
                    'margin-right': '5px',
                    'padding': '8px 16px',
                    'border': 'none',
                    'font-size': '0.9rem'
                });
            });
        }
    });
});

// Open create modal
function openCreateModal() {
    document.getElementById('hallForm').reset();
    document.getElementById('methodField').value = '';
    document.getElementById('hallForm').action = '{{ route("admin.halls.store") }}';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Create New Hall';
}

// Open edit modal
function openEditModal(hall) {
    document.getElementById('hall_name').value = hall.hall_name;
    document.getElementById('building').value = hall.building;
    document.getElementById('floor').value = hall.floor || '';
    document.getElementById('rows').value = hall.rows;
    document.getElementById('columns').value = hall.columns;
    document.getElementById('students_per_table').value = hall.students_per_table;
    
    document.getElementById('methodField').value = 'PUT';
    document.getElementById('hallForm').action = '{{ route("admin.halls.update", "") }}/' + hall.id;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Hall';
}

// Confirm delete
function confirmDelete(button) {
    if (confirm('Are you sure you want to delete this hall?\nThis action cannot be undone!')) {
        button.closest('form').submit();
    }
}

// Live capacity preview
document.addEventListener('DOMContentLoaded', function() {
    const rowsInput = document.getElementById('rows');
    const colsInput = document.getElementById('columns');
    const perTableInput = document.getElementById('students_per_table');
    
    if (rowsInput && colsInput && perTableInput) {
        [rowsInput, colsInput, perTableInput].forEach(input => {
            input.addEventListener('input', updateCapacityPreview);
        });
    }
});

function updateCapacityPreview() {
    const rows = parseInt(document.getElementById('rows').value) || 0;
    const cols = parseInt(document.getElementById('columns').value) || 0;
    const perTable = parseInt(document.getElementById('students_per_table').value) || 0;
    
    const total = rows * cols * perTable;
    const preview = document.getElementById('totalCapacityPreview');
    
    if (preview) {
        preview.textContent = total ? total.toLocaleString() + ' seats' : 'Rows × Columns × Students per table';
    }
}
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


@endsection