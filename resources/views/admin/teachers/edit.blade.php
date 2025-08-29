@extends('layouts.admin')

@section('title', 'Edit Teacher')
@section('page-title', 'Update Teacher Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-person-badge-fill me-2"></i> Teacher Details
                </h5>
                <a href="{{ route('teachers.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to Teachers
                </a>
            </div>
            <div class="card-body p-4">
                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Edit Teacher Form --}}
                <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Personal Information --}}
                    <div class="mb-4">
                        <h5 class="mb-3 border-bottom pb-2 fw-semibold">Teacher's Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control rounded-3 shadow-sm" name="name" value="{{ old('name', $teacher->user->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control rounded-3 shadow-sm" name="email" value="{{ old('email', $teacher->user->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" class="form-control rounded-3 shadow-sm" name="phone" value="{{ old('phone', $teacher->user->phone) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Profile Image <small class="text-muted">(Leave empty to keep old)</small></label>
                                <input type="file" class="form-control rounded-3 shadow-sm" name="profile_image">
                                @if($teacher->user->user_pic)
                                    <img src="{{ asset('storage/' . $teacher->user->user_pic) }}" class="mt-2 rounded-circle shadow-sm" width="80">
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">ID Card Number</label>
                                <input type="text" class="form-control rounded-3 shadow-sm" name="id_card_number" value="{{ old('id_card_number', $teacher->id_card_number) }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea class="form-control rounded-3 shadow-sm" name="address" rows="3" required>{{ old('address', $teacher->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Academic Information --}}
                    <div class="mb-4">
                        <h5 class="mb-3 border-bottom pb-2 fw-semibold">Academic Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Date of Birth</label>
                                <input type="date" class="form-control rounded-3 shadow-sm" name="date_of_birth" value="{{ old('date_of_birth', $teacher->date_of_birth) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Highest Education</label>
                                <input type="text" class="form-control rounded-3 shadow-sm" name="education" value="{{ old('education', $teacher->education) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Update Teacher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
