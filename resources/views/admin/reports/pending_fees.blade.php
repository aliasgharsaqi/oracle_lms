@extends('layouts.admin')

@section('title', 'Pending Fees Report')
@section('page-title', 'Pending & Partially Paid Vouchers')

@section('content')
<div class="container-fluid">
    <!-- Filter Card -->
    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="custom-card-header bg-primary text-white rounded-top-4">
            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-funnel-fill me-2"></i>Filter Reports</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('reports.pending_fees') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="class_id" class="form-label fw-semibold">Filter by Class</label>
                        <select class="form-select form-select-lg" name="class_id">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="month" class="form-label fw-semibold">Filter by Month</label>
                        <input type="month" class="form-control form-control-lg" name="month" value="{{ $selectedMonth }}">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-lg btn-primary"><i class="bi bi-search me-1"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pending Fees Report Card -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-circle-fill me-2 text-danger"></i>Pending Vouchers Report</h5>
            @php
                $totalBalance = $pendingFees->sum(function($fee) {
                    return $fee->amount_due - $fee->amount_paid;
                });
            @endphp
            <div class="bg-danger text-white rounded-pill px-3 py-2 fw-bold">
                Total Balance Due: PKR {{ number_format($totalBalance, 2) }}
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="pendingFeesTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">Student Name</th>
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Voucher Month</th>
                            <th class="py-3 px-4">Amount Due</th>
                            <th class="py-3 px-4">Amount Paid</th>
                            <th class="py-3 px-4">Balance Due</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingFees as $fee)
                        <tr>
                            <td class="py-3 px-4 fw-bold align-middle">{{ $fee->student?->user?->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 align-middle">{{ $fee->student?->schoolClass?->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 align-middle">{{ \Carbon\Carbon::parse($fee->voucher_month)->format('F Y') }}</td>
                            <td class="py-3 px-4 align-middle">PKR {{ number_format($fee->amount_due, 2) }}</td>
                            <td class="py-3 px-4 align-middle">PKR {{ number_format($fee->amount_paid ?? 0, 2) }}</td>
                            <td class="py-3 px-4 fw-bold text-danger align-middle">PKR {{ number_format($fee->amount_due - ($fee->amount_paid ?? 0), 2) }}</td>
                            <td class="py-3 px-4 align-middle">
                                @if($fee->status == 'partial')
                                    <span class="badge bg-info-soft text-info">Partial</span>
                                @else
                                    <span class="badge bg-warning-soft text-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted p-5">No pending fees found for the selected criteria.</td>
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
    $(document).ready(function() {
        $('#pendingFeesTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            pageLength: 10,
            responsive: true
        });
    });
</script>
@endpush
