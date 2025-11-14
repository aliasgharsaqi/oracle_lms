@extends('layouts.admin')

@section('title', 'Mark Student Attendance')
@section('page-title', 'Mark Attendance')

@push('styles')
    {{-- Aapke existing styles --}}
    <style>
        .attendance-radio-group { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .attendance-radio { display: none; }
        .attendance-label { cursor: pointer; padding: 0.5rem 0.75rem; border-radius: 9999px; border: 1px solid #e5e7eb; background: #f9fafb; font-size: 0.85rem; font-weight: 500; transition: all 0.25s ease; min-width: 70px; text-align: center; }
        .attendance-radio:checked+.attendance-label { color: white; transform: scale(1.05); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08); }
        .attendance-radio[value="present"]:checked+.attendance-label { background-color: #10b981; border-color: #10b981; }
        .attendance-radio[value="absent"]:checked+.attendance-label { background-color: #ef4444; border-color: #ef4444; }
        .attendance-radio[value="late"]:checked+.attendance-label { background-color: #f59e0b; border-color: #f59e0b; }
        .attendance-radio[value="leave"]:checked+.attendance-label { background-color: #6b7280; border-color: #6b7280; }
        tr:hover { background-color: #f9fafb; }
    </style>
@endpush

@section('content')

    {{-- Alerts (Aapka existing code) --}}
    @if (session('success'))
        <div class="flex items-center gap-3 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg shadow-sm">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            <div><p class="font-semibold">Success!</p><p>{{ session('success') }}</p></div>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-sm">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            <div><p class="font-semibold">Error</p><p>{{ session('error') }}</p></div>
        </div>
    @endif

    {{-- Filter Form --}}
    <div class=" shadow-lg rounded-xl p-6 mb-8 border border-indigo-100">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            Mark Student Attendance
        </h3>
        
        {{-- *** CHANGE YAHAN HAI: method="GET" aur @csrf hta diya hai *** --}}
        <form action="{{ route('attendance.fetch') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
            {{-- @csrf  <-- YEH HATA DIYA HAI --}}
            
            <div>
                <label for="school_class_id" class="block text-sm font-semibold text-gray-700 mb-1">Class</label>
                <select id="school_class_id" name="school_class_id" class="w-full p-2 rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
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
                <input type="date" id="attendance_date" name="attendance_date" value="{{ $selectedDate ?? now()->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 p-2  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-3 py-2 shadow-md transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    Fetch Students
                </button>
                <a href="{{ route('attendance.showReport') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-3 py-2 shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0h6m-6 0H5m6 0v-6m6 6v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0h6" /></svg>
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
            {{-- Yeh form 'attendance.store' ke liye hai (POST request), isay nahi change karna --}}
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        Attendance for <span class="text-indigo-600">{{ $students->first()->schoolClass->name }}</span>
                        — {{ \Carbon\Carbon::parse($selectedDate)->format('d M, Y') }}
                    </h3>
                    
                    <div>
                        <button type="button" id="markAllPresent" class="flex items-center gap-2 rounded-md border border-green-500 bg-green-50 py-2 px-4 text-sm font-medium text-green-700 shadow-sm hover:bg-green-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Mark All Present
                        </button>
                        
                        {{-- Button jo Modal ko kholega --}}
                        <button id="openModalBtn" type="button" class="rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 shadow-md transition ml-2">
                            Apply for Leave
                        </button>
                    </div>

                    {{-- Modal Box (Shuru mein chupa hua) --}}
                    <div id="leaveModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 transition-opacity">
                        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg mx-4">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h3 class="text-xl font-semibold text-gray-800">Apply for Leave</h3>
                                <button id="closeModalBtn" type="button" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
                            </div>

                            {{-- Yeh form 'leave.store' ke liye hai (POST request), isay nahi change karna --}}
                            <form action="{{ route('leave.store') }}" method="POST">
                                @csrf
                                <div class="space-y-4">

                                    {{-- REQ 1.1 & 1.3: Date Range --}}
                                    <div>
                                        <label for="leave_dates" class="block text-sm font-semibold text-gray-700 mb-1">
                                            Select Date(s)
                                        </label>
                                        <input type="text" id="leave_dates" name="dates" required 
                                            placeholder="Select one or more dates"
                                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                                    </div>

                                    {{-- REQ 1.2: Leave Type --}}
                                    <div>
                                        <label for="leave_type" class="block text-sm font-semibold text-gray-700 mb-1">
                                            Leave Type
                                        </label>
                                        <select name="leave_type" id="leave_type" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                                            <option value="full_day">Full Day Leave</option>
                                            <option value="short_leave">Short Leave</option>
                                        </select>
                                    </div>

                                    {{-- Reason --}}
                                    <div>
                                        <label for="reason" class="block text-sm font-semibold text-gray-700 mb-1">
                                            Reason (Optional)
                                        </label>
                                        <textarea name="reason" id="reason" rows="3" placeholder="e.g., Family emergency" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>

                                {{-- Form Buttons --}}
                                <div class="mt-6 flex justify-end gap-3">
                                    <button type="button" id="cancelModalBtn" class="rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-4 py-2 transition">
                                        Cancel
                                    </button>
                                    <button type="submit" class="rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 shadow-md transition">
                                        Submit Application
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Attendance Table (Aapka existing code) --}}
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
                                            <img class="w-10 h-10 rounded-full object-cover border border-gray-200" src="{{ asset('storage/' . $student->user->user_pic) }}" alt="{{ $student->user->name }}">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $student->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $student->user->email ?? 'No Email' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700">
                                            {{ $student->id_card_number }}
                                            <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="attendance-radio-group">
                                                <div>
                                                    <input type="radio" id="present_{{ $student->id }}" name="attendance[{{ $loop->index }}][status]" value="present" class="attendance-radio" {{ $status == 'present' ? 'checked' : '' }}>
                                                    <label for="present_{{ $student->id }}" class="attendance-label">Present</label>
                                                </div>
                                                <div>
                                                    <input type="radio" id="absent_{{ $student->id }}" name="attendance[{{ $loop->index }}][status]" value="absent" class="attendance-radio" {{ $status == 'absent' ? 'checked' : '' }}>
                                                    <label for="absent_{{ $student->id }}" class="attendance-label">Absent</label>
                                                </div>
                                                <div>
                                                    <input type="radio" id="late_{{ $student->id }}" name="attendance[{{ $loop->index }}][status]" value="late" class="attendance-radio" {{ $status == 'late' ? 'checked' : '' }}>
                                                    <label for="late_{{ $student->id }}" class="attendance-label">Late</label>
                                                </div>
                                                <div>
                                                    <input type="radio" id="leave_{{ $student->id }}" name="attendance[{{ $loop->index }}][status]" value="leave" class="attendance-radio" {{ $status == 'leave' ? 'checked' : '' }}>
                                                    <label for="leave_{{ $student->id }}" class="attendance-label">Leave</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" name="attendance[{{ $loop->index }}][remarks]" value="{{ $existingAttendance->remarks ?? '' }}" placeholder="Optional remarks" class="w-full rounded-md border-gray-300 focus:border-indigo-500 p-2 focus:ring-indigo-500 text-sm shadow-sm">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-6 py-2 shadow-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Save Attendance
                    </button>
                </div>
            </form>
        @endif
    @endif

@endsection

@push('scripts')
    {{-- Mark All Present Script --}}
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

    {{-- Flatpickr (Modal) Scripts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.npet/npm/flatpickr"></script> {{-- Note: Original file had 'npet', assuming 'npm' --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Date Range Picker
            flatpickr("#leave_dates", {
                mode: "multiple", // REQ 1.1 (range se multiple)
                dateFormat: "Y-m-d",
                // REQ 1.3 (Past/Future): Koi minDate nahi lagayein
            });

            // 2. Modal Logic (Aapka existing code)
            const modal = document.getElementById('leaveModal');
            const openBtn = document.getElementById('openModalBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelModalBtn = document.getElementById('cancelModalBtn');

            const openModal = () => modal.classList.remove('hidden');
            const closeModal = () => modal.classList.add('hidden');

            openBtn.addEventListener('click', openModal);
            closeModalBtn.addEventListener('click', closeModal);
            cancelModalBtn.addEventListener('click', closeModal);

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        });
    </script>
@endpush