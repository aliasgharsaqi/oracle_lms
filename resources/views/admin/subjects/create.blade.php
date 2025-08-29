@extends('layouts.admin')

@section('title', 'Add New Subject')
@section('page-title', 'Create Subject')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-book-half me-2"></i> New Subject Details
                </h5>
            </div>
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

                {{-- Subject Form --}}
                <form action="{{ route('subjects.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Subject Name</label>
                        <input type="text" 
                               class="form-control rounded-3 shadow-sm" 
                               id="name" name="name" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., Mathematics, Physics" required>
                    </div>

                    <div class="mb-3">
                        <label for="subject_code" class="form-label fw-semibold">Subject Code</label>
                        <input type="text" 
                               class="form-control rounded-3 shadow-sm" 
                               id="subject_code" name="subject_code" 
                               value="{{ old('subject_code') }}" 
                               placeholder="e.g., MATH101, PHY201" required>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('subjects.index') }}" 
                           class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" 
                                class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Create Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
