@extends('layouts.admin')

@section('title', 'Edit Subject')
@section('page-title', 'Edit Subject')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-journal-text me-2"></i> Subject Details
                </h5>
                <a href="{{ route('subjects.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to Subjects
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

                {{-- Edit Subject Form --}}
                <form action="{{ route('subjects.update', $subject->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Subject Name</label>
                        <input type="text" class="form-control rounded-3 shadow-sm" id="name" name="name"
                            value="{{ old('name', $subject->name) }}" placeholder="Enter subject name" required>
                    </div>

                    <div class="mb-3">
                        <label for="subject_code" class="form-label fw-semibold">Subject Code</label>
                        <input type="text" class="form-control rounded-3 shadow-sm" id="subject_code" name="subject_code"
                            value="{{ old('subject_code', $subject->subject_code) }}" placeholder="Enter subject code" required>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Update Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
