@extends('layouts.admin')

@section('title', 'Edit Student Record')
@section('page-title', 'Edit Admission: ' . $student->user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-lg rounded-4">
            <div class="card-body">
                {{-- Error Display --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle-fill me-1"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    {{-- Personal Information --}}
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-person-circle me-1"></i> Student's Personal Information
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control rounded-3" id="name" name="name" 
                                   value="{{ old('name', $student->user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control rounded-3" id="email" name="email" 
                                   value="{{ old('email', $student->user->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Student Phone</label>
                            <input type="text" class="form-control rounded-3" id="phone" name="phone" 
                                   value="{{ old('phone', $student->user->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="id_card_number" class="form-label">ID Card Number</label>
                            <input type="text" class="form-control rounded-3" id="id_card_number" name="id_card_number" 
                                   value="{{ old('id_card_number', $student->id_card_number) }}" required>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Full Address</label>
                            <textarea class="form-control rounded-3" id="address" name="address" rows="3" required>{{ old('address', $student->address) }}</textarea>
                        </div>
                    </div>

                    {{-- Guardian Information --}}
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-people-fill me-1"></i> Guardian's Information
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control rounded-3" id="father_name" name="father_name" 
                                   value="{{ old('father_name', $student->father_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="father_phone" class="form-label">Father's Phone</label>
                            <input type="text" class="form-control rounded-3" id="father_phone" name="father_phone" 
                                   value="{{ old('father_phone', $student->father_phone) }}" required>
                        </div>
                    </div>

                    {{-- Academic Information --}}
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-journal-bookmark-fill me-1"></i> Academic Information
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="school_class_id" class="form-label">Assign Class</label>
                            <select class="form-select rounded-3" id="school_class_id" name="school_class_id" required>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('school_class_id', $student->school_class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="section" class="form-label">Section</label>
                            <input type="text" class="form-control rounded-3" id="section" name="section" 
                                   value="{{ old('section', $student->section) }}">
                        </div>
                    </div>

                    {{-- Documents --}}
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-file-earmark-arrow-up-fill me-1"></i> Documents
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="profile_image" class="form-label">New Profile Image (Optional)</label>
                            <input type="file" class="form-control rounded-3" id="profile_image" name="profile_image">
                        </div>
                        <div class="col-md-6">
                            <label for="previous_docs" class="form-label">New Previous Docs (Optional)</label>
                            <input type="file" class="form-control rounded-3" id="previous_docs" name="previous_docs">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-arrow-left-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Record
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
