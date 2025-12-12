@extends('layouts.admin')

@section('title', 'Monthly Teacher Diary Report')
@section('page-title', 'Monthly Assignments: ' . ($teacher->user->name ?? 'Select Teacher'))

@section('content')

    {{-- Filter Card --}}
    <div class="card shadow-lg border-0 rounded-4 mb-6">
        <div class="custom-card-header bg-primary text-white rounded-top-4">
            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-funnel-fill me-2"></i>Filter Report</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('teacher_diary.monthly_report') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="teacher_id" class="form-label fw-semibold">Select Teacher</label>
                        <select class="form-select form-select-lg" name="teacher_id" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ $selectedTeacherId == $t->id ? 'selected' : '' }}>
                                    {{ $t->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="month" class="form-label fw-semibold">Select Month</label>
                        <input type="month" class="form-control form-control-lg" name="month" value="{{ $selectedMonth }}" required>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-lg btn-primary"><i class="bi bi-search me-1"></i> Generate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($teacher)
    {{-- Report Card --}}
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-journal-check me-2 text-primary"></i>
                Assignments for {{ $teacher->user->name }} in {{ $monthName }}
            </h5>
            <div class="text-sm text-muted">
                Total Tasks: <span class="fw-bold">{{ $assignments->count() }}</span>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="monthlyReportTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 px-4">Due Date</th>
                            <th class="py-3 px-4">Class & Subject</th>
                            <th class="py-3 px-4">Assignment / Task</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td class="py-3 px-4 align-middle text-nowrap">
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($assignment->due_date)->format('d M, Y') }}</span>
                                @if(\Carbon\Carbon::parse($assignment->due_date)->isPast() && $assignment->status != 'completed' && $assignment->status != 'verified')
                                    <span class="badge bg-danger ms-2">OVERDUE</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 align-middle">
                                <span class="badge bg-info text-dark">{{ $assignment->schoolClass->name ?? 'N/A' }}</span>
                                <span class="badge bg-secondary">{{ $assignment->subject->name ?? 'N/A' }}</span>
                            </td>
                            <td class="py-3 px-4 align-middle">{{ $assignment->homework_assignment }}</td>
                            <td class="py-3 px-4 text-center align-middle">
                                @php
                                    $badge = match($assignment->status) {
                                        'completed' => 'bg-success',
                                        'verified' => 'bg-primary',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ ucfirst($assignment->status) }}</span>
                            </td>
                            <td class="py-3 px-4 align-middle text-muted small">
                                {{ $assignment->teacher_notes ?? 'No notes recorded.' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted p-5">No assignments found for the selected month.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables for the report table
        $('#monthlyReportTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            pageLength: 10,
            responsive: true,
            ordering: true,
            order: [[0, 'asc']] // Default order by Due Date
        });
    });
</script>
@endpush