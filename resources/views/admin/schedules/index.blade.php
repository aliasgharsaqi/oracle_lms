@extends('layouts.admin')

@section('title', 'Weekly Schedule')
@section('page-title', 'Class Schedules')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Assigned Lectures</h5>
                <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus"></i> Assign New Lecture
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules->sortBy(['day_of_week', 'start_time']) as $schedule)
                                <tr>
                                    <td>{{ $schedule->day_of_week }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</td>
                                    <td>{{ $schedule->schoolClass->name }}</td>
                                    <td>{{ $schedule->subject->name }}</td>
                                    <td>{{ $schedule->teacher->user->name }}</td>
                                    <td><a href="#" class="btn btn-sm btn-danger">Delete</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No lectures have been scheduled yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
