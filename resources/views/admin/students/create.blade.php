@extends('layouts.admin')

@section('title', 'Enroll New Student')
@section('page-title', 'New Student Admission Form')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3 border-bottom pb-2">Student's Personal Information</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address (for login)</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Student Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_card_number" class="form-label">ID Card Number</label>
                            <input type="text" class="form-control" id="id_card_number" name="id_card_number" value="{{ old('id_card_number') }}" required>
                        </div>
                         <div class="col-12 mb-3">
                            <label for="address" class="form-label">Full Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <h5 class="mb-3 border-bottom pb-2">Guardian's Information</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" value="{{ old('father_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="father_phone" class="form-label">Father's Phone Number</label>
                            <input type="text" class="form-control" id="father_phone" name="father_phone" value="{{ old('father_phone') }}" required>
                        </div>

                        <div class="col-12 mt-4">
                            <h5 class="mb-3 border-bottom pb-2">Academic Information</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="school_class_id" class="form-label">Assign Class</label>
                            <select class="form-select" id="school_class_id" name="school_class_id" required>
                                <option value="" selected disabled>Select a class...</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="section" class="form-label">Section</label>
                            <input type="text" class="form-control" id="section" name="section" value="{{ old('section') }}"  placeholder="e.g., A, B, Blue, etc.">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="previous_docs" class="form-label">Previous Study Documents (Optional)</label>
                            <input type="file" class="form-control" id="previous_docs" name="previous_docs">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Enroll Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
