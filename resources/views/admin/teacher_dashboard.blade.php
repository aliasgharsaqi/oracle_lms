@extends('layouts.admin')

@section('title', 'Teacher Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Welcome, {{ Auth::user()->name }}!</h1>
            <p class="text-muted mb-0">
                Your schedule for today, <strong>{{ \Carbon\Carbon::now()->format('l, F jS') }}</strong>.
            </p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6 col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100 bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center p-4">
                    <div>
                        <h6 class="card-title text-uppercase mb-2">Assigned Classes</h6>
                        <h3 class="fw-bold mb-0">{{ $classCount }}</h3>
                    </div>
                    <div class="fs-1 opacity-50"><i class="bi bi-easel-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100 bg-info text-white">
                <div class="card-body d-flex justify-content-between align-items-center p-4">
                    <div>
                        <h6 class="card-title text-uppercase mb-2">Assigned Subjects</h6>
                        <h3 class="fw-bold mb-0">{{ $subjectCount }}</h3>
                    </div>
                    <div class="fs-1 opacity-50"><i class="bi bi-book-half"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content Row -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-dark text-white rounded-top-4">
                    <h6 class="m-0 fw-bold"><i class="bi bi-calendar-day-fill me-2"></i>Today's Schedule</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-4">Time</th>
                                    <th class="py-3 px-4">Class</th>
                                    <th class="py-3 px-4">Subject</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($todaySchedules as $schedule)
                                    <tr>
                                        <td class="py-3 px-4 fw-bold align-middle">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</td>
                                        <td class="py-3 px-4 align-middle">{{ $schedule->schoolClass->name ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 align-middle">{{ $schedule->subject->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center p-5 text-muted">
                                            <div class="fs-4">🎉</div>
                                            No classes scheduled for today. Enjoy your day!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-dark text-white rounded-top-4">
                    <h6 class="m-0 fw-bold"><i class="bi bi-lightning-charge-fill me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                         @can('Manage Schedules')
                            <a href="{{ route('schedules.index') }}" class="list-group-item list-group-item-action fs-5 py-3"><i class="bi bi-calendar3 me-3 text-primary"></i> View Full Schedule</a>
                         @endcan
                         {{-- You can add more role-specific links here --}}
                         <a href="#" class="list-group-item list-group-item-action fs-5 py-3"><i class="bi bi-card-checklist me-3 text-success"></i> Manage Attendance</a>
                         <a href="#" class="list-group-item list-group-item-action fs-5 py-3"><i class="bi bi-pencil-square me-3 text-warning"></i> Enter Marks/Grades</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
