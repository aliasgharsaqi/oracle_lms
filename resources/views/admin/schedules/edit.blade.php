@extends('layouts.admin')

@section('title', 'Edit Schedule')
@section('page-title', 'Edit Lecture Schedule')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pencil-fill me-2"></i> Edit Details</h5>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                <div class="alert alert-danger rounded-3 shadow-sm">
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
                            <label for="teacher_id" class="form-label fw-semibold">Teacher</label>
                            <select class="form-select" id="teacher_id" name="teacher_id" required>
                                @foreach($teachers as $teacher)
                                    {{-- Add a check to ensure user relationship exists --}}
                                    @if($teacher->user)
                                    <option value="{{ $teacher->id }}" {{ $schedule->teacher_id == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="class_id" class="form-label fw-semibold">Class</label>
                            <select class="form-select" id="class_id" name="class_id" required>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $schedule->class_id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="subject_id" class="form-label fw-semibold">Subject</label>
                            <select class="form-select" id="subject_id" name="subject_id" required>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $schedule->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="day_of_week" class="form-label fw-semibold">Day</label>
                            <select class="form-select" name="day_of_week" required>
                                @foreach($days as $day)
                                <option value="{{ $day }}" {{ $schedule->day_of_week == $day ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="start_time" class="form-label fw-semibold">Start Time</label>
                            <input type="time" class="form-control" name="start_time" value="{{ $schedule->start_time }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_time" class="form-label fw-semibold">End Time</label>
                            <input type="time" class="form-control" name="end_time" value="{{ $schedule->end_time }}" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4">Update Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#class_id').on('change', function() {
        var classId = $(this).val();
        var subjectSelect = $('#subject_id');
        subjectSelect.html('<option>Loading...</option>');

        if (classId) {
            $.ajax({
                url: '/admin/get-subjects-by-class/' + classId,
                type: 'GET',
                success: function(data) {
                    subjectSelect.html('<option value="">Choose a subject...</option>');
                    $.each(data, function(key, value) {
                        subjectSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            subjectSelect.html('<option value="">Select a class first</option>');
        }
    });
});
</script>
@endpush

