@extends('layouts.admin')

@section('title', 'Edit Student Record')
@section('page-title', 'Edit Admission: ' . $student->user->name)

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
                <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-12"><h5 class="mb-3 border-bottom pb-2">Student's Personal Information</h5></div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $student->user->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->user->email) }}" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Student Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $student->user->phone) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_card_number" class="form-label">ID Card Number</label>
                            <input type="text" class="form-control" id="id_card_number" name="id_card_number" value="{{ old('id_card_number', $student->id_card_number) }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Full Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $student->address) }}</textarea>
                        </div>

                        <div class="col-12 mt-4"><h5 class="mb-3 border-bottom pb-2">Guardian's Information</h5></div>
                        <div class="col-md-6 mb-3">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" value="{{ old('father_name', $student->father_name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="father_phone" class="form-label">Father's Phone</label>
                            <input type="text" class="form-control" id="father_phone" name="father_phone" value="{{ old('father_phone', $student->father_phone) }}" required>
                        </div>

                        <div class="col-12 mt-4"><h5 class="mb-3 border-bottom pb-2">Academic Information</h5></div>
                        <div class="col-md-6 mb-3">
                            <label for="school_class_id" class="form-label">Assign Class</label>
                            <select class="form-select" id="school_class_id" name="school_class_id" required>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('school_class_id', $student->school_class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="section" class="form-label">Section</label>
                            <input type="text" class="form-control" id="section" name="section" value="{{ old('section', $student->section) }}" >
                        </div>
                        
                        <div class="col-12 mt-4"><h5 class="mb-3 border-bottom pb-2">Documents</h5></div>
                        <div class="col-md-6 mb-3">
                            <label for="profile_image" class="form-label">New Profile Image (Optional)</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="previous_docs" class="form-label">New Previous Docs (Optional)</label>
                            <input type="file" class="form-control" id="previous_docs" name="previous_docs">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
