@extends('layouts.admin')

@section('title', 'Edit Teacher')
@section('page-title', 'Update Teacher Details')

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
                <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h5 class="mb-3 border-bottom pb-2">Teacher's Personal Information</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $teacher->user->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $teacher->user->email) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $teacher->user->phone) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Profile Image (Leave empty to keep old)</label>
                            <input type="file" class="form-control" name="profile_image">
                            @if($teacher->user->user_pic)
                                <img src="{{ asset('storage/' . $teacher->user->user_pic) }}" class="mt-2" width="80">
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Card Number</label>
                            <input type="text" class="form-control" name="id_card_number" value="{{ old('id_card_number', $teacher->id_card_number) }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3" required>{{ old('address', $teacher->address) }}</textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <h5 class="mb-3 border-bottom pb-2">Academic Information</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', $teacher->date_of_birth) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Highest Education</label>
                            <input type="text" class="form-control" name="education" value="{{ old('education', $teacher->education) }}" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('teachers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
