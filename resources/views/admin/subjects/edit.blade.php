@extends('layouts.admin')

@section('title', 'Edit Subject')
@section('page-title', 'Edit Subject')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Subject</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $subject->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject_code" class="form-label">Subject Code</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code"
                            value="{{ old('subject_code', $subject->subject_code) }}" required>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('subjects.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
