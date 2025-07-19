@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="alert alert-primary" role="alert">
            <h4 class="alert-heading">Welcome back, {{ Auth::user()->name }}!</h4>
            <p>You are logged in as a {{ Auth::user()->role }}. From this central dashboard, you can manage all aspects of the school system assigned to your role.</p>
            <hr>
            <p class="mb-0">Use the navigation menu on the left to access different modules.</p>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-primary shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-75 small">Total Students</div>
                        <div class="h2 mb-0 fw-bold">1,250</div>
                    </div>
                    <i class="bi bi-people-fill" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>

     <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-success shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-75 small">Total Staff</div>
                        <div class="h2 mb-0 fw-bold">85</div>
                    </div>
                    <i class="bi bi-person-workspace" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-warning shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-75 small">Courses Offered</div>
                        <div class="h2 mb-0 fw-bold">48</div>
                    </div>
                    <i class="bi bi-journal-bookmark-fill" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-danger shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-75 small">Pending Issues</div>
                        <div class="h2 mb-0 fw-bold">3</div>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <p>This area will soon contain a log of recent system events and activities.</p>
            </div>
        </div>
    </div>
</div>
@endsection
