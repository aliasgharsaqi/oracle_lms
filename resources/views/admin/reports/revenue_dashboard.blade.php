@extends('layouts.admin')

@section('title', 'Revenue Dashboard')
@section('page-title', 'Financial Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Filter Card -->
    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="custom-card-header bg-primary text-white rounded-top-4">
            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-funnel-fill me-2"></i>Filter Dashboard</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('reports.revenue_dashboard') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="year" class="form-label fw-semibold">Year</label>
                        <select class="form-select form-select-lg" name="year" id="yearFilter">
                            <option value="">All Years</option>
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ ($filters['year'] ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="month" class="form-label fw-semibold">Month</label>
                        <select class="form-select form-select-lg" name="month" id="monthFilter">
                            <option value="">All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ ($filters['month'] ?? '') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label fw-semibold">Start Date</label>
                        <input type="date" class="form-control form-control-lg" name="start_date" id="startDateFilter" value="{{ $filters['start_date'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label fw-semibold">End Date</label>
                        <input type="date" class="form-control form-control-lg" name="end_date" id="endDateFilter" value="{{ $filters['end_date'] ?? '' }}">
                    </div>
                    <div class="col-md-2 d-grid">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-lg btn-primary" title="Apply Filters"><i class="bi bi-search"></i></button>
                            <a href="{{ route('reports.revenue_dashboard') }}" class="btn btn-lg btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-clockwise"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @php
        $filterTitle = 'Showing All-Time Data';
        if($filters['start_date'] && $filters['end_date']) {
            $filterTitle = 'Data From ' . \Carbon\Carbon::parse($filters['start_date'])->format('d M, Y') . ' To ' . \Carbon\Carbon::parse($filters['end_date'])->format('d M, Y');
        } elseif ($filters['year'] && $filters['month']) {
            $filterTitle = 'Data For ' . \Carbon\Carbon::create()->month($filters['month'])->format('F') . ', ' . $filters['year'];
        } elseif ($filters['year']) {
            $filterTitle = 'Data For Year ' . $filters['year'];
        }
    @endphp
    <div class="mb-3"><span class="badge bg-secondary fs-6 rounded-pill px-3 py-2 shadow-sm">{{ $filterTitle }}</span></div>


    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100 bg-success text-white">
                <div class="card-body text-center p-4">
                    <h6 class="card-title text-uppercase mb-2"><i class="bi bi-cash-stack me-2"></i>Total Revenue Collected</h6>
                    <h2 class="display-5 fw-bold mb-0">PKR {{ number_format($totalCollected, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100 bg-warning text-dark">
                <div class="card-body text-center p-4">
                    <h6 class="card-title text-uppercase mb-2"><i class="bi bi-hourglass-split me-2"></i>Total Pending Clearance</h6>
                    <h2 class="display-5 fw-bold mb-0">PKR {{ number_format($totalPending, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100 bg-info text-white">
                <div class="card-body text-center p-4">
                    <h6 class="card-title text-uppercase mb-2"><i class="bi bi-graph-up-arrow me-2"></i>Total Expected Revenue</h6>
                    <h2 class="display-5 fw-bold mb-0">PKR {{ number_format($totalExpected, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Breakdowns -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line-fill me-2"></i>Monthly Revenue Collection ({{ $filters['year'] ?? now()->year }})</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2"></i>Collected Fee Breakdown</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center fs-5 py-3">
                            Tuition Fees
                            <span class="badge bg-primary rounded-pill">PKR {{ number_format($feeBreakdown->tuition ?? 0, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fs-5 py-3">
                            Admission Fees
                            <span class="badge bg-info rounded-pill">PKR {{ number_format($feeBreakdown->admission ?? 0, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fs-5 py-3">
                            Examination Fees
                            <span class="badge bg-secondary rounded-pill">PKR {{ number_format($feeBreakdown->examination ?? 0, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fs-5 py-3">
                            Other Fees
                            <span class="badge bg-light text-dark rounded-pill">PKR {{ number_format($feeBreakdown->other ?? 0, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Defaulters List -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-danger text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Student Defaulter List (3+ Months Due)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">Student Name</th>
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Father's Name</th>
                            <th class="py-3 px-4">Father's Phone</th>
                            <th class="py-3 px-4">Total Amount Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($defaulters as $student)
                        <tr>
                            <td class="py-3 px-4 fw-bold align-middle">{{ $student->user->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 align-middle">{{ $student->schoolClass->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 align-middle">{{ $student->father_name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 align-middle">{{ $student->father_phone ?? 'N/A' }}</td>
                            <td class="py-3 px-4 fw-bold text-danger align-middle">PKR {{ number_format($student->total_due, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted p-5">
                                <div class="fs-4">🎉</div>
                                <div>No defaulters found. Great job!</div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyData = @json($monthlyRevenue);
        
        const allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        const chartData = allMonths.map(monthName => {
            const monthData = monthlyData.find(d => d.month === monthName);
            return monthData ? monthData.total : 0;
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: allMonths,
                datasets: [{
                    label: 'Revenue Collected (PKR)',
                    data: chartData,
                    backgroundColor: 'rgba(22, 163, 74, 0.7)',
                    borderColor: 'rgba(22, 163, 74, 1)',
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'PKR ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'PKR ' + context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // --- Filter Logic ---
        const yearFilter = document.getElementById('yearFilter');
        const monthFilter = document.getElementById('monthFilter');
        const startDateFilter = document.getElementById('startDateFilter');
        const endDateFilter = document.getElementById('endDateFilter');

        function handleDateInput() {
            if (startDateFilter.value || endDateFilter.value) {
                yearFilter.value = '';
                monthFilter.value = '';
                yearFilter.disabled = true;
                monthFilter.disabled = true;
            } else {
                yearFilter.disabled = false;
                monthFilter.disabled = false;
            }
        }

        function handleYearMonthInput() {
            if (yearFilter.value || monthFilter.value) {
                startDateFilter.value = '';
                endDateFilter.value = '';
                startDateFilter.disabled = true;
                endDateFilter.disabled = true;
            } else {
                startDateFilter.disabled = false;
                endDateFilter.disabled = false;
            }
        }

        startDateFilter.addEventListener('input', handleDateInput);
        endDateFilter.addEventListener('input', handleDateInput);
        yearFilter.addEventListener('input', handleYearMonthInput);
        monthFilter.addEventListener('input', handleYearMonthInput);

        // Run on page load to set initial state
        handleDateInput();
        handleYearMonthInput();
    });
</script>
@endpush

