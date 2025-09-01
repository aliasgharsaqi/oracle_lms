@extends('layouts.admin')

@section('title', 'Add New Class')
@section('page-title', 'Create Class')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-journal-plus me-2"></i> New Class Details
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

                {{-- Class Form --}}
                <form action="{{ route('classes.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Class Name</label>
                        <input type="text" class="form-control rounded-3 shadow-sm" 
                               id="name" name="name" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., Grade 10 - Section A" required>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('classes.index') }}" 
                           class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" 
                                class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Create Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
