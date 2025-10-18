@extends('layouts.admin')

@section('title', 'Student Result Cards')
@section('page-title', 'View Student Result Card')

@push('styles')
    {{-- Styling for the Result Card Modal and its print layout --}}
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border: 1px solid #888;
            width: 80%;
            max-width: 700px;
            border-radius: 0.3rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .5);
        }

        .modal-header {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            background-color: #f7f7f7;
            border-top-left-radius: 0.3rem;
            border-top-right-radius: 0.3rem;
        }

        .modal-header h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 500;
        }

        .modal-header .close {
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            background: transparent;
            border: 0;
            padding: 0;
            cursor: pointer;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }

        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: 0.3rem;
            border-bottom-left-radius: 0.3rem;
        }

        .modal-footer button {
            margin-left: 0.25rem;
        }

        .modal-open {
            overflow: hidden;
        }

        /* Table Styling for Modal */
        #resultCardTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        #resultCardTable th,
        #resultCardTable td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        #resultCardTable th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .pass-status {
            color: green;
            font-weight: bold;
        }

        .fail-status {
            color: red;
            font-weight: bold;
        }

        /* Print styles for the modal content */
        @media print {
            body * {
                visibility: hidden;
            }

            .modal-body,
            .modal-body * {
                visibility: visible;
            }

            .modal-body {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 10px;
                margin: 0;
            }

            .modal-footer,
            .modal-header .close,
            .no-print {
                display: none !important;
            }

            #resultCardTable th,
            #resultCardTable td {
                font-size: 9pt;
                padding: 5px;
            }

            h5,
            h6 {
                margin-top: 10px;
                margin-bottom: 5px;
                text-align: center;
                font-size: 11pt;
                color: #000;
            }
        }
    </style>
@endpush

