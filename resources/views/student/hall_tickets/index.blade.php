@extends('layouts.student')

@section('page-title', 'My Hall Tickets')

@section('main')
<div class="hall-ticket-wrapper">
<div class="container py-5">

    <!-- Header -->
    <div class="ticket-header p-4 mb-4 rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold text-white mb-1">
                    <i class="fas fa-id-card me-2"></i>My Hall Tickets
                </h2>
                <small class="text-light opacity-75">
                    View and download your exam hall tickets
                </small>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">

            <div class="table-responsive">
                <table id="hallTicketTable" class="table align-middle ticket-table w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Exam Date</th>
                            <th>Exam</th>
                            <th>Session</th>
                            <th>Code</th>
                            <th>Hall</th>
                            <th>Table</th>
                            <th>Seat</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hallTickets as $ticket)
                            @php
                                $exam = $ticket->allocation->exam;
                                $hall = $ticket->allocation->hall;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <span class="date-badge">
                                        {{ \Carbon\Carbon::parse($ticket->allocation->exam_date)->format('d M Y') }}
                                    </span>
                                </td>

                                <td>
                                    <strong>{{ $exam->subject_name ?? '-' }}</strong>
                                </td>

                                <td>
                                    <span class="session-badge">
                                        {{ $exam->time_session ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="code-badge">
                                        {{ $exam->subject_code ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <strong>{{ $hall->hall_name ?? '-' }}</strong><br>
                                    <small class="text-muted">
                                        {{ $hall->building ?? '' }}
                                    </small>
                                </td>

                                <td>{{ $ticket->table_number }}</td>
                                <td>{{ $ticket->seat_number }}</td>

                                <td class="text-center">
                                    <a href="{{ route('student.hall-tickets.download', $ticket->id) }}"
                                       class="btn action-btn download-btn">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
</div>

<!-- DataTables CDN -->
<link rel="stylesheet"
href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#hallTicketTable').DataTable({
        responsive: true,
        pageLength: 5,
        autoWidth: false,
        language: {
            search: "Search Exam:"
        }
    });
});
</script>

<style>

/* Wrapper Scope */
.hall-ticket-wrapper .ticket-header {
    background: linear-gradient(135deg, #640d3c, #9D71C9);
}

/* Override DataTable header */
.hall-ticket-wrapper table.dataTable thead th {
    background: #f4f0fa !important;
    color: #640d3c !important;
    font-weight: 600 !important;
    border-bottom: 2px solid #640d3c !important;
}

/* Badges */
.date-badge {
    background: #f1e6ff;
    color: #640d3c;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}

.session-badge {
    background: #fff3cd;
    color: #856404;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
}

.code-badge {
    background: linear-gradient(135deg, #9D71C9, #640d3c);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

/* Action Buttons */
.action-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.3s ease;
}

.download-btn {
    background: linear-gradient(135deg, #640d3c, #9D71C9);
    color: white;
}

.download-btn:hover {
    opacity: 0.85;
    transform: scale(1.05);
    color: white;
}

/* DataTable Styling */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #640d3c !important;
    color: white !important;
    border-radius: 50px !important;
}

.dataTables_wrapper .dataTables_filter input {
    border-radius: 20px;
    border: 1px solid #640d3c;
    padding: 5px 12px;
}

/* Responsive */
@media (max-width: 768px) {
    .ticket-header h2 {
        font-size: 1.4rem;
    }

    .table td {
        font-size: 0.85rem;
    }
}


/* Center all header text */
.hall-ticket-wrapper table.dataTable thead th {
    text-align: center !important;
    vertical-align: middle !important;
}

</style>
@endsection