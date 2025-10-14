@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-muted mb-0">
                Here’s your overview for <strong>{{ $schoolName }}</strong> as of {{ \Carbon\Carbon::now()->format('l, F jS') }}.
            </p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row row-cols-1 row-cols-md-2 {{ Auth::user()->hasRole('Super Admin') ? 'row-cols-xl-4' : 'row-cols-xl-4' }} g-4 mb-4">
        <div class="col">
            <a href="{{ route('reports.revenue_dashboard') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-primary text-white hover-lift">
                    <div class="card-body p-4">
                        <h6 class="card-title text-uppercase mb-2"><i class="bi bi-wallet2 me-2"></i>Total Revenue</h6>
                        <h3 class="fw-bold mb-0">PKR {{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <div class="card shadow-sm border-0 rounded-4 h-100 bg-success text-white">
                <div class="card-body p-4">
                    <h6 class="card-title text-uppercase mb-2"><i class="bi bi-cash-stack me-2"></i>Revenue Today</h6>
                    <h3 class="fw-bold mb-0">PKR {{ number_format($revenueToday, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col">
            <a href="{{ route('reports.revenue_dashboard') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-warning text-dark hover-lift">
                    <div class="card-body p-4">
                        <h6 class="card-title text-uppercase mb-2"><i class="bi bi-hourglass-split me-2"></i>Total Pending Fees</h6>
                        <h3 class="fw-bold mb-0">PKR {{ number_format($totalPending, 2) }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
             <a href="{{ route('students.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-info text-white hover-lift">
                    <div class="card-body p-4">
                        <h6 class="card-title text-uppercase mb-2"><i class="bi bi-people-fill me-2"></i>Total Students</h6>
                        <h3 class="fw-bold mb-0">{{ $studentCount }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('teachers.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-secondary text-white hover-lift">
                    <div class="card-body p-4">
                        <h6 class="card-title text-uppercase mb-2"><i class="bi bi-person-workspace me-2"></i>Total Staff</h6>
                        <h3 class="fw-bold mb-0">{{ $teacherCount }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('classes.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-light text-dark hover-lift">
                    <div class="card-body p-4">
                        <h6 class="card-title text-uppercase mb-2"><i class="bi bi-journal-text me-2"></i>Total Classes</h6>
                        <h3 class="fw-bold mb-0">{{ $classCount }}</h3>
                    </div>
                </div>
            </a>
        </div>
        @if(Auth::user()->hasRole('Super Admin'))
        <div class="col">
            <a href="{{ route('schools.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-dark text-white hover-lift">
                    <div class="card-body p-4">
                        <h6 class="card-title text-uppercase mb-2"><i class="bi bi-building me-2"></i>Total Schools</h6>
                        <h3 class="fw-bold mb-0">{{ $schoolCount }}</h3>
                    </div>
                </div>
            </a>
        </div>
        @endif
    </div>

    <!-- Content Row -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h6 class="m-0 fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>Student Enrollment Trends</h6>
                </div>
                <div class="card-body p-4">
                    <div style="height: 350px;">
                        <canvas id="enrollmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-dark text-white rounded-top-4">
                    <h6 class="m-0 fw-bold"><i class="bi bi-lightning-charge-fill me-2"></i>Quick Access</h6>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        @can('Manage Students')<a href="{{ route('students.index') }}" class="list-group-item list-group-item-action fs-5 py-3"><i class="bi bi-person-lines-fill me-3 text-primary"></i>Manage Students</a>@endcan
                        @can('Manage Teachers')<a href="{{ route('teachers.index') }}" class="list-group-item list-group-item-action fs-5 py-3"><i class="bi bi-person-badge-fill me-3 text-success"></i>Manage Teachers</a>@endcan
                        @can('Manage Classes')<a href="{{ route('classes.index') }}" class="list-group-item list-group-item-action fs-5 py-3"><i class="bi bi-journal-text me-3 text-warning"></i>Manage Classes</a>@endcan
                        @can('Manage Fees')<a href="{{ route('fees.payments.index') }}" class="list-group-item list-group-item-action fs-5 py-3"><i class="bi bi-cash-coin me-3 text-danger"></i>Fee Collection</a>@endcan
                        @can('Manage Reports')<a href="{{ route('reports.revenue_dashboard') }}" class="list-group-item list-group-item-action fs-5 py-3"><i class="bi bi-bar-chart-line-fill me-3 text-info"></i>Revenue Dashboard</a>@endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('enrollmentChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'New Enrollments',
                    data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 40, 38, 45], // Placeholder data
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endpush

