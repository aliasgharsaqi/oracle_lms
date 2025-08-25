@extends('layouts.admin')

@section('title', 'Schedule Details')
@section('page-title', 'Lecture Schedule Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header"><h5 class="card-title mb-0">Lecture Information</h5></div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Teacher</dt>
                    <dd class="col-sm-8">{{ $schedule->teacher->name }}</dd>

                    <dt class="col-sm-4">Class</dt>
                    <dd class="col-sm-8">{{ $schedule->schoolClass->name }}</dd>

                    <dt class="col-sm-4">Subject</dt>
                    <dd class="col-sm-8">{{ $schedule->subject->name }}</dd>

                    <dt class="col-sm-4">Day</dt>
                    <dd class="col-sm-8">{{ $schedule->day_of_week }}</dd>

                    <dt class="col-sm-4">Start Time</dt>
                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</dd>

                    <dt class="col-sm-4">End Time</dt>
                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</dd>
                </dl>

                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary me-2">Back</a>
                    <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-warning me-2">Edit</a>
                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this schedule?')">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
