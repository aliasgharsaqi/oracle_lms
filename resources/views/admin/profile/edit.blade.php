@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'Profile Settings')

@section('content')

@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        @switch(session('status'))
            @case('profile-info-updated')
                Profile information has been updated successfully.
                @break
            @case('social-links-updated')
                Social links have been updated successfully.
                @break
            @case('password-updated')
                Password has been changed successfully.
                @break
            @case('image-updated')
                Profile image has been updated successfully.
                @break
        @endswitch
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Error!</strong> Please check the form for errors and try again.
        <ul class="mt-2 mb-0 d-none"> {{-- Hidden for cleaner UI, but useful for debugging --}}
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


<div class="row g-4">
    <div class="col-xl-4">
        <div class="card shadow-lg border-0 rounded-4 h-100">
            <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileImageForm">
                    @csrf
                    @method('patch')
                    <input type="hidden" name="section" value="image">
                    
                    <img id="profileImagePreview" class="img-fluid rounded-circle mb-3 shadow-lg mx-auto" 
                        src="{{ $user->user_pic ? asset('storage/' . $user->user_pic) : 'https://placehold.co/150x150/E8E8E8/424242?text=' . substr($user->name, 0, 1) }}" 
                        alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white;">

                    <h4 class="card-title fw-bold mt-2">{{ $user->name }}</h4>
                    <p class="card-text text-muted mb-3">{{ $user->getRoleNames()->first() ?? 'User' }}</p>
                    
                    <div class="mb-3">
                        <label for="user_pic_upload" class="btn btn-outline-primary rounded-pill">
                            <i class="bi bi-camera-fill me-1"></i> Change Image
                        </label>
                        <input class="form-control d-none" type="file" id="user_pic_upload" name="user_pic" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 d-none" id="uploadImageBtn">Upload New Image</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card shadow-lg border-0 rounded-4 h-100">
            <div class="card-header bg-light border-0 p-2">
                <ul class="nav nav-pills nav-fill" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab" aria-controls="profile-pane" aria-selected="true">
                            <i class="bi bi-person-fill me-1"></i> Edit Profile
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social-pane" type="button" role="tab" aria-controls="social-pane" aria-selected="false">
                            <i class="bi bi-link-45deg me-1"></i> Social Links
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-pane" type="button" role="tab" aria-controls="password-pane" aria-selected="false">
                            <i class="bi bi-shield-lock-fill me-1"></i> Change Password
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="profile-pane" role="tabpanel" aria-labelledby="profile-tab">
                        <h5 class="mb-4 fw-bold">Account Information</h5>
                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="section" value="info">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 mt-3">Save Changes</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="social-pane" role="tabpanel" aria-labelledby="social-tab">
                        <h5 class="mb-4 fw-bold">Social Profiles</h5>
                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="section" value="social">
                            <div class="input-group input-group-lg mb-3">
                                <span class="input-group-text"><i class="bi bi-twitter"></i></span>
                                <input type="url" class="form-control @error('twitter_profile') is-invalid @enderror" placeholder="https://twitter.com/username" name="twitter_profile" value="{{ old('twitter_profile', $user->twitter_profile) }}">
                                @error('twitter_profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="input-group input-group-lg mb-3">
                                <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                                <input type="url" class="form-control @error('facebook_profile') is-invalid @enderror" placeholder="https://facebook.com/username" name="facebook_profile" value="{{ old('facebook_profile', $user->facebook_profile) }}">
                                @error('facebook_profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="input-group input-group-lg mb-3">
                                <span class="input-group-text"><i class="bi bi-linkedin"></i></span>
                                <input type="url" class="form-control @error('linkedin_profile') is-invalid @enderror" placeholder="https://linkedin.com/in/username" name="linkedin_profile" value="{{ old('linkedin_profile', $user->linkedin_profile) }}">
                                @error('linkedin_profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 mt-3">Save Social Links</button>
                        </form>
                    </div>
                    
                    <div class="tab-pane fade" id="password-pane" role="tabpanel" aria-labelledby="password-tab">
                        <h5 class="mb-4 fw-bold">Update Password</h5>
                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="section" value="password">

                            {{-- Current Password Field --}}
                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                    <span class="input-group-text p-0 border-start-0 bg-transparent">
                                        <i class="bi bi-eye-slash password-toggle p-3" data-target="current_password"></i>
                                    </span>
                                    @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- New Password Field --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required>
                                    <span class="input-group-text p-0 border-start-0 bg-transparent">
                                        <i class="bi bi-eye-slash password-toggle p-3" data-target="password"></i>
                                    </span>
                                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            {{-- Confirm New Password Field --}}
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required>
                                    <span class="input-group-text p-0 border-start-0 bg-transparent">
                                        <i class="bi bi-eye-slash password-toggle p-3" data-target="password_confirmation"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill px-4 mt-3">Update Password</button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Custom style for the password toggle icon */
    .password-toggle {
        cursor: pointer;
        color: #6c757d; /* Muted color for the icon */
        transition: color 0.15s ease-in-out;
    }
    .password-toggle:hover {
        color: #0d6efd; /* Highlight on hover */
    }
    /* Ensure the input-group-text doesn't add extra borders/backgrounds */
    .input-group-text {
        border-color: #dee2e6;
        border-radius: 0 0.5rem 0.5rem 0; /* Match Bootstrap rounded-4 */
    }
    .input-group:focus-within .input-group-text {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    /* Fix for invalid-feedback positioning in input-group (must be display block to work) */
    .input-group > .invalid-feedback.d-block {
        position: absolute;
        width: 100%;
        margin-top: calc(0.5rem + 0.9rem); /* Adjust based on form-control-lg height */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileImageUpload = document.getElementById('user_pic_upload');
        const profileImagePreview = document.getElementById('profileImagePreview');
        const uploadImageBtn = document.getElementById('uploadImageBtn');

        // Logic for image preview and showing upload button
        profileImageUpload.addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                profileImagePreview.src = URL.createObjectURL(file);
                uploadImageBtn.classList.remove('d-none'); // Show the upload button
            }
        });

        // --- PASSWORD TOGGLE FUNCTIONALITY START ---
        const passwordToggles = document.querySelectorAll('.password-toggle');

        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                // Get the ID of the input field from the data-target attribute
                const inputId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(inputId);

                // Toggle the input type between 'password' and 'text'
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    // Change icon from 'slash' (hidden) to 'open' (visible)
                    this.classList.remove('bi-eye-slash');
                    this.classList.add('bi-eye');
                } else {
                    passwordInput.type = 'password';
                    // Change icon from 'open' (visible) to 'slash' (hidden)
                    this.classList.remove('bi-eye');
                    this.classList.add('bi-eye-slash');
                }
            });
        });
        // --- PASSWORD TOGGLE FUNCTIONALITY END ---


        // If there are validation errors, switch to the correct tab
        @if($errors->any())
            let activeTabId = 'profile-tab'; // Default to profile tab
            @if($errors->has('twitter_profile') || $errors->has('facebook_profile') || $errors->has('linkedin_profile'))
                activeTabId = 'social-tab';
            @elseif ($errors->has('current_password') || $errors->has('password') || $errors->has('user_pic'))
                activeTabId = 'password-tab';
            @endif
            
            const triggerEl = document.querySelector('#' + activeTabId);
            if (triggerEl) {
                // Manually handle Bootstrap tab switching (necessary when done via JS after errors)
                
                // Remove active class from all nav-links and add to the target
                document.querySelectorAll('#myTab .nav-link').forEach(nav => nav.classList.remove('active'));
                triggerEl.classList.add('active');

                // Hide all panes and show the target pane
                document.querySelectorAll('#myTabContent .tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                    pane.setAttribute('aria-selected', 'false');
                });
                
                const targetPaneId = triggerEl.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetPaneId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                    triggerEl.setAttribute('aria-selected', 'true');
                }
            }
        @endif
    });
</script>
@endpush