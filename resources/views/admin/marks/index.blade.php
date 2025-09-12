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
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden mb-6">
        <!-- Card Header -->
        <div class="px-4 py-3 border-b bg-gradient-to-r from-blue-500 to-blue-600">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-funnel-fill"></i> Select Class & Subject
            </h4>
        </div>

        <!-- Card Body -->
        <div class="p-4">
            <form>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Semester Dropdown -->
                    <div>
                        <label for="semester" class="block text-sm font-semibold mb-1">Semester</label>
                        <select id="semester" name="semester"
                            class="w-full rounded-lg border border-blue-500 shadow-sm px-3 py-2 focus:ring focus:ring-blue-300">
                            <option value="">Select Semester</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                            <option value="3">Semester 3</option>
                            <option value="4">Semester 4</option>
                        </select>
                    </div>

                    <!-- Class Dropdown -->
                    <div>
                        <label for="class_id" class="block text-sm font-semibold mb-1">Class</label>
                        <select id="class_id" name="class_id" required
                            class="w-full rounded-lg border border-blue-500 shadow-sm px-3 py-2 focus:ring focus:ring-blue-300">
                            <option value="">Select a class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject Dropdown -->
                    <div>
                        <label for="subject_id" class="block text-sm font-semibold mb-1">Subject</label>
                        <select id="subject_id" name="subject_id" required
                            class="w-full rounded-lg border border-blue-500 shadow-sm px-3 py-2 focus:ring focus:ring-blue-300">
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
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full btn btn-gradient-primary font-bold py-2 px-4 rounded-lg shadow">
                            <i class="bi bi-search me-1"></i> Load Students
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($students) && $selectedClass && $selectedSubject)
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div class="px-4 py-3 border-b bg-gradient-to-r from-gray-600 to-gray-800">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-people-fill"></i> Marks for {{ $selectedSubject->name }} - {{ $selectedClass->name }}
            </h4>
        </div>

        <!-- Card Body -->
        <div class="p-0">
            @if($students->isEmpty())
            <div class="p-6 text-center text-blue-600">
                <i class="bi bi-info-circle me-2"></i> No students enrolled in this class.
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-center">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3"><i class="bi bi-person"></i> Student</th>
                            <th class="px-4 py-3"><i class="bi bi-journal-bookmark"></i> Subject</th>
                            <th class="px-4 py-3"><i class="bi bi-123"></i> Total Marks</th>
                            <th class="px-4 py-3"><i class="bi bi-pencil"></i> Obtained Marks</th>
                            <th class="px-4 py-3"><i class="bi bi-gear"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <form>
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-blue-600">{{ $student->name }}</td>
                                <td class="px-4 py-3">{{ $selectedSubject->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge badge-gradient-info text-black px-3 py-1">400</span>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="obtained_marks"
                                        class="w-full rounded-lg border border-green-500 text-center font-semibold shadow-sm px-2 py-1"
                                        value="{{ $student->pivot->obtained_marks ?? '' }}" required>
                                </td>
                                <td class="px-4 py-3">
                                    <button type="submit"
                                        class="btn btn-sm btn-gradient-success rounded-lg px-3 py-1">
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
