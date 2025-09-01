@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Profile: ' . $user->name)

@section('content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 text-center py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2"></i> Profile Picture</h5>
            </div>
            <div class="card-body text-center">
                @if($user->user_pic)
                    <img class="img-fluid rounded-circle mb-3 shadow-sm" src="{{ asset('storage/' . $user->user_pic) }}" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img class="img-fluid rounded-circle mb-3 shadow-sm" src="https://placehold.co/150x150/E8E8E8/424242?text={{ substr($user->name, 0, 1) }}" alt="Profile Picture">
                @endif
                <h4 class="fw-bold">{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->role }}</p>
            </div>
        </div>
    </div>

    <!-- Information Card -->
    <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i> User Information</h5>
                <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 30%;">Full Name</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email Address</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Phone Number</th>
                        <td>{{ $user->phone ?? 'Not Provided' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><span class="badge rounded-pill bg-gradient-primary">{{ $user->role }}</span></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($user->status == 1)
                                <span class="badge rounded-pill bg-gradient-success">Active</span>
                            @elseif($user->status == 2)
                                <span class="badge rounded-pill bg-gradient-warning">Pending</span>
                            @else
                                <span class="badge rounded-pill bg-gradient-danger">Suspended</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Member Since</th>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
