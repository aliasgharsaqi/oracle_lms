@extends('layouts.admin')

@section('title', 'Teacher Timetable')
@section('page-title', "Weekly Timetable")

@section('content')
<div class="container-fluid">
    <div class="card shadow-lg border-0 rounded-4">
        {{-- Card Header: Displays Teacher's Name and a Back Button --}}
        <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex flex-column flex-md-row align-items-center justify-content-between p-3">
            <div class="text-center text-md-start">
                <h4 class="card-title mb-0 fw-bold">
                    <i class="bi bi-person-workspace me-2"></i> {{ $teacher->user->name ?? 'N/A' }}
                </h4>
                <p class="mb-0 small opacity-75">{{ $teacher->user->school->name ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('schedules.index') }}" class="btn btn-light btn-sm rounded-pill mt-2 mt-md-0">
                <i class="bi bi-arrow-left"></i> Back to Schedule List
            </a>
        </div>

        {{-- Card Body: Contains the grid of daily schedules --}}
        <div class="card-body p-4">
            <div class="row g-4">
                @php
                    // Array of colors to cycle through for each day's header
                    $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-danger', 'bg-warning', 'bg-dark', 'bg-secondary'];
                @endphp

                {{-- Loop through the days in the correct order (Monday -> Sunday) --}}
                @foreach($daysOrder as $index => $day)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-light rounded-3">
                            {{-- Day Header with a unique color --}}
                            <div class="card-header {{ $colors[$index % count($colors)] }} text-white fw-bold fs-6 rounded-top-3">
                                {{ $day }}
                            </div>
                            <div class="card-body p-2">
                                {{-- Check if there are any lectures scheduled for this day --}}
                                @if(isset($weeklySchedules[$day]) && $weeklySchedules[$day]->count() > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach($weeklySchedules[$day] as $lecture)
                                            <li class="list-group-item px-2 py-3 border-bottom">
                                                <div class="d-flex w-100 justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold text-primary-custom">{{ $lecture->subject->name ?? 'N/A' }}</h6>
                                                        <p class="mb-1 text-muted small">{{ $lecture->schoolClass->name ?? 'N/A' }}</p>
                                                    </div>
                                                    <small class="text-muted text-nowrap">{{ \Carbon\Carbon::parse($lecture->start_time)->format('h:i A') }}</small>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{-- Message for days with no lectures --}}
                                    <div class="text-center text-muted p-4 d-flex align-items-center justify-content-center h-100">
                                        <span class="opacity-75">No lectures scheduled.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

