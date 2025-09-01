@extends('layouts.admin')

@section('title', 'Edit Lecture')
@section('page-title', 'Edit Lecture Schedule')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-pencil-square me-2"></i> Edit Lecture Schedule
                </h5>
                <a href="{{ route('schedules.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to Schedule
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

                <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Teacher (Staff)</label>
                            <select class="form-select rounded-3 shadow-sm" name="teacher_id" required>
                                <option disabled selected>Choose...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" 
                                        {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Class</label>
                            <select class="form-select rounded-3 shadow-sm" name="class_id" required>
                                <option disabled selected>Choose...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" 
                                        {{ old('class_id', $schedule->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Select Subject</label>
                            <select class="form-select rounded-3 shadow-sm" name="subject_id" required>
                                <option disabled selected>Choose...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                        {{ old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Day of the Week</label>
                            <select class="form-select rounded-3 shadow-sm" name="day_of_week" required>
                                <option disabled selected>Choose...</option>
                                @foreach($days as $day)
                                    <option value="{{ $day }}" 
                                        {{ old('day_of_week', $schedule->day_of_week) == $day ? 'selected' : '' }}>
                                        {{ $day }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Start Time</label>
                            <input type="time" class="form-control rounded-3 shadow-sm" name="start_time" 
                                   value="{{ old('start_time', $schedule->start_time) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">End Time</label>
                            <input type="time" class="form-control rounded-3 shadow-sm" name="end_time" 
                                   value="{{ old('end_time', $schedule->end_time) }}" required>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Update Lecture
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
    