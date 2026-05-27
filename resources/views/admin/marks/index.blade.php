{{-- resources/views/admin/marks/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mark Entry - Completed Exams')

@section('main')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">
            <i class="fas fa-marker me-2"></i>Mark Entry - Completed Exams
        </h2>
        <div>
            <!-- Bulk Publish Button -->
            <button type="button" class="btn btn-success me-2" id="bulkPublishBtn" data-bs-toggle="modal" data-bs-target="#bulkPublishModal">
                <i class="fas fa-rocket me-1"></i> Bulk Publish
            </button>
            <a href="{{ route('admin.marks.publish-all') }}" class="btn btn-warning me-2" onclick="return confirm('Publish ALL draft marks? This will publish all marks across all exams. This action cannot be undone.')">
                <i class="fas fa-globe me-1"></i> Publish All
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Exams</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.marks.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name ?? $class->name }} @if($class->section_name) - Section {{ $class->section_name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} ({{ $subject->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Marks Status</label>
                    <select name="marks_status" class="form-select">
                        <option value="">All</option>
                        <option value="completed" {{ request('marks_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('marks_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="draft" {{ request('marks_status') == 'draft' ? 'selected' : '' }}>Draft Only</option>
                    </select>
                </div>

                <div class="col-md-2">
    <label class="form-label">Show Draft Only</label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input" type="checkbox" id="showDraftOnly" 
               {{ request('draft_only') ? 'checked' : '' }}
               onchange="window.location.href='{{ route('admin.marks.index', array_merge(request()->all(), ['draft_only' => request('draft_only') ? null : 1])) }}'">
        <label class="form-check-label" for="showDraftOnly">Draft Exams</label>
    </div>
</div>
            
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.marks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams List -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Completed Exams</h5>
            @if($exams->where('marks_status', 'partial')->count() > 0 || $exams->where('marks_status', 'completed')->where('has_draft', true)->count() > 0)
                <span class="badge bg-warning text-dark">Draft marks available</span>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllExams">
                                </div>
                            </th>
                            <th>Date & Time</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Exam Type</th>
                            <th>Students</th>
                            <th>Marks Entered</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            @php
                                $marksCount = $exam->marks()->count();
                                $draftCount = $exam->marks()->where('status', 'draft')->count();
                                $studentCount = $exam->student_count ?? 0;
                                $marksStatus = $marksCount == 0 ? 'pending' : ($marksCount < $studentCount ? 'partial' : 'completed');
                                $statusColors = [
                                    'completed' => 'success',
                                    'partial' => 'warning',
                                    'pending' => 'danger'
                                ];
                                $hasDraft = $draftCount > 0;
                            @endphp
                            <tr class="{{ $hasDraft ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    @if($hasDraft)
                                        <div class="form-check">
                                            <input class="form-check-input exam-checkbox" type="checkbox" value="{{ $exam->id }}" id="exam_{{ $exam->id }}">
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}</strong><br>
                                    <small class="text-muted">{{ $exam->formatted_time ?? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }} ({{ $exam->time_session == 'FN' ? 'FN' : 'AN' }})</small>
                                </td>
                                <td>
                                    <strong>{{ $exam->subject->name ?? $exam->subject_name }}</strong><br>
                                    <small class="text-muted">{{ $exam->subject->code ?? $exam->subject_code }}</small>
                                    @if($hasDraft)
                                        <br><span class="badge bg-warning text-dark mt-1">{{ $draftCount }} Draft</span>
                                    @endif
                                </td>
                                <td>{{ $exam->class_name ?? ($exam->subject->classModel->full_name ?? 'N/A') }}</td>
                                <td><span class="badge bg-info">{{ ucfirst($exam->exam_type) }}</span></td>
                                <td class="text-center">{{ $studentCount }}</td>
                                <td class="text-center">
                                    <strong>{{ $marksCount }}</strong> / {{ $studentCount }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$marksStatus] ?? 'secondary' }}">
                                        {{ ucfirst($marksStatus) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($marksStatus == 'pending')
                                            <a href="{{ route('admin.marks.create', $exam) }}" 
                                               class="btn btn-sm btn-primary" title="Enter Marks">
                                                <i class="fas fa-plus"></i> Enter
                                            </a>
                                        @else
                                            <a href="{{ route('admin.marks.show', $exam) }}" 
                                               class="btn btn-sm btn-info" title="View Marks">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.marks.edit', $exam) }}" 
                                               class="btn btn-sm btn-warning" title="Edit Marks">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($marksStatus == 'partial' || $marksStatus == 'completed')
                                                <a href="{{ route('admin.marks.download', $exam) }}" 
                                                   class="btn btn-sm btn-secondary" title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            @if($hasDraft)
                                                <form action="{{ route('admin.marks.publish-exam', $exam) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            title="Publish All Drafts"
                                                            onclick="return confirm('Publish all draft marks for {{ $exam->subject_name }}?')">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                    <p class="text-muted">No completed exams found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Bulk Actions Bar -->
            <div class="row mt-3 align-items-center">
                <div class="col-md-6">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success" id="bulkPublishBtn2" onclick="publishSelected()">
                            <i class="fas fa-rocket me-1"></i> Publish Selected (<span id="selectedCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-secondary" id="clearSelection">
                            <i class="fas fa-times me-1"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    {{ $exams->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Publish Modal -->
<div class="modal fade" id="bulkPublishModal" tabindex="-1" aria-labelledby="bulkPublishModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="bulkPublishModalLabel">
                    <i class="fas fa-rocket me-2"></i>Bulk Publish Draft Exams
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Select the exams you want to publish. All marks for selected exams will be published immediately and become visible to students.
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label fw-bold" for="selectAll">
                            Select All Exams
                        </label>
                    </div>
                </div>
                
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover" id="draftExamsTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="5%"></th>
                                <th>Exam Date</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Draft Marks</th>
                                <th>Total Marks</th>
                            </tr>
                        </thead>
                        <tbody id="draftExamsList">
                            <tr>
                                <td colspan="6" class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="publishSelectedBtn" onclick="publishSelectedFromModal()">
                    <i class="fas fa-rocket me-1"></i> Publish Selected
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load draft exams when modal opens
    $('#bulkPublishModal').on('show.bs.modal', function() {
        loadDraftExams();
    });
    
    // Select All checkbox in main table
    $('#selectAllExams').change(function() {
        $('.exam-checkbox').prop('checked', $(this).prop('checked'));
        updateSelectedCount();
    });
    
    // Individual checkboxes
    $(document).on('change', '.exam-checkbox', function() {
        updateSelectedCount();
        updateSelectAll();
    });
    
    // Clear selection
    $('#clearSelection').click(function() {
        $('.exam-checkbox').prop('checked', false);
        $('#selectAllExams').prop('checked', false);
        updateSelectedCount();
    });
    
    // Select All in modal
    $('#selectAll').change(function() {
        $('.modal-exam-checkbox').prop('checked', $(this).prop('checked'));
    });
});

function loadDraftExams() {
    $.ajax({
        url: '{{ route("admin.marks.draft-exams") }}',
        type: 'GET',
        success: function(exams) {
            let html = '';
            if (exams.length === 0) {
                html = '<tr><td colspan="6" class="text-center py-4">No draft exams found.</td></tr>';
            } else {
                exams.forEach(function(exam) {
                    html += `
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input modal-exam-checkbox" value="${exam.id}">
                            </td>
                            <td>${new Date(exam.exam_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                            <td>
                                <strong>${exam.subject_name}</strong>
                                <br><small class="text-muted">${exam.subject_code}</small>
                            </td>
                            <td>${exam.class_name}</td>
                            <td class="text-center">
                                <span class="badge bg-warning">${exam.draft_marks_count}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">${exam.total_marks_count}</span>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#draftExamsList').html(html);
        },
        error: function() {
            $('#draftExamsList').html('<tr><td colspan="6" class="text-center text-danger py-4">Error loading exams.</td></tr>');
        }
    });
}

function updateSelectedCount() {
    const count = $('.exam-checkbox:checked').length;
    $('#selectedCount').text(count);
}

function updateSelectAll() {
    const total = $('.exam-checkbox').length;
    const checked = $('.exam-checkbox:checked').length;
    $('#selectAllExams').prop('checked', total === checked && total > 0);
}

function publishSelected() {
    const selectedExams = [];
    $('.exam-checkbox:checked').each(function() {
        selectedExams.push($(this).val());
    });
    
    if (selectedExams.length === 0) {
        alert('Please select at least one exam to publish.');
        return;
    }
    
    if (!confirm(`Are you sure you want to publish ${selectedExams.length} selected exam(s)? All draft marks will be published.`)) {
        return;
    }
    
    publishExams(selectedExams);
}

function publishSelectedFromModal() {
    const selectedExams = [];
    $('.modal-exam-checkbox:checked').each(function() {
        selectedExams.push($(this).val());
    });
    
    if (selectedExams.length === 0) {
        alert('Please select at least one exam to publish.');
        return;
    }
    
    if (!confirm(`Are you sure you want to publish ${selectedExams.length} selected exam(s)?`)) {
        return;
    }
    
    $('#publishSelectedBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Publishing...');
    
    publishExams(selectedExams);
}

function publishExams(examIds) {
    $.ajax({
        url: '{{ route("admin.marks.bulk-publish") }}',
        type: 'POST',
        data: {
            exam_ids: examIds,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#bulkPublishModal').modal('hide');
                
                // Show success message
                toastr.success(response.message);
                
                // Reload page after 1.5 seconds
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error publishing exams. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert(errorMsg);
            console.error(xhr);
        },
        complete: function() {
            $('#publishSelectedBtn').prop('disabled', false).html('<i class="fas fa-rocket me-1"></i> Publish Selected');
        }
    });
}

// Toastr configuration (if not already included)
if (typeof toastr !== 'undefined') {
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };
}
</script>
@endpush

@push('styles')
<style>
.table-warning {
    background-color: #fff3cd !important;
}
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
#draftExamsTable {
    font-size: 0.9rem;
}
#draftExamsTable tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}
.badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
}
.btn-group .btn {
    margin: 0 2px;
}
</style>
@endpush