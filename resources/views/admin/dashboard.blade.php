
@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

{{-- This custom CSS is key to the new design. For production, move this to your app.css file. --}}
@push('styles')
@endpush


@section('content')
<!-- Main Content -->
<div class=" py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-0">Welcome back, Admin User!</h1>
            <p class="text-muted mb-0">Here's your overview for today, {{ \Carbon\Carbon::now()->format('l, F jS') }}.
            </p>
        </div>
        <a href="#" class="btn btn-primary shadow-sm d-none d-sm-inline-block">
            <i class="bi bi-download me-2"></i>Generate Report
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">

        <!-- Total Students -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Total Students</h6>
                        <h3 class="fw-bold text-primary mb-0">{{ $user }}</h3>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width:60px; height:60px;">
                        <i class="bi bi-people-fill fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Total Staff</h6>
                        <h3 class="fw-bold text-success mb-0">{{ $staff }}</h3>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                        style="width:60px; height:60px;">
                        <i class="bi bi-person-workspace fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Offered -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Courses Offered</h6>
                        <h3 class="fw-bold text-warning mb-0">{{ $course }}</h3>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center"
                        style="width:60px; height:60px;">
                        <i class="bi bi-journal-bookmark-fill fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Issues -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Pending Issues</h6>
                        <h3 class="fw-bold text-danger mb-0">3</h3>
                    </div>
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center"
                        style="width:60px; height:60px;">
                        <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Chart Column -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Student Enrollment Trends</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="enrollmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Column -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Recent Activity</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start border-0">
                        <i class="bi bi-person-plus-fill text-success fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">New student registered</div>
                            Ali Khan
                            <div class="small text-muted mt-1">15 minutes ago</div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start border-0">
                        <i class="bi bi-calendar-check text-info fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">Event updated</div>
                            Annual Sports Day
                            <div class="small text-muted mt-1">1 hour ago</div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start border-0">
                        <i class="bi bi-receipt text-warning fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">Fee payment received</div>
                            From Fatima Jilani
                            <div class="small text-muted mt-1">3 hours ago</div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start border-0">
                        <i class="bi bi-flag-fill text-danger fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">Support ticket raised</div>
                            By a parent
                            <div class="small text-muted mt-1">Yesterday</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
{{-- Include Chart.js from a CDN for the graph --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Enrollment Chart
const ctx = document.getElementById('enrollmentChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'New Enrollments',
                data: [12, 19, 15, 25, 22, 30, 28],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4e73df',
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#4e73df',
                pointHoverBorderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>
@endpush