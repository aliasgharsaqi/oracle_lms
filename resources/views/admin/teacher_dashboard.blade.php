@extends('layouts.admin')

@section('title', 'User Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-muted mb-0">Here are the tools available for your role: <strong>{{ Auth::user()->roles->first()->name ?? 'User' }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Your Quick Actions</h3>
                </div>
                <div class="card-body">
                    <p>This is your dedicated dashboard. From here you can access the tools and features relevant to your role.</p>

                    <div class="row mt-4 gy-3">

                        @can('Manage Schedules')
                        <div class="col-md-4">
                            <a href="{{ route('schedules.index') }}" class="btn btn-primary w-100 p-3">
                                <i class="bi bi-calendar3 me-2"></i> Manage Schedules
                            </a>
                        </div>
                        @endcan

                        @can('Manage Marks')
                        <div class="col-md-4">
                            <a href="{{ route('marks.index') }}" class="btn btn-success w-100 p-3">
                                <i class="bi bi-pencil-square me-2"></i> Enter Marks
                            </a>
                        </div>
                        @endcan

                        @can('View Admission')
                        <div class="col-md-4">
                            <a href="{{ route('students.index') }}" class="btn btn-info w-100 p-3">
                                <i class="bi bi-people-fill me-2"></i> View Students
                            </a>
                        </div>
                        @endcan

                        {{-- Add more conditional links here based on permissions --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection