@extends('layouts.admin')

@section('title', 'Student Marks Management')
@section('page-title', 'Enter & Update Student Marks')

@push('styles')
{{-- This style block handles the print layout --}}
<style>
    @media print {
        /* Hide all elements on the page by default */
        body * {
            visibility: hidden;
        }
        /* Only display the printable area and its contents */
        #printable-area, #printable-area * {
            visibility: visible;
        }
        /* Position the printable area to fill the page */
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        /* Ensure elements marked with .no-print are not displayed */
        .no-print {
            display: none !important;
        }
        /* Basic table styling for a clean print output */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h4 {
            font-size: 1.25rem;
            font-weight: bold;
        }
    }
</style>
@endpush

@section('content')

<!-- 1. Filters Card -->
<div class="bg-white shadow-lg rounded-2xl overflow-hidden mb-6 no-print">
    <div class="px-4 py-3 border-b bg-gradient-to-r from-blue-500 to-blue-600">
        <h4 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="bi bi-funnel-fill"></i> Select Criteria to Load Students
        </h4>
    </div>
    <div class="p-4">
        <form id="filter-form" method="GET" action="{{ route('marks.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Semester -->
                <div>
                    <label for="semester_id" class="block text-sm font-semibold mb-1 text-gray-700">Semester</label>
                    <select id="semester_id" name="semester_id" required class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">Select Semester</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ $selectedSemesterId == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Class -->
                <div>
                    <label for="class_id" class="block text-sm font-semibold mb-1 text-gray-700">Class</label>
                    <select id="class_id" name="class_id" required class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">Select a Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedSchoolClassId == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject -->
                <div>
                    <label for="subject_id" class="block text-sm font-semibold mb-1 text-gray-700">Subject</label>
                    <select id="subject_id" name="subject_id" required class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 transition" disabled>
                        <option value="">Select a Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ $selectedSubjectId == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                        <i class="bi bi-search me-1"></i> Load Students
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 2. Marks Entry Table -->
@if($students->isNotEmpty())
<div id="printable-area">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <div class="px-4 py-3 border-b bg-gradient-to-r from-gray-700 to-gray-800 flex justify-between items-center">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Entering Marks for: {{ $subjects->find($selectedSubjectId)->name ?? '' }} - {{ $classes->find($selectedSchoolClassId)->name ?? '' }}
            </h4>
            <div class="flex items-center gap-2 no-print">
                {{-- Export Button Form --}}
                <form method="GET" action="{{ route('marks.export') }}">
                    <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                    <input type="hidden" name="class_id" value="{{ $selectedSchoolClassId }}">
                    <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-bold py-1 px-3 rounded-lg shadow-md text-sm flex items-center gap-1 transition-transform transform hover:scale-105">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                        <span>Export</span>
                    </button>
                </form>
                {{-- Print Button --}}
                <button id="print-btn" type="button" class="text-white bg-sky-600 hover:bg-sky-700 font-bold py-1 px-3 rounded-lg shadow-md text-sm flex items-center gap-1 transition-transform transform hover:scale-105">
                    <i class="bi bi-printer-fill"></i>
                    <span>Print</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm text-center">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold"><i class="bi bi-person-badge"></i> Student Name</th>
                        <th class="px-4 py-3 font-semibold"><i class="bi bi-hash"></i> Roll Number</th>
                        <th class="px-4 py-3 font-semibold"><i class="bi bi-card-heading"></i> Total Marks</th>
                        <th class="px-4 py-3 font-semibold"><i class="bi bi-check2-circle"></i> Obtained Marks</th>
                        <th class="px-4 py-3 font-semibold no-print"><i class="bi bi-gear-fill"></i> Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($students as $student)
                    @php
                        $mark = $student->marks->where('subject_id', $selectedSubjectId)->where('semester_id', $selectedSemesterId)->first();
                    @endphp
                    <tr class="border-b hover:bg-gray-50 transition marks-row">
                        <td class="px-4 py-2 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $student->user->name }}</td>
                        <td class="px-4 py-2">{{ $student->roll_number ?? 'N/A' }}</td>
                        <td class="px-4 py-2">
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <input type="number" name="total_marks" class="w-24 rounded-md border-gray-300 text-center font-semibold shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ $mark->total_marks ?? 100 }}" required>
                        </td>

                        <td class="px-4 py-2">
                            <input type="number" name="obtained_marks" class="w-24 rounded-md border-gray-300 text-center font-semibold shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ $mark->obtained_marks ?? '' }}" required>
                        </td>

                        <td class="px-4 py-2 no-print">
                            <button type="button" class="save-btn text-white bg-green-500 hover:bg-green-600 font-bold py-1 px-3 rounded-md shadow-sm transition-all duration-300 ease-in-out">
                                <i class="bi bi-save"></i> Save
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@elseif(request()->has('class_id'))
<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow-md" role="alert">
    <p class="font-bold"><i class="bi bi-exclamation-triangle-fill"></i> No Students Found</p>
    <p>There are no students enrolled in the selected class. Please check your filter criteria or enroll students.</p>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const printButton = document.getElementById('print-btn');

    // Print functionality
    if (printButton) {
        printButton.addEventListener('click', function () {
            window.print();
        });
    }

    const fetchSubjects = () => {
        const classId = classSelect.value;
        subjectSelect.innerHTML = '<option value="">Loading...</option>';
        subjectSelect.disabled = true;

        if (!classId) {
            subjectSelect.innerHTML = '<option value="">Select Class First</option>';
            return;
        }

        fetch(`{{ route('marks.getSubjects', ['class_id' => 'CLASS_ID']) }}`.replace('CLASS_ID', classId))
            .then(response => response.json())
            .then(data => {
                subjectSelect.innerHTML = '<option value="">Select a Subject</option>';
                data.forEach(subject => {
                    const option = new Option(subject.name, subject.id);
                    if (subject.id == '{{ $selectedSubjectId ?? '' }}') {
                        option.selected = true;
                    }
                    subjectSelect.appendChild(option);
                });
                subjectSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching subjects:', error);
                subjectSelect.innerHTML = '<option value="">Could not load subjects</option>';
            });
    };

    if (classSelect.value) {
        fetchSubjects();
    }
    
    classSelect.addEventListener('change', fetchSubjects);

    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            
            const saveButton = this;
            const originalButtonContent = saveButton.innerHTML;
            const row = saveButton.closest('tr.marks-row');

            saveButton.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Saving...';
            saveButton.disabled = true;

            const formData = new FormData();
            formData.append('student_id', row.querySelector('input[name="student_id"]').value);
            formData.append('total_marks', row.querySelector('input[name="total_marks"]').value);
            formData.append('obtained_marks', row.querySelector('input[name="obtained_marks"]').value);
            formData.append('semester_id', document.getElementById('semester_id').value);
            formData.append('class_id', document.getElementById('class_id').value);
            formData.append('subject_id', document.getElementById('subject_id').value);
            
            fetch("{{ route('marks.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Server error') });
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    saveButton.innerHTML = '<i class="bi bi-check-circle-fill"></i> Saved!';
                    saveButton.classList.remove('bg-green-500', 'hover:bg-green-600');
                    saveButton.classList.add('bg-indigo-500');
                } else {
                    throw new Error(data.message || 'Validation failed.');
                }
            })
            .catch(error => {
                console.error('Error saving marks:', error);
                saveButton.innerHTML = `<i class="bi bi-exclamation-triangle"></i> Error`;
                saveButton.classList.remove('bg-green-500', 'hover:bg-green-600');
                saveButton.classList.add('bg-red-500');
            })
            .finally(() => {
                setTimeout(() => {
                    saveButton.innerHTML = originalButtonContent;
                    saveButton.classList.remove('bg-indigo-500', 'bg-red-500');
                    saveButton.classList.add('bg-green-500', 'hover:bg-green-600');
                    saveButton.disabled = false;
                }, 2000);
            });
        });
    });
});
</script>
@endpush

