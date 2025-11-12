@extends('layouts.admin')

@section('title', 'Mark Student Attendance')
@section('page-title', 'Mark Attendance')

@push('styles')
    <style>
        /* Modern toggle-style radio buttons */
        .attendance-radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .attendance-radio {
            display: none;
        }

        .attendance-label {
            cursor: pointer;
            padding: 0.5rem 0.75rem;
            border-radius: 9999px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.25s ease;
            min-width: 70px;
            text-align: center;
        }

        .attendance-radio:checked+.attendance-label {
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .attendance-radio[value="present"]:checked+.attendance-label {
            background-color: #10b981;
            /* Green */
            border-color: #10b981;
        }

        .attendance-radio[value="absent"]:checked+.attendance-label {
            background-color: #ef4444;
            /* Red */
            border-color: #ef4444;
        }

        .attendance-radio[value="late"]:checked+.attendance-label {
            background-color: #f59e0b;
            /* Amber */
            border-color: #f59e0b;
        }

        .attendance-radio[value="leave"]:checked+.attendance-label {
            background-color: #6b7280;
            /* Gray */
            border-color: #6b7280;
        }

        tr:hover {
            background-color: #f9fafb;
        }
    </style>
@endpush

@section('content')

    {{-- Alerts --}}
    @if (session('success'))
        <div
            class="flex items-center gap-3 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg shadow-sm">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <div>
                <p class="font-semibold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-sm">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <div>
                <p class="font-semibold">Error</p>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Filter Form --}}
    <div class=" shadow-lg rounded-xl p-6 mb-8 border border-indigo-100">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Mark Student Attendance
        </h3>

        <form action="{{ route('attendance.fetch') }}" method="POST"
            class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
            @csrf
            <div>
                <label for="school_class_id" class="block text-sm font-semibold text-gray-700 mb-1">Class</label>
                <select id="school_class_id" name="school_class_id"
                    class="w-full p-2 rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ (isset($selectedClassId) && $selectedClassId == $class->id) ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="attendance_date" class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                <input type="date" id="attendance_date" name="attendance_date"
                    value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 p-2  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-3 py-2 shadow-md transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    Fetch Students
                </button>

                <a href="{{ route('attendance.showReport') }}"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-3 py-2 shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0h6m-6 0H5m6 0v-6m6 6v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0h6" />
                    </svg>
                    Monthly Report
                </a>
            </div>
        </form>
    </div>

    {{-- Student List --}}
    @if(isset($students))
        @if($students->isEmpty())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 mb-4 rounded-lg shadow-sm">
                <p class="font-semibold">No Students Found</p>
                <p>There are no active students enrolled in the selected class.</p>
            </div>
        @else
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        Attendance for
                        <span class="text-indigo-600">{{ $students->first()->schoolClass->name }}</span>
                        — {{ \Carbon\Carbon::parse($selectedDate)->format('d M, Y') }}
                    </h3>
                    <button type="button" id="markAllPresent"
                        class="flex items-center gap-2 rounded-md border border-green-500 bg-green-50 py-2 px-4 text-sm font-medium text-green-700 shadow-sm hover:bg-green-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mark All Present
                    </button>
                </div>

                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-gray-700">
                            <thead class="bg-indigo-50 text-gray-800 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-3 text-left">Student</th>
                                    <th class="px-6 py-3 text-left">ID Card #</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-left">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($students as $student)
                                    @php
                                        $existingAttendance = $student->attendances->first();
                                        $status = $existingAttendance->status ?? 'present';
                                    @endphp
                                    <tr class="hover:bg-indigo-50/50 transition">
                                        <td class="px-6 py-4 flex items-center gap-4">
                                            <img class="w-10 h-10 rounded-full object-cover border border-gray-200"
                                                src="{{ asset('storage/' . $student->user->user_pic) }}"
                                                alt="{{ $student->user->name }}">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $student->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $student->user->email ?? 'No Email' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700">
                                            {{ $student->id_card_number }}
                                            <input type="hidden" name="attendance[{{ $loop->index }}][student_id]"
                                                value="{{ $student->id }}">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="attendance-radio-group">
                                                <div>
                                                    <input type="radio" id="present_{{ $student->id }}"
                                                        name="attendance[{{ $loop->index }}][status]" value="present"
                                                        class="attendance-radio" {{ $status == 'present' ? 'checked' : '' }}>
                                                    <label for="present_{{ $student->id }}" class="attendance-label">Present</label>
                                                </div>
                                                <div>
                                                    <input type="radio" id="absent_{{ $student->id }}"
                                                        name="attendance[{{ $loop->index }}][status]" value="absent"
                                                        class="attendance-radio" {{ $status == 'absent' ? 'checked' : '' }}>
                                                    <label for="absent_{{ $student->id }}" class="attendance-label">Absent</label>
                                                </div>
                                                <div>
                                                    <input type="radio" id="late_{{ $student->id }}"
                                                        name="attendance[{{ $loop->index }}][status]" value="late"
                                                        class="attendance-radio" {{ $status == 'late' ? 'checked' : '' }}>
                                                    <label for="late_{{ $student->id }}" class="attendance-label">Late</label>
                                                </div>
                                                <div>
                                                    <input type="radio" id="leave_{{ $student->id }}"
                                                        name="attendance[{{ $loop->index }}][status]" value="leave"
                                                        class="attendance-radio" {{ $status == 'leave' ? 'checked' : '' }}>
                                                    <label for="leave_{{ $student->id }}" class="attendance-label">Leave</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" name="attendance[{{ $loop->index }}][remarks]"
                                                value="{{ $existingAttendance->remarks ?? '' }}" placeholder="Optional remarks"
                                                class="w-full rounded-md border-gray-300 focus:border-indigo-500 p-2 focus:ring-indigo-500 text-sm shadow-sm">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-6 py-2 shadow-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Attendance
                    </button>
                </div>
            </form>
        @endif
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const markAllPresentButton = document.getElementById('markAllPresent');
            if (markAllPresentButton) {
                markAllPresentButton.addEventListener('click', function () {
                    document.querySelectorAll('input[type="radio"][value="present"]').forEach(radio => {
                        radio.checked = true;
                    });
                });
            }
        });
    </script>
@endpush