@extends('layouts.admin')

@section('title', 'Marks Collection')
@section('page-title', 'Enter Student Marks')

@section('content')
@php
    // --- Static data ---
    $classes = [
        (object)['id' => 1, 'name' => 'Grade 10'],
        (object)['id' => 2, 'name' => 'Grade 11'],
        (object)['id' => 3, 'name' => 'Grade 12'],
    ];

    $allSubjects = [
        1 => [(object)['id' => 101, 'name' => 'Mathematics'], (object)['id' => 102, 'name' => 'Physics']],
        2 => [(object)['id' => 201, 'name' => 'Chemistry'], (object)['id' => 202, 'name' => 'Biology']],
        3 => [(object)['id' => 301, 'name' => 'Computer Science'], (object)['id' => 302, 'name' => 'Economics']],
    ];

    $studentsData = [
        (object)['id' => 1, 'name' => 'John Doe', 'pivot' => (object)['total_marks' => 100, 'obtained_marks' => 85]],
        (object)['id' => 2, 'name' => 'Jane Smith', 'pivot' => (object)['total_marks' => 100, 'obtained_marks' => 92]],
        (object)['id' => 3, 'name' => 'Peter Jones', 'pivot' => (object)['total_marks' => 100, 'obtained_marks' => 78]],
    ];

    $selectedClassId   = request()->input('class_id');
    $selectedSubjectId = request()->input('subject_id');

    $selectedClass   = null;
    $subjects        = [];
    $selectedSubject = null;
    $students        = [];

    if ($selectedClassId) {
        $selectedClass = collect($classes)->firstWhere('id', $selectedClassId);
        $subjects      = $allSubjects[$selectedClassId] ?? [];
        if ($selectedSubjectId) {
            $selectedSubject = collect($subjects)->firstWhere('id', $selectedSubjectId);
            $students = collect($studentsData);
        }
    }
@endphp

<!-- Selection Card -->
<div class="card shadow-lg border-0 rounded-4 mb-5">
    <div class="card-header bg-gradient bg-primary text-white rounded-top-4">
        <h5 class="mb-0 fw-bold"><i class="bi bi-funnel-fill me-2"></i> Select Class & Subject</h5>
    </div>
    <div class="card-body p-4">
        <form>
            <div class="row g-4 align-items-end">

                <!-- Semester Dropdown -->
                <div class="col-md-3">
                    <label for="semester" class="form-label fw-semibold">Semester</label>
                    <select class="form-select shadow-sm border-primary" id="semester" name="semester">
                        <option value="">Select Semester</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                        <option value="3">Semester 3</option>
                        <option value="4">Semester 4</option>
                    </select>
                </div>

                <!-- Class Dropdown -->
                <div class="col-md-3">
                    <label for="class_id" class="form-label fw-semibold">Class</label>
                    <select class="form-select shadow-sm border-primary" id="class_id" name="class_id" required>
                        <option value="">Select a class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject Dropdown -->
                <div class="col-md-3">
                    <label for="subject_id" class="form-label fw-semibold">Subject</label>
                    <select class="form-select shadow-sm border-primary" id="subject_id" name="subject_id" required>
                        <option value="">Select a subject</option>
                        @if($subjects)
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $selectedSubject && $selectedSubject->id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Button -->
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm fw-bold rounded-pill">
                        <i class="bi bi-search me-1"></i> Load Students
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
@if(isset($students) && $selectedClass && $selectedSubject)
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-gradient bg-secondary text-white rounded-top-4">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-people-fill me-2"></i> Marks for {{ $selectedSubject->name }} - {{ $selectedClass->name }}
            </h5>
        </div>
        <div class="card-body p-4">
            @if($students->isEmpty())
                <div class="alert alert-info text-center shadow-sm">
                    <i class="bi bi-info-circle me-2"></i> No students enrolled in this class.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden">
                        <thead class="bg-dark text-white text-center">
                            <tr>
                                <th><i class="bi bi-person"></i> Student</th>
                                <th><i class="bi bi-journal-bookmark"></i> Subject</th>
                                <th><i class="bi bi-123"></i> Total Marks</th>
                                <th><i class="bi bi-pencil"></i> Obtained Marks</th>
                                <th><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <form>
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                                    <tr class="text-center">
                                        <td class="fw-bold text-primary">{{ $student->name }}</td>
                                        <td>{{ $selectedSubject->name }}</td>
                                        <td><span class="badge bg-gradient bg-info text-dark px-3 py-2">400</span></td>
                                        <td class="w-25">
                                            <input type="number" name="obtained_marks"
                                                class="form-control text-center fw-semibold shadow-sm border-success"
                                                value="{{ $student->pivot->obtained_marks ?? '' }}" required>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-sm btn-success shadow-sm rounded-pill px-3">
                                                <i class="bi bi-check-circle me-1"></i> Save
                                            </button>
                                        </td>
                                    </tr>
                                </form>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const classSelect   = document.getElementById('class_id');
        const subjectSelect = document.getElementById('subject_id');

        const staticSubjects = {
            1: [{ id: 101, name: 'Mathematics' }, { id: 102, name: 'Physics' }],
            2: [{ id: 201, name: 'Chemistry' }, { id: 202, name: 'Biology' }],
            3: [{ id: 301, name: 'Computer Science' }, { id: 302, name: 'Economics' }]
        };

        classSelect.addEventListener('change', function () {
            const classId = this.value;
            subjectSelect.innerHTML = '<option value="">Select a subject</option>';
            if (classId && staticSubjects[classId]) {
                staticSubjects[classId].forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.name;
                    subjectSelect.appendChild(option);
                });
            }
        });
    });
</script>
@endpush
