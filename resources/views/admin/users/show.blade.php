@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Profile: ' . $user->name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body text-center">
                 @if($user->user_pic)
                    <img class="img-fluid rounded-circle mb-3" src="{{ asset('storage/' . $user->user_pic) }}" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img class="img-fluid rounded-circle mb-3" src="https://placehold.co/150x150/E8E8E8/424242?text={{ substr($user->name, 0, 1) }}" alt="Profile Picture">
                @endif
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->role }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">User Information</h5>
                <table class="table table-borderless">
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
                        <td><span class="badge bg-primary">{{ $user->role }}</span></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($user->status == 1) <span class="badge bg-success">Active</span>
                            @elseif($user->status == 2) <span class="badge bg-warning">Pending</span>
                            @else <span class="badge bg-danger">Suspended</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Member Since</th>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>
                 <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