@section('content')

    <!-- 1. Filters Card -->
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden mb-6 no-print">
        <div class="px-4 py-3 border-b bg-gradient-to-r from-indigo-500 to-indigo-600">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-person-vcard"></i> Select Criteria
            </h4>
        </div>
        <div class="p-4">
            {{-- Form submits to the index route to reload the page with students --}}
            <form id="filter-form" method="GET" action="{{ route('result-cards.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Semester -->
                    <div>
                        <label for="semester_id" class="block text-sm font-semibold mb-1 text-gray-700">Semester</label>
                        <select id="semester_id" name="semester_id" required
                            class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
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
                        <select id="class_id" name="class_id" required
                            class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedSchoolClassId == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Button -->
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full text-white bg-indigo-600 hover:bg-indigo-700 font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                            <i class="bi bi-search me-1"></i> Load Students
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Students List Table -->
    @if($students->isNotEmpty())
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden mb-6">
            <div class="px-4 py-3 border-b bg-gradient-to-r from-gray-700 to-gray-800">
                <h4 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="bi bi-people-fill"></i> Students List
                </h4>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="min-w-full table-auto text-sm text-center">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 font-semibold">#</th>
                            <th class="px-4 py-3 font-semibold">Student Name</th>
                            <th class="px-4 py-3 font-semibold">Class</th>
                            <th class="px-4 py-3 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="students-table-body" class="text-gray-700">
                        @foreach($students as $student)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $student->user->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $student->schoolClass->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <button type="button"
                                        class="view-result-btn text-white bg-blue-500 hover:bg-blue-600 font-bold py-1 px-3 rounded-md shadow-sm text-xs"
                                        data-student-id="{{ $student->id }}" data-semester-id="{{ $selectedSemesterId }}">
                                        <i class="bi bi-eye-fill"></i> View Result
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    <!-- 3. Result Card Modal (Initially Hidden) -->
    <div id="resultCardModal" class="modal">
        <div class="modal-content">
            <div
                class="modal-header flex items-center justify-between px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-md">
                <h5 class="modal-title text-lg md:text-xl font-semibold flex items-center gap-2">
                    <i class="bi bi-card-checklist text-xl"></i>
                    Student Result Card
                </h5>
                <button type="button" id="modalCloseBtn"
                    class="text-white hover:text-gray-200 text-2xl font-bold leading-none transition-transform transform hover:scale-110">
                    &times;
                </button>
            </div>

            <div class="modal-body">
                {{-- Content will be loaded via JS --}}
                <p>Loading result...</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="modalPrintBtn"
                    class="text-white bg-blue-600 hover:bg-blue-700 font-bold py-1 px-3 rounded-lg shadow-md text-sm">
                    <i class="bi bi-printer-fill"></i> Print
                </button>
                <button type="button" id="modalCloseFooterBtn"
                    class="text-gray-700 bg-gray-200 hover:bg-gray-300 font-bold py-1 px-3 rounded-lg shadow-md text-sm">Close</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const studentsTableBody = document.getElementById('students-table-body');
            const modal = document.getElementById('resultCardModal');
            const modalBody = modal.querySelector('.modal-body');
            const modalCloseBtns = [document.getElementById('modalCloseBtn'), document.getElementById('modalCloseFooterBtn')];
            const modalPrintBtn = document.getElementById('modalPrintBtn');

            // --- Helper Functions ---
            function openModal() {
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
            }

            function closeModal() {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }

            function printModalContent() {
                window.print();
            }

            // --- Fetch and Display Result Card in Modal (Event Delegation) ---
            if (studentsTableBody) {
                studentsTableBody.addEventListener('click', function (event) {
                    const button = event.target.closest('.view-result-btn');

                    if (button) {
                        const studentId = button.dataset.studentId;
                        const semesterId = button.dataset.semesterId;

                        if (!semesterId) {
                            alert('Please select a semester first.');
                            return;
                        }

                        modalBody.innerHTML = '<p class="text-center py-5">Loading result...</p>';
                        openModal();

                        fetch(`{{ url('result-cards/show') }}/${studentId}/${semesterId}`)
                            .then(response => {
                                if (!response.ok) throw new Error('Network response was not ok');
                                return response.json();
                            })
                            .then(data => {
                                let tableRows = '';
                                data.results.forEach((res, index) => {
                                    tableRows += `
                                                    <tr class="hover:bg-indigo-50 transition-colors duration-200">
                                                    <td class="py-3 px-4 text-center font-medium text-gray-800">${index + 1}</td>
                                                    <td class="py-3 px-4 text-center text-gray-700 font-semibold">${res.subject_name}</td>
                                                    <td class="py-3 px-4 text-center text-gray-600">${res.total_marks}</td>
                                                    <td class="py-3 px-4 text-center text-gray-600">${res.obtained_marks}</td>
                                                    <td class="py-3 px-4 text-center">
                                                        <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                                        ${res.status === 'Pass'
                                                                                            ? 'bg-green-100 text-green-700 border border-green-300'
                                                                                            : 'bg-red-100 text-red-700 border border-red-300'}">
                                                        ${res.status}
                                                        </span>
                                                    </td>
                                                    </tr>

                                                `;
                                });

                                const modalHtml = `
                                               <div class="text-center mb-5">
  <h5 class="text-2xl font-extrabold text-gray-800 mb-1 tracking-wide">
    ${data.student_name}
  </h5>
  <div class="flex justify-center gap-6 text-sm text-gray-600">
    <span class="bg-gray-100 px-4 py-1 rounded-full shadow-sm border border-gray-200">
      <i class="bi bi-person-lines-fill text-indigo-600 mr-1"></i>
      Class: <span class="font-medium text-gray-800">${data.class_name}</span>
    </span>
    <span class="bg-gray-100 px-4 py-1 rounded-full shadow-sm border border-gray-200">
      <i class="bi bi-book-half text-indigo-600 mr-1"></i>
      Semester: <span class="font-medium text-gray-800">${data.semester_name}</span>
    </span>
  </div>
</div>


                                               <table id="resultCardTable" class="w-full border-collapse mt-4 text-sm text-gray-700 rounded-xl overflow-hidden shadow-md">
                                                <thead>
                                                    <tr class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm uppercase tracking-wider text-center">
                                                    <th class="py-3 px-4 font-semibold">#</th>
                                                    <th class="py-3 px-4 font-semibold">Subject</th>
                                                    <th class="py-3 px-4 font-semibold">Total Marks</th>
                                                    <th class="py-3 px-4 font-semibold">Obtained Marks</th>
                                                    <th class="py-3 px-4 font-semibold">Status</th>
                                                    </tr>
                                                </thead>

                                                <tbody class="bg-white divide-y divide-gray-200 text-center">
                                                    ${tableRows}
                                                </tbody>

                                                <tfoot>
                                                    <tr class="bg-gray-100 font-semibold text-gray-800 text-center">
                                                    <td colspan="2" class="py-3 px-4 text-right">Total</td>
                                                    <td class="py-3 px-4">${data.total_possible}</td>
                                                    <td class="py-3 px-4">${data.total_obtained}</td>
                                                    <td></td>
                                                    </tr>
                                                </tfoot>
                                                </table>


                                                <div style="margin-top: 15px; text-align: center;">
                                                    <span class="font-semibold">Percentage: ${data.percentage}%</span> |
                                                    <span class="font-semibold">Overall Status:
                                                        <span class="${data.status === 'Pass' ? 'pass-status' : 'fail-status'}">${data.status}</span>
                                                    </span>
                                                </div>
                                            `;
                                modalBody.innerHTML = modalHtml;
                            })
                            .catch(error => {
                                console.error('Error fetching result card:', error);
                                modalBody.innerHTML = `<p class="text-center py-5 text-red-600 font-semibold">Could not load result card.</p>`;
                            });
                    }
                });
            }

            // --- Modal Close/Print Logic ---
            modalCloseBtns.forEach(btn => btn.addEventListener('click', closeModal));
            modalPrintBtn.addEventListener('click', printModalContent);
            window.addEventListener('click', function (event) {
                if (event.target == modal) {
                    closeModal();
                }
            });
        });
    </script>
@endpush