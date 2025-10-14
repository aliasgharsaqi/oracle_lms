@extends('layouts.admin')

@section('title', 'Staff Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Welcome, {{ Auth::user()->name }}!</h1>
            <p class="text-muted mb-0">Your quick actions for the <strong>{{ Auth::user()->getRoleNames()->first() ?? 'Staff' }}</strong> role.</p>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4">
         <div class="card-header bg-primary text-white rounded-top-4">
            <h5 class="m-0 fw-bold"><i class="bi bi-tools me-2"></i>Your Tools</h5>
        </div>
        <div class="card-body p-5">
            <p class="fs-5">This is your dedicated dashboard. From here you can access the modules and features relevant to your role.</p>

            <div class="row mt-4 gy-4">
                
                @can('Manage Students')
                <div class="col-md-4">
                    <a href="{{ route('students.index') }}" class="d-block text-center text-decoration-none p-4 bg-light rounded-4 shadow-sm hover-lift">
                        <i class="bi bi-people-fill display-4 text-primary"></i>
                        <h5 class="mt-2 mb-0">Manage Students</h5>
                    </a>
                </div>
                @endcan

                @can('Manage Fees')
                <div class="col-md-4">
                    <a href="{{ route('fees.payments.index') }}" class="d-block text-center text-decoration-none p-4 bg-light rounded-4 shadow-sm hover-lift">
                        <i class="bi bi-cash-coin display-4 text-success"></i>
                        <h5 class="mt-2 mb-0">Fee Collection</h5>
                    </a>
                </div>
                @endcan

                @can('Manage Reports')
                 <div class="col-md-4">
                    <a href="{{ route('reports.revenue_dashboard') }}" class="d-block text-center text-decoration-none p-4 bg-light rounded-4 shadow-sm hover-lift">
                        <i class="bi bi-bar-chart-line-fill display-4 text-info"></i>
                        <h5 class="mt-2 mb-0">View Reports</h5>
                    </a>
                </div>
                @endcan
                
                {{-- Add more conditional quick links for other staff permissions --}}

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