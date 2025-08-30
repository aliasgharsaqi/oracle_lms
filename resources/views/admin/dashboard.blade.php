@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
@endpush

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Welcome back, Admin User!</h1>
            <p class="text-muted mb-0">Here’s your overview for today,
                <strong>{{ \Carbon\Carbon::now()->format('l, F jS') }}</strong>.
            </p>
        </div>
        <a href="#" style="padding: 10px 20px; border-radius: 10px;" class="bts hover-card text-white shadow btn-lg px-4 d-none d-sm-inline-block">
            <i class="bi bi-download me-2"></i>Generate Report
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <!-- Total Students -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg border-0 rounded-4 shortcut-card bg-light">
                <div class="card-body hover-card p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Total Students</h6>
                        <h3 class="fw-bold text-primary mb-0">{{ $user }}</h3>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg border-0 rounded-4 shortcut-card bg-light">
                <div class="card-body hover-card p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Total Staff</h6>
                        <h3 class="fw-bold text-success mb-0">{{ $staff }}</h3>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Offered -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg border-0 rounded-4 shortcut-card bg-light">
                <div class="card-body p-4 d-flex hover-card justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Courses Offered</h6>
                        <h3 class="fw-bold text-warning mb-0">{{ $course }}</h3>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Issues -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg border-0 rounded-4 shortcut-card bg-light">
                <div class="card-body p-4 d-flex justify-content-between hover-card align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-2">Pending Issues</h6>
                        <h3 class="fw-bold text-danger mb-0">3</h3>
                    </div>
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Chart Column -->
        <div class="col-lg-8 mb-4 hover-card">
            <div class="card border-0 shadow rounded-4" style="">
                <div class="card-header card-header-gradient py-3 bts">
                    <h6 class="m-0 text-white"><i class="bi bi-graph-up-arrow me-2"></i>Student Enrollment Trends</h6>
                </div>
                <div class="card-body">
                    <div style="height: 420px;">
                        <canvas id="enrollmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Column -->
        <div class="col-lg-4 mb-4">
            <div class="card hover-card shadow border-0 rounded-4">
                <div class="card-header card-header-gradient py-3"  style="background: linear-gradient(135deg, #667eea, #764ba2); ">
                    <h6 class="m-0 text-white"><i class="bi bi-clock-history me-2"></i>Recent Activity</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start">
                        <i class="bi bi-person-plus-fill text-success fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">New student registered</div>
                            Ali Khan
                            <div class="small text-muted mt-1">15 minutes ago</div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start">
                        <i class="bi bi-calendar-check text-info fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">Event updated</div>
                            Annual Sports Day
                            <div class="small text-muted mt-1">1 hour ago</div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start">
                        <i class="bi bi-receipt text-warning fs-5 me-3"></i>
                        <div>
                            <div class="fw-bold">Fee payment received</div>
                            From Fatima Jilani
                            <div class="small text-muted mt-1">3 hours ago</div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-start">
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

    <!-- Extra Row for LMS Features -->
  <div class="row g-4">
    <!-- Quick Access -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-lg rounded-4 h-100 hover-card">
            <div class="card-header py-3 text-white fw-bold"
                 style="background: linear-gradient(135deg, #007bff, #00c6ff); border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                <i class="bi bi-lightning-charge-fill me-2"></i> Quick Access
            </div>
            <div class="card-body d-grid gap-3">
                <a href="#" class="btn btn-outline-primary btn-custom w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-lines-fill me-2"></i> Manage Students
                </a>
                <a href="#" class="btn btn-outline-success btn-custom w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-badge-fill me-2"></i> Manage Teachers
                </a>
                <a href="#" class="btn btn-outline-warning btn-custom w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-journal-text me-2"></i> Manage Courses
                </a>
                <a href="#" class="btn btn-outline-danger btn-custom w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-cash-coin me-2"></i> Fee Management
                </a>
            </div>
        </div>
    </div>

    <!-- Notice Board -->
    <div class="col-lg-4">
    <div class="card border-0 shadow-lg rounded-4 h-100">
        <div class="card-header py-3 text-white fw-bold"
             style="background: linear-gradient(135deg, #667eea, #764ba2); border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
            <i class="bi bi-calendar-event-fill me-2"></i> Upcoming Events
        </div>
        <div class="card-body">
            <ul class="timeline list-unstyled">
                <!-- Event 1 -->
                <li class="timeline-item mb-4">
                    <div class="d-flex align-items-start">
                        <span class="timeline-icon bg-primary text-white rounded-circle me-3">
                            <i class="bi bi-megaphone-fill"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-1">PTM Scheduled</h6>
                            <small class="text-muted">Monday, 10 AM</small>
                        </div>
                    </div>
                </li>
                <!-- Event 2 -->
                <li class="timeline-item mb-4">
                    <div class="d-flex align-items-start">
                        <span class="timeline-icon bg-success text-white rounded-circle me-3">
                            <i class="bi bi-mortarboard-fill"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-1">Mid-term Exams</h6>
                            <small class="text-muted">Starting Next Week</small>
                        </div>
                    </div>
                </li>
                <!-- Event 3 -->
                <li class="timeline-item mb-4">
                    <div class="d-flex align-items-start">
                        <span class="timeline-icon bg-warning text-white rounded-circle me-3">
                            <i class="bi bi-award-fill"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-1">Science Exhibition</h6>
                            <small class="text-muted">Friday, Main Hall</small>
                        </div>
                    </div>
                </li>
                <!-- Event 4 -->
                <li class="timeline-item mb-4">
                    <div class="d-flex align-items-start">
                        <span class="timeline-icon bg-danger text-white rounded-circle me-3">
                            <i class="bi bi-music-note-beamed"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-1">Annual Concert</h6>
                            <small class="text-muted">20th March, 6 PM</small>
                        </div>
                    </div>
                </li>
                <!-- Event 5 -->
                <li class="timeline-item mb-4">
                    <div class="d-flex align-items-start">
                        <span class="timeline-icon bg-info text-white rounded-circle me-3">
                            <i class="bi bi-bus-front-fill"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-1">Educational Trip</h6>
                            <small class="text-muted">25th March, Lahore Museum</small>
                        </div>
                    </div>
                </li>
                <!-- Event 6 -->
                <li class="timeline-item">
                    <div class="d-flex align-items-start">
                        <span class="timeline-icon bg-dark text-white rounded-circle me-3">
                            <i class="bi bi-trophy-fill"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-1">Sports Gala</h6>
                            <small class="text-muted">30th March, School Ground</small>
                        </div>
                    </div>
                </li>
            </ul>

           
        </div>
    </div>
</div>


    <!-- Attendance Overview -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-lg rounded-4 h-100 hover-card">
            <div class="card-header py-3 text-white fw-bold"
                 style="background: linear-gradient(135deg, #28a745, #20c997); border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                <i class="bi bi-clipboard2-check-fill me-2"></i> Attendance Overview
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="attendanceChart" style="height: 220px;"></canvas>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
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
                pointBackgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
}

// Attendance Chart
const ctx2 = document.getElementById('attendanceChart');
if (ctx2) {
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                data: [85, 10, 5],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}
</script>
@endpush
