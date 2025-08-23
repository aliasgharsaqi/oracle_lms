@extends('layouts.admin')

@section('title', 'Assign Lecture')
@section('page-title', 'Assign New Lecture')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header"><h5 class="card-title mb-0">Schedule Details</h5></div>
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
                <form action="{{ route('schedules.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label">Select Teacher (Staff)</label>
                            <select class="form-select" name="teacher_id" required>
                                <option disabled {{ old('teacher_id') ? '' : 'selected' }}>Choose...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" 
                                        {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="class_id" class="form-label">Select Class</label>
                            <select class="form-select" name="class_id" required>
                                <option disabled {{ old('class_id') ? '' : 'selected' }}>Choose...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" 
                                        {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="subject_id" class="form-label">Select Subject</label>
                            <select class="form-select" name="subject_id" required>
                                <option disabled {{ old('subject_id') ? '' : 'selected' }}>Choose...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                        {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="day_of_week" class="form-label">Day of the Week</label>
                            <select class="form-select" name="day_of_week" required>
                                <option disabled {{ old('day_of_week') ? '' : 'selected' }}>Choose...</option>
                                @foreach($days as $day)
                                    <option value="{{ $day }}" 
                                        {{ old('day_of_week') == $day ? 'selected' : '' }}>
                                        {{ $day }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" name="start_time" 
                                   value="{{ old('start_time') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" name="end_time" 
                                   value="{{ old('end_time') }}" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Assign Lecture</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
