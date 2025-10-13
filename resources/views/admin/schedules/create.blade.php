@extends('layouts.admin')

@section('title', 'Assign Lecture')
@section('page-title', 'Assign New Lecture')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-calendar-plus-fill me-2"></i> Schedule Details
                </h5>
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

                <form action="{{ route('schedules.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="teacher_id" class="form-label fw-semibold">Select Teacher</label>
                            <select class="form-select rounded-3 shadow-sm" id="teacher_id" name="teacher_id" required>
                                <option value="" selected disabled>Choose...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="class_id" class="form-label fw-semibold">Select Class</label>
                            <select class="form-select rounded-3 shadow-sm" id="class_id" name="class_id" required>
                                <option value="" selected disabled>Choose...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="subject_id" class="form-label fw-semibold">Select Subject</label>
                            <select class="form-select rounded-3 shadow-sm" id="subject_id" name="subject_id" required>
                                <option value="" selected disabled>Select a class first...</option>
                                {{-- Subjects will be loaded dynamically --}}
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="day_of_week" class="form-label fw-semibold">Days of the Week</label>
                            <select class="form-select rounded-3 shadow-sm" id="day_of_week" name="day_of_week[]" multiple required>
                                @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold down "Ctrl" (or "Command" on Mac) to select multiple days.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="start_time" class="form-label fw-semibold">Start Time</label>
                            <input type="time" class="form-control rounded-3 shadow-sm" name="start_time" value="{{ old('start_time') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="end_time" class="form-label fw-semibold">End Time</label>
                            <input type="time" class="form-control rounded-3 shadow-sm" name="end_time" value="{{ old('end_time') }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Assign Lecture(s)
                        </button>
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
        subjectSelect.html('<option value="" selected disabled>Loading...</option>');

        if (classId) {
            $.ajax({
                url: '/admin/get-subjects-by-class/' + classId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    subjectSelect.html('<option value="" selected disabled>Choose a subject...</option>');
                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            subjectSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    } else {
                        subjectSelect.html('<option value="" selected disabled>No subjects found for this class</option>');
                    }
                },
                error: function() {
                    subjectSelect.html('<option value="" selected disabled>Error loading subjects</option>');
                }
            });
        } else {
            subjectSelect.html('<option value="" selected disabled>Select a class first</option>');
        }
    });
});
</script>
@endpush