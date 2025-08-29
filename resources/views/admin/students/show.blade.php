@extends('layouts.admin')

@section('title', 'Student Details')
@section('page-title', 'Student Profile: ' . $student->user->name)

@section('content')
<div class="row">
    {{-- Student Profile Card --}}
    <div class="col-lg-4">
        <div class="card shadow-lg rounded-4 mb-4 text-center">
            <div class="card-body">
                @if($student->user->user_pic)
                    <img src="{{ asset('storage/' . $student->user->user_pic) }}" 
                         alt="{{ $student->user->name }}" 
                         class="rounded-circle img-fluid mb-3 shadow-sm" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mb-3" 
                         style="width: 150px; height: 150px; font-size: 50px;">
                        {{ substr($student->user->name, 0, 1) }}
                    </div>
                @endif

                <h4 class="fw-bold">{{ $student->user->name }}</h4>
                <p class="text-muted mb-0">
                    <i class="bi bi-building me-1"></i> {{ $student->schoolClass->name }} 
                    - Section {{ $student->section }}
                </p>
            </div>
        </div>
    </div>

    {{-- Student Information Card --}}
    <div class="col-lg-8">
        <div class="card shadow-lg rounded-4 mb-4">
            <div class="card-body ">
                {{-- Student Information --}}
                <h5 class="fw-bold border-bottom  pb-2 mb-3">
                    <i class="bi bi-person-badge me-1"></i> Student Information
                </h5>
                <div class="row g-3">
                    <div class="col-sm-6"><strong>Email:</strong> {{ $student->user->email }}</div>
                    <div class="col-sm-6"><strong>Phone:</strong> {{ $student->user->phone }}</div>
                    <div class="col-sm-6"><strong>ID Card #:</strong> {{ $student->id_card_number }}</div>
                    <div class="col-sm-12"><strong>Address:</strong> {{ $student->address }}</div>
                </div>

                {{-- Guardian Information --}}
                <h5 class="fw-bold border-bottom pb-2 mt-4 mb-3">
                    <i class="bi bi-people me-1"></i> Guardian Information
                </h5>
                <div class="row g-3">
                    <div class="col-sm-6"><strong>Father's Name:</strong> {{ $student->father_name }}</div>
                    <div class="col-sm-6"><strong>Father's Phone:</strong> {{ $student->father_phone }}</div>
                </div>

                {{-- Previous Documents --}}
                @if($student->previous_school_docs)
                <h5 class="fw-bold border-bottom pb-2 mt-4 mb-3">
                    <i class="bi bi-file-earmark-text me-1"></i> Documents
                </h5>
                <div class="row g-3">
                    <div class="col-sm-12">
                        <a href="{{ asset('storage/' . $student->previous_school_docs) }}" 
                           target="_blank" 
                           class="btn btn-outline-primary rounded-pill shadow-sm">
                            <i class="bi bi-eye me-1"></i> View Previous Documents
                        </a>
                    </div>
                </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-4 d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('students.index') }}" 
                       class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-arrow-left-circle me-1"></i> Back to List
                    </a>
                    <a href="{{ route('students.edit', $student->id) }}" 
                       class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Edit Record
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
