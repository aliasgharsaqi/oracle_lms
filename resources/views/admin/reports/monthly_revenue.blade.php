@extends('layouts.admin')

@section('title', 'Monthly Revenue Report')
@section('page-title', 'Monthly Revenue Report')

@section('content')
<div class="row justify-content-center">
    <div class="mb-10">
        <div class="card shadow-lg border-0 rounded-4">
            <!-- Card Header -->
            <div
                class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-bar-chart-line me-2"></i> Monthly Revenue Report
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                <!-- 🔹 Filter Form -->
                <form method="GET" action="{{ route('reports.monthlyRevenue') }}" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <!-- Year -->
                        <div class="col-md-6">
                            <label for="year" class="form-label fw-semibold">Year</label>
                            <select name="year" class="form-select rounded-3 shadow-sm">
                                <option value="">Select Year</option>
                                @foreach(range(date('Y'), date('Y')-5) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month -->
                        <div class="col-md-6">
                            <label for="month" class="form-label fw-semibold">Month</label>
                            <select name="month" class="form-select rounded-3 shadow-sm">
                                <option value="">Select Month</option>
                                @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Button -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div
                class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2"></i> Revenue Records
                </h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="teachersTable">
                        <thead>
                            <tr>
                                <th class="border px-2 py-1">Month</th>
                                <th class="border px-2 py-1">Year</th>
                                <th class="border px-2 py-1">Total Collected</th>
                                <th class="border px-2 py-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyIncome as $income)
                            <tr>
                                <td class="border px-2 py-1">
                                    {{ \Carbon\Carbon::create()->month($income->month)->format('F') }}
                                </td>
                                <td class="border px-2 py-1">{{ $income->year }}</td>
                                <td class="border px-2 py-1">{{ number_format($income->total_collected, 2) }}</td>
                                <td class="border px-2 py-1">
                                    @if($income->month && $income->year)
                                    <a href="{{ route('reports.paidFees', ['month' => $income->year . '-' . str_pad($income->month, 2, '0', STR_PAD_LEFT)]) }}"
                                        class="btn btn-success btn-sm rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i> Paid
                                    </a>

                                    <a href="{{ route('reports.pendingFees', ['month' => $income->year . '-' . str_pad($income->month, 2, '0', STR_PAD_LEFT)]) }}"
                                        class="btn btn-warning btn-sm rounded-pill shadow-sm">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending
                                    </a>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-2">No revenue data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#teachersTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ],
        pageLength: 10,
        responsive: true
    });
});
</script>
@endpush
