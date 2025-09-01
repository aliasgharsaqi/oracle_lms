@extends('layouts.admin')

@section('title', 'Schedule Details')
@section('page-title', 'Lecture Schedule Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-calendar-event me-2"></i> Lecture Information
                </h5>
                <a href="{{ route('schedules.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to Schedule
                </a>
            </div>
            <div class="card-body p-4">
                <dl class="row mb-4">
                    <dt class="col-sm-4 fw-semibold">Teacher</dt>
                    <dd class="col-sm-8">{{ $schedule->teacher->name }}</dd>

                    <dt class="col-sm-4 fw-semibold">Class</dt>
                    <dd class="col-sm-8">{{ $schedule->schoolClass->name }}</dd>

                    <dt class="col-sm-4 fw-semibold">Subject</dt>
                    <dd class="col-sm-8">{{ $schedule->subject->name }}</dd>

                    <dt class="col-sm-4 fw-semibold">Day</dt>
                    <dd class="col-sm-8">{{ $schedule->day_of_week }}</dd>

                    <dt class="col-sm-4 fw-semibold">Start Time</dt>
                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</dd>

                    <dt class="col-sm-4 fw-semibold">End Time</dt>
                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</dd>
                </dl>

                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-arrow-left-circle me-1"></i> Back
                    </a>
                    <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn badge-gradient-warning text-white rounded-pill px-4 shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn badge-gradient-danger text-white rounded-pill px-4 shadow-sm"
                                onclick="return confirm('Are you sure you want to delete this schedule?')">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
