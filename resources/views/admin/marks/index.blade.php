@extends('layouts.admin')

@section('title', 'Student Marks Management')
@section('page-title', 'Enter & Update Student Marks')

{{-- 1. PURANA PRINT @push('styles') BLOCK HATA DIYA GAYA HAI --}}
{{-- DataTables ko uski zaroorat nahi hai --}}

@section('content')

{{-- 'no-print' class ki ab zaroorat nahi, lekin isay rakhna acha hai --}}
<div class="bg-white shadow-lg rounded-2xl overflow-hidden mb-6 no-print">
    <div class="px-4 py-3 border-b bg-gradient-to-r from-blue-500 to-blue-600">
        <h4 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="bi bi-funnel-fill"></i> Select Criteria to Load Students
        </h4>
    </div>
    <div class="p-4">
        <form id="filter-form" method="GET" action="{{ route('marks.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

                <div class="flex items-end">
                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                        <i class="bi bi-search me-1"></i> Load Students
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($students->isNotEmpty())
<div> 
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        
        {{-- Table Header --}}
        {{-- 'no-print' class ki ab zaroorat nahi, lekin isay rakhna acha hai --}}
        <div class="px-4 py-3 border-b bg-gradient-to-r from-gray-700 to-gray-800 flex justify-between items-center no-print">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Entering Marks for: {{ $subjects->find($selectedSubjectId)->name ?? '' }} - {{ $classes->find($selectedSchoolClassId)->name ?? '' }}
            </h4>
            <div class="flex items-center gap-2 no-print">
                {{-- Export Button Form (Yeh DataTables mein shamil nahi hai, isko rakhain) --}}
                <form method="GET" action="{{ route('marks.export') }}">
                    <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                    <input type="hidden" name="class_id" value="{{ $selectedSchoolClassId }}">
                    <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-bold py-1 px-3 rounded-lg shadow-md text-sm flex items-center gap-1 transition-transform transform hover:scale-105">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                        <span>Export</span>
                    </button>
                </form>
                {{-- 2. PURANA PRINT BUTTON (id="print-btn") HATA DIYA GAYA HAI --}}
                {{-- Hum DataTables ka print button istemal karain gay --}}
            </div>
        </div>

        <div class="overflow-x-auto p-4"> {{-- Thori padding add ki hai --}}
            <table id="studentsTable" class="min-w-full table-auto text-sm text-center">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold"><i class="bi bi-person-badge"></i> Student Name</th>
                        <th class="px-4 py-3 font-semibold"><i class="bi bi-hash"></i> Roll Number</th>
                        <th class="px-4 py-3 font-semibold"><i class="bi bi-card-heading"></i> Total Marks</th>
                        <th class="px-4 py-3 font-semibold"><i class="bi bi-check2-circle"></i> Obtained Marks</th>
                        {{-- 'no-print' class DataTables ke print button ke liye bhi kaam karti hai --}}
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
                        <td class="px-4 py-2">{{ $student->school_class_id . $student->id }}</td>
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
    $(document).ready(function() {
        $('#studentsTable').DataTable({
            dom: 
                "<'flex flex-col md:flex-row justify-between items-center my-3 mx-3'<'flex items-center gap-2'B><'ml-auto'f>>" + // Buttons + Search
                "<'overflow-x-auto'tr>" + // Table
                "<'flex flex-col md:flex-row justify-between items-center my-3 mx-3'<'text-sm'i><'mt-2 md:mt-0'p>>", // Info + Pagination
            buttons: [
                {
                    extend: 'excel',
                    className: 'text-white bg-black-600 hover:bg-black-700 font-bold py-1 px-3 rounded-lg shadow-md text-sm flex items-center gap-1',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4], 
                        format: {
                            body: function(data, row, col, node) {
                                // FIX: Input ko naam (name) se dhoondain
                                if (col === 3) {
                                    return $(node).find('input[name="total_marks"]').val();
                                }
                                if (col === 4) {
                                    return $(node).find('input[name="obtained_marks"]').val();
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdf',
                    className: 'text-white bg-black-600 hover:bg-black-700 font-bold py-1 px-3 rounded-lg shadow-md text-sm flex items-center gap-1',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4],
                        format: {
                            body: function(data, row, col, node) {
                                // FIX: Input ko naam (name) se dhoondain
                                if (col === 3) {
                                    return $(node).find('input[name="total_marks"]').val();
                                }
                                if (col === 4) {
                                    return $(node).find('input[name="obtained_marks"]').val();
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    className: 'text-white bg-black-600 hover:bg-black-700 font-bold py-1 px-3 rounded-lg shadow-md text-sm flex items-center gap-1',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4],
                        format: {
                            body: function(data, row, col, node) {
                                // FIX: Input ko naam (name) se dhoondain
                                // col === 3 (Total Marks)
                                if (col === 3) {
                                    return $(node).find('input[name="total_marks"]').val();
                                }
                                // col === 4 (Obtained Marks)
                                if (col === 4) {
                                    return $(node).find('input[name="obtained_marks"]').val();
                                }
                                // Baaqi columns ke liye normal text
                                return data;
                            }
                        }
                    },
                    customize: function (win) {
                        // Custom CSS for the print window
                        let css = `
                            @media print {
                                body { padding: 20px; font-family: Arial, sans-serif; }
                                h1 { text-align: center; font-size: 1.5rem; margin-bottom: 20px; }
                                table { width: 100% !important; border-collapse: collapse !important; font-size: 10pt !important; }
                                th, td {
                                    border: 1px solid #D1D5DB !important;
                                    padding: 10px 8px !important;
                                    text-align: center !important;
                                    vertical-align: middle !important;
                                }
                                thead th {
                                    background-color: #F3F4F6 !important;
                                    color: #1F2937 !important;
                                    font-weight: 600 !important;
                                }
                                tbody tr:nth-child(even) { background-color: #F9FAFB; }
                                .dt-print-footer { display: none; }
                            }
                        `;
                        
                        // Add styles to the print window
                        $(win.document.head).append('<style>' + css + '</style>');
                        
                        // Get Class and Subject names for the title
                        let className = $("#class_id option:selected").text().trim();
                        let subjectName = $("#subject_id option:selected").text().trim();
                        
                        // Set the print window title
                        $(win.document.head).find('title').text('Marks - ' + className + ' - ' + subjectName);
                        
                        // Add a title above the table
                        $(win.document.body).prepend(
                            '<h1>Marks List: ' + className + ' - ' + subjectName + '</h1>'
                        );
                    }
                },
                {
                    extend: 'colvis',
                    className: 'text-white bg-black-600 hover:bg-black-700 font-bold py-1 px-3 rounded-lg shadow-md text-sm flex items-center gap-1'
                }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            columnDefs: [
                {
                    orderable: false,
                    targets: 5 // Disable sorting on 'Actions' column (index 5)
                }
            ]
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
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

    // Save Button functionality
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