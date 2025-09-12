@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
@endpush

@section('content')
<div class="py-4">
  <!-- Page Header -->
  <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-3">
    <div>
      <h1 class="text-2xl font-bold text-dark mb-1">Welcome back, Admin User!</h1>
      <p class="text-gray-500 mb-0">
        Here’s your overview for today,
        <strong>{{ \Carbon\Carbon::now()->format('l, F jS') }}</strong>.
      </p>
    </div>
    <a href="#"
       class="bts hover-card text-white shadow px-6 py-3 rounded-xl text-lg hidden sm:inline-flex items-center">
      <i class="bi bi-download me-2"></i>Generate Report
    </a>
  </div>

  <!-- Stats Row -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Students -->
    <div class="bg-light shadow-lg border-0 rounded-2xl shortcut-card">
      <div class="card-body hover-card p-4 flex justify-between items-center">
        <div>
          <h6 class="uppercase text-gray-500 font-bold mb-2">Total Students</h6>
          <h3 class="font-bold text-primary mb-0">{{ $user }}</h3>
        </div>
        <div class="icon-box bg-primary/10 text-primary rounded-full flex items-center justify-center p-3">
          <i class="bi bi-people-fill"></i>
        </div>
      </div>
    </div>

    <!-- Total Staff -->
    <div class="bg-light shadow-lg border-0 rounded-2xl shortcut-card">
      <div class="card-body hover-card p-4 flex justify-between items-center">
        <div>
          <h6 class="uppercase text-gray-500 font-bold mb-2">Total Staff</h6>
          <h3 class="font-bold text-success mb-0">{{ $staff }}</h3>
        </div>
        <div class="icon-box bg-green-500/10 text-green-600 rounded-full flex items-center justify-center p-3">
          <i class="bi bi-person-workspace"></i>
        </div>
      </div>
    </div>

    <!-- Courses Offered -->
    <div class="bg-light shadow-lg border-0 rounded-2xl shortcut-card">
      <div class="card-body hover-card p-4 flex justify-between items-center">
        <div>
          <h6 class="uppercase text-gray-500 font-bold mb-2">Courses Offered</h6>
          <h3 class="font-bold text-warning mb-0">{{ $course }}</h3>
        </div>
        <div class="icon-box bg-yellow-500/10 text-yellow-500 rounded-full flex items-center justify-center p-3">
          <i class="bi bi-journal-bookmark-fill"></i>
        </div>
      </div>
    </div>

    <!-- Pending Issues -->
    <div class="bg-light shadow-lg border-0 rounded-2xl shortcut-card">
      <div class="card-body hover-card p-4 flex justify-between items-center">
        <div>
          <h6 class="uppercase text-gray-500 font-bold mb-2">Pending Issues</h6>
          <h3 class="font-bold text-danger mb-0">3</h3>
        </div>
        <div class="icon-box bg-red-500/10 text-red-500 rounded-full flex items-center justify-center p-3">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Content Row -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <!-- Chart Column -->
    <div class="lg:col-span-2 hover-card">
      <div class="card border-0 shadow rounded-2xl">
        <div class="card-header card-header-gradient py-3 bts rounded-t-2xl">
          <h6 class="m-0 text-white flex items-center">
            <i class="bi bi-graph-up-arrow me-2"></i>Student Enrollment Trends
          </h6>
        </div>
        <div class="card-body">
          <div class="h-[420px]">
            <canvas id="enrollmentChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity Column -->
    <div>
      <div class="card hover-card shadow border-0 rounded-2xl">
        <div class="card-header py-3 rounded-t-2xl text-white font-semibold"
             style="background: linear-gradient(135deg, #667eea, #764ba2);">
          <i class="bi bi-clock-history me-2"></i>Recent Activity
        </div>
        <div class="divide-y">
          <a href="#" class="flex items-start p-3 hover:bg-gray-50">
            <i class="bi bi-person-plus-fill text-green-500 text-lg me-3"></i>
            <div>
              <div class="font-bold">New student registered</div>
              Ali Khan
              <div class="text-sm text-gray-500 mt-1">15 minutes ago</div>
            </div>
          </a>
          <a href="#" class="flex items-start p-3 hover:bg-gray-50">
            <i class="bi bi-calendar-check text-sky-500 text-lg me-3"></i>
            <div>
              <div class="font-bold">Event updated</div>
              Annual Sports Day
              <div class="text-sm text-gray-500 mt-1">1 hour ago</div>
            </div>
          </a>
          <a href="#" class="flex items-start p-3 hover:bg-gray-50">
            <i class="bi bi-receipt text-yellow-500 text-lg me-3"></i>
            <div>
              <div class="font-bold">Fee payment received</div>
              From Fatima Jilani
              <div class="text-sm text-gray-500 mt-1">3 hours ago</div>
            </div>
          </a>
          <a href="#" class="flex items-start p-3 hover:bg-gray-50">
            <i class="bi bi-flag-fill text-red-500 text-lg me-3"></i>
            <div>
              <div class="font-bold">Support ticket raised</div>
              By a parent
              <div class="text-sm text-gray-500 mt-1">Yesterday</div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Extra Row for LMS Features -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Quick Access -->
    <div class="card shadow-lg rounded-2xl hover-card h-full">
      <div class="card-header py-3 text-white font-bold rounded-t-2xl"
           style="background: linear-gradient(135deg, #007bff, #00c6ff);">
        <i class="bi bi-lightning-charge-fill me-2"></i> Quick Access
      </div>
      <div class="card-body flex flex-col gap-3">
        <a href="#" class="btn btn-outline-primary w-full flex items-center justify-center">
          <i class="bi bi-person-lines-fill me-2"></i> Manage Students
        </a>
        <a href="#" class="btn btn-outline-success w-full flex items-center justify-center">
          <i class="bi bi-person-badge-fill me-2"></i> Manage Teachers
        </a>
        <a href="#" class="btn btn-outline-warning w-full flex items-center justify-center">
          <i class="bi bi-journal-text me-2"></i> Manage Courses
        </a>
        <a href="#" class="btn btn-outline-danger w-full flex items-center justify-center">
          <i class="bi bi-cash-coin me-2"></i> Fee Management
        </a>
      </div>
    </div>

    <!-- Upcoming Events -->
    <div class="card shadow-lg rounded-2xl h-full">
      <div class="card-header py-3 text-white font-bold rounded-t-2xl"
           style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <i class="bi bi-calendar-event-fill me-2"></i> Upcoming Events
      </div>
      <div class="card-body">
        <ul class="space-y-4">
          <li class="flex items-start">
            <span class="bg-primary text-white rounded-full p-2 me-3">
              <i class="bi bi-megaphone-fill"></i>
            </span>
            <div>
              <h6 class="font-bold mb-1">PTM Scheduled</h6>
              <small class="text-gray-500">Monday, 10 AM</small>
            </div>
          </li>
          <li class="flex items-start">
            <span class="bg-green-500 text-white rounded-full p-2 me-3">
              <i class="bi bi-mortarboard-fill"></i>
            </span>
            <div>
              <h6 class="font-bold mb-1">Mid-term Exams</h6>
              <small class="text-gray-500">Starting Next Week</small>
            </div>
          </li>
          <li class="flex items-start">
            <span class="bg-yellow-500 text-white rounded-full p-2 me-3">
              <i class="bi bi-award-fill"></i>
            </span>
            <div>
              <h6 class="font-bold mb-1">Science Exhibition</h6>
              <small class="text-gray-500">Friday, Main Hall</small>
            </div>
          </li>
          <li class="flex items-start">
            <span class="bg-red-500 text-white rounded-full p-2 me-3">
              <i class="bi bi-music-note-beamed"></i>
            </span>
            <div>
              <h6 class="font-bold mb-1">Annual Concert</h6>
              <small class="text-gray-500">20th March, 6 PM</small>
            </div>
          </li>
          <li class="flex items-start">
            <span class="bg-sky-500 text-white rounded-full p-2 me-3">
              <i class="bi bi-bus-front-fill"></i>
            </span>
            <div>
              <h6 class="font-bold mb-1">Educational Trip</h6>
              <small class="text-gray-500">25th March, Lahore Museum</small>
            </div>
          </li>
          <li class="flex items-start">
            <span class="bg-black text-white rounded-full p-2 me-3">
              <i class="bi bi-trophy-fill"></i>
            </span>
            <div>
              <h6 class="font-bold mb-1">Sports Gala</h6>
              <small class="text-gray-500">30th March, School Ground</small>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Attendance Overview -->
    <div class="card shadow-lg rounded-2xl hover-card h-full">
      <div class="card-header py-3 text-white font-bold rounded-t-2xl"
           style="background: linear-gradient(135deg, #28a745, #20c997);">
        <i class="bi bi-clipboard2-check-fill me-2"></i> Attendance Overview
      </div>
      <div class="card-body flex items-center justify-center">
        <canvas id="attendanceChart" class="h-[220px]"></canvas>
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
