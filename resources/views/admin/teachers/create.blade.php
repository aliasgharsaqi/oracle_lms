@extends('layouts.admin')

@section('title', 'Add New Teacher')
@section('page-title', 'New Teacher Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white py-3 rounded-top-4">
                <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i> Add New Teacher</h4>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>- {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2"><i class="bi bi-person-badge-fill me-2"></i> Personal Information</h5>
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control shadow-sm" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email Address (for login)</label>
                            <input type="email" class="form-control shadow-sm" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control shadow-sm" id="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Contact Phone Number</label>
                            <input type="text" class="form-control shadow-sm" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="profile_image" class="form-label fw-semibold">Profile Image</label>
                            <input type="file" class="form-control shadow-sm" id="profile_image" name="profile_image" required>
                        </div>
                        <div class="col-md-6">
                            <label for="id_card_number" class="form-label fw-semibold">ID Card Number</label>
                            <input type="text" class="form-control shadow-sm" id="id_card_number" name="id_card_number" value="{{ old('id_card_number') }}" required>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">Full Address</label>
                            <textarea class="form-control shadow-sm" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                        </div>

                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mt-4"><i class="bi bi-mortarboard-fill me-2"></i> Academic & Professional Information</h5>
                        </div>

                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" class="form-control shadow-sm" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="education" class="form-label fw-semibold">Highest Education</label>
                            <input type="text" class="form-control shadow-sm" id="education" name="education" value="{{ old('education') }}" required placeholder="e.g., BS Computer Science">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary me-2 px-4">Cancel</a>
                        <button type="submit" class="btn btn-gradient-primary px-4"><i class="bi bi-save2-fill me-1"></i> Add Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
