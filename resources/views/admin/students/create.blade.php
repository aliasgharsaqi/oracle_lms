@extends('layouts.admin')

@section('title', 'Enroll New Student')
@section('page-title', 'New Student Admission Form')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-lg border-0 rounded-4">
            <!-- Card Header -->
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i> Student Admission Details
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger rounded-3 shadow-sm">{{ session('error') }}</div>
                @endif

                {{-- Student Form --}}
                <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf

                    <!-- Student Personal Info -->
                    <h5 class="mb-3 border-bottom pb-2">Student's Personal Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control rounded-3 shadow-sm" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                       <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address (Optional)</label>
                            {{-- FIX 2: Removed the 'required' attribute --}}
                            <input type="email" class="form-control rounded-3 shadow-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Password (Optional)</label>
                            <div class="input-group">
                                <input type="password" class="form-control rounded-start-3 shadow-sm" id="password" name="password" value="{{ old('password') }}">
                                <button class="btn btn-outline-secondary rounded-end-3 shadow-sm" type="button" id="togglePassword">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Student Phone Number</label>
                            <input type="text" class="form-control rounded-3 shadow-sm" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="profile_image" class="form-label fw-semibold">Profile Image</label>
                            <input type="file" class="form-control rounded-3 shadow-sm" id="profile_image" name="profile_image" required>
                        </div>
                        <div class="col-md-6">
                            <label for="id_card_number" class="form-label fw-semibold">ID Card / B-Form Number</label>
                            <input type="text" class="form-control rounded-3 shadow-sm" id="id_card_number" name="id_card_number" value="{{ old('id_card_number') }}" required>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">Full Address</label>
                            <textarea class="form-control rounded-3 shadow-sm" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- Guardian Info -->
                    <h5 class="mb-3 border-bottom pb-2 mt-4">Guardian's Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="father_name" class="form-label fw-semibold">Father's Name</label>
                            <input type="text" class="form-control rounded-3 shadow-sm" id="father_name" name="father_name" value="{{ old('father_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="father_phone" class="form-label fw-semibold">Father's Phone Number</label>
                            <input type="text" class="form-control rounded-3 shadow-sm" id="father_phone" name="father_phone" value="{{ old('father_phone') }}" required>
                        </div>
                    </div>

                    <!-- Academic Info -->
                    <h5 class="mb-3 border-bottom pb-2 mt-4">Academic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="school_class_id" class="form-label fw-semibold">Assign Class</label>
                            <select class="form-select rounded-3 shadow-sm" id="school_class_id" name="school_class_id" required>
                                <option value="" selected disabled>Select a class...</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="section" class="form-label fw-semibold">Section (Optional)</label>
                            <input type="text" class="form-control rounded-3 shadow-sm" id="section" name="section" value="{{ old('section') }}" placeholder="e.g., A, B, Blue, etc.">
                        </div>
                        <div class="col-md-12">
                            <label for="previous_docs" class="form-label fw-semibold">Previous Study Documents (Optional)</label>
                            <input type="file" class="form-control rounded-3 shadow-sm" id="previous_docs" name="previous_docs">
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Enroll Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        icon.classList.toggle('bi-eye-fill');
        icon.classList.toggle('bi-eye-slash-fill');
    });
</script>
@endpush

