@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'Profile Settings')

@section('content')

@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        @if(session('status') == 'profile-updated')
            Profile information has been updated successfully.
        @elseif(session('status') == 'password-updated')
            Password has been changed successfully.
        @elseif(session('status') == 'image-updated')
            Profile image has been updated successfully.
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


<div class="row">
    <div class="col-xl-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profile Picture</h6>
            </div>
            <div class="card-body text-center">
                @if($user->user_pic)
                    <img class="img-fluid rounded-circle mb-3" src="{{ asset('storage/' . $user->user_pic) }}" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img class="img-fluid rounded-circle mb-3" src="https://placehold.co/150x150/E8E8E8/424242?text={{ substr($user->name, 0, 1) }}" alt="Profile Picture">
                @endif
                <h5 class="card-title">{{ $user->name }}</h5>
                <p class="card-text text-muted">{{ $user->role }}</p>
                
                <form action="{{ route('profile.image.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="user_pic" class="form-label">Change Image</label>
                        <input class="form-control" type="file" id="user_pic" name="user_pic" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Upload New Image</button>
                    @error('user_pic') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Edit Profile</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active p-3" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <h6 class="mt-4">Social Profiles</h6>
                            <div class="mb-3">
                                <label for="twitter_profile" class="form-label">Twitter URL</label>
                                <input type="url" class="form-control" id="twitter_profile" name="twitter_profile" value="{{ old('twitter_profile', $user->twitter_profile) }}">
                            </div>
                            <div class="mb-3">
                                <label for="facebook_profile" class="form-label">Facebook URL</label>
                                <input type="url" class="form-control" id="facebook_profile" name="facebook_profile" value="{{ old('facebook_profile', $user->facebook_profile) }}">
                            </div>
                            <div class="mb-3">
                                <label for="linkedin_profile" class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control" id="linkedin_profile" name="linkedin_profile" value="{{ old('linkedin_profile', $user->linkedin_profile) }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                    <div class="tab-pane fade p-3" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <form method="post" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('patch')
                             <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
