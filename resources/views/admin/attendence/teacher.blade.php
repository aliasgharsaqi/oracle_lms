@extends('layouts.admin')

@section('title', 'Attendance')
@section('page-title', 'Teacher Attendance')

@section('content')

    <div class=" p-6 rounded-2xl shadow-lg max-w-2xl mx-auto my-6">
    <form method="GET" action="{{ route('attendence.teacher') }}" class="space-y-4">
        <h2 class="text-xl font-semibold text-gray-800 mb-3">📅 Attendance Filter</h2>

        <div>
            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                Select Date
            </label>
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <input 
                    type="date" 
                    name="date" 
                    id="date"
                    value="{{ $selected_date->format('Y-m-d') }}"
                    class="w-full sm:w-auto flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-gray-700"
                >

                <button 
                    type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg transition-all duration-300"
                >
                    Filter
                </button>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="text-center flex flex-col sm:flex-row justify-center items-center gap-4 sm:gap-6">
            <a 
                href="{{ route('attendence.teacher.monthly_report') }}" 
                class="inline-flex items-center text-blue-700 font-medium hover:text-blue-800 transition-all duration-300"
            >
                View Monthly Report 
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <span class="text-gray-300 hidden sm:inline">|</span>

            {{-- NEW PENDING LEAVES LINK --}}
            <a 
                href="{{ route('attendence.teacher.pending_leaves') }}" 
                class="inline-flex items-center text-yellow-700 font-medium hover:text-yellow-800 transition-all duration-300 relative"
            >
                View Pending Leaves
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{-- You can add a badge here later if you pass a $pending_count --}}
            </a>
        </div>
    </form>
</div>


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach ($teachers as $teacher)
            {{-- Set up dynamic status variables --}}
            @php
                $attendance = $teacher->attendanceRecord; 
                $isToday = $selected_date->isToday();

                $status = $attendance->status ?? 'none';
                
                // NEW: Check leave status
                $leaveStatus = $attendance->leave_status ?? null;
                $leaveType = $attendance->leave_type ?? null;
                $isLeavePending = $leaveStatus === 'pending';
                $isLeaveApproved = $leaveStatus === 'approved';
                $isLeaveRejected = $leaveStatus === 'rejected';

                $hasCheckedIn = $attendance && $attendance->check_in && !$attendance->check_out; 
                $hasCheckedOut = $attendance && $attendance->check_out;
                
                $isAbsent = $status === 'absent';
                // Only "Approved" leave counts as leave now
                $isLeave = $status === 'leave' && $isLeaveApproved;
                $isShortLeave = $status === 'short_leave' && $isLeaveApproved;
                $isLate = $status === 'late_arrival';
                $isPresent = $status === 'present';

                // Determine dot color
                $dotColor = 'bg-gray-400'; // Default (not marked)
                if ($isPresent) $dotColor = 'bg-green-500';
                if ($isLate) $dotColor = 'bg-yellow-500'; 
                if ($isAbsent) $dotColor = 'bg-red-500';
                if ($isLeave || $isShortLeave) $dotColor = 'bg-blue-500'; // Approved Leave
                
                if ($hasCheckedIn) $dotColor = 'bg-green-500'; // Present
                if ($hasCheckedOut) $dotColor = 'bg-blue-500'; // Checked Out
                
                // NEW: Pending status overrides all
                if ($isLeavePending) $dotColor = 'bg-yellow-500'; // Pending
                
                // Set title for status dot
                $statusTitle = ucfirst(str_replace('_', ' ', $status));
                if ($isLeavePending) {
                    $statusTitle = 'Leave Pending';
                } elseif ($isLeave) {
                    $statusTitle = 'Leave (Approved)';
                } elseif ($isShortLeave) {
                    $statusTitle = 'Short Leave (Approved)';
                }
            @endphp

            {{-- Main Attendance Card --}}
            <div class="bg-white w-full shadow-lg rounded-2xl p-4 space-y-4">

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                         @if($teacher->user->user_pic ?? '')
                                <img src="{{ asset('storage/' . $teacher->user->user_pic) }}"
                                    alt="{{ $teacher->user->name }}"
                                    class="rounded-full shadow-sm w-11 h-11 object-cover">
                                @endif
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ optional($teacher->user)->name ?? 'No Name' }}</h2>
                            <p class="text-xs text-gray-500">{{ optional($teacher->user)->email ?? 'No Email' }}</p>
                        </div>
                    </div>
                    {{-- UPDATED: Title now reflects pending status --}}
                    <div id="status-dot-{{ $teacher->id }}" class="w-3 h-3 {{ $dotColor }} rounded-full shadow-sm transition-colors duration-300" title="Status: {{ $statusTitle }}"></div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                    <span class="text-sm text-gray-600 font-medium">Monthly Salary</span>
                    <span class="text-2xl font-bold text-blue-600">
                        {{ number_format($teacher->monthly_salary ?? 50000) }}
                        <span class="text-gray-500 text-sm font-normal ml-1">PKR</span>
                    </span>
                </div>

                <div class="pt-4 border-t border-gray-100 space-y-2">
                    <p class="text-gray-800 font-semibold text-base mb-3">
                        Attendance for: {{ $selected_date->format('M d, Y') }}
                    </p>

                    {{-- Check if the selected date is TODAY --}}
                    @if ($isToday)
                        {{-- ********* TODAY's LOGIC (Buttons with timestamps) ********* --}}
                        
                        @if ($hasCheckedOut)
                            <p class="text-sm text-center text-blue-600 font-medium p-3 bg-blue-50 rounded-lg">
                                Checked out for the day.
                            </p>
                            @if ($attendance->check_in)
                                <p class="text-xs text-center text-gray-500">
                                    In: {{ $attendance->check_in->format('h:i A') }} | Out: {{ $attendance->check_out->format('h:i A') }}
                                </p>
                            @endif
                        
                        @else
                            {{-- Show all buttons with active/disabled states --}}
                            
                            @if ($hasCheckedIn)
                                {{-- Show Check Out button --}}
                                <button data-teacher-id="{{ $teacher->id }}" data-action="check_out"
                                    class="btn-attendance-action w-full flex items-center justify-center gap-2 bg-blue-100 text-blue-700 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm hover:bg-blue-200 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0-3-3m0 0 3-3m3 3H9" /></svg>
                                    Check Out ({{ $attendance->check_in->format('h:i A') }})
                                </button>
                            @else
                                {{-- Show Check In button --}}
                                @php
                                    // UPDATED: Cannot check in if leave is pending
                                    $checkInDisabled = $isPresent || $isLate || $isLeavePending || $isLeave || $isShortLeave;
                                @endphp
                                <button data-teacher-id="{{ $teacher->id }}" data-action="check_in"
                                    class="btn-attendance-action w-full flex items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                    {{ $checkInDisabled ? 'bg-green-500 text-white cursor-not-allowed' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                    {{ $checkInDisabled ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                                    {{ $checkInDisabled ? 'Checked In' : 'Check In' }}
                                </button>
                            @endif

                            <div class="grid grid-cols-2 gap-2">
                                <button data-teacher-id="{{ $teacher->id }}" data-action="absent"
                                    class="btn-attendance-action flex-1 flex items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                    {{ $isAbsent ? 'bg-red-500 text-white cursor-not-allowed' : 'bg-red-100 text-red-700 hover:bg-red-200' }}
                                    {{ $isAbsent || $hasCheckedIn || $isLeavePending ? 'disabled' : '' }}">
                                    Absent
                                </button>
                                
                                @php
                                    $lateDisabled = $isLate || $hasCheckedIn || $isAbsent || $isLeave || $isShortLeave || $isLeavePending;
                                @endphp
                                <button data-teacher-id="{{ $teacher->id }}" data-action="late_arrival"
                                    class="btn-attendance-action flex-1 flex items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                    {{ $isLate ? 'bg-yellow-500 text-white cursor-not-allowed' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}
                                    {{ $lateDisabled ? 'disabled' : '' }}">
                                    Late
                                </button>
                                
                                {{-- UPDATED: Short Leave Button --}}
                                <button data-teacher-id="{{ $teacher->id }}" data-action="short_leave" data-teacher-name="{{ optional($teacher->user)->name }}"
                                    class="btn-leave-modal flex-1 flex items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                    {{ $isLeavePending && $leaveType == 'short_leave' ? 'bg-yellow-500 text-white cursor-not-allowed' : ($isShortLeave ? 'bg-purple-500 text-white cursor-not-allowed' : 'bg-purple-100 text-purple-700 hover:bg-purple-200') }}"
                                    {{ $isLeavePending || $isShortLeave ? 'disabled' : '' }}>
                                    {{ $isLeavePending && $leaveType == 'short_leave' ? 'Pending' : ($isShortLeave ? 'On Leave' : 'Short Leave') }}
                                </button>

                                {{-- UPDATED: Full Leave Button --}}
                                <button data-teacher-id="{{ $teacher->id }}" data-action="leave" data-teacher-name="{{ optional($teacher->user)->name }}"
                                    class="btn-leave-modal flex-1 flex items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                    {{ $isLeavePending && $leaveType == 'leave' ? 'bg-yellow-500 text-white cursor-not-allowed' : ($isLeave ? 'bg-blue-500 text-white cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200') }}"
                                    {{ $isLeavePending || $isLeave ? 'disabled' : '' }}>
                                    {{ $isLeavePending && $leaveType == 'leave' ? 'Pending' : ($isLeave ? 'On Leave' : 'Apply Leave') }}
                                </button>
                            </div>
                        @endif

                    @else
                        {{-- ********* PAST DATE LOGIC (Buttons for STATUS only) ********* --}}
                        
                        @php
                            $presentActive = $isPresent;
                            $absentActive = $isAbsent && !$isLeavePending; // Don't show active absent if leave is pending
                            $leaveActive = $isLeave;
                            $shortLeaveActive = $isShortLeave;
                            $lateActive = $isLate;
                        @endphp

                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                data-teacher-id="{{ $teacher->id }}" 
                                data-action="present"
                                class="btn-past-action flex-1 items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                {{ $presentActive ? 'bg-green-500 text-white cursor-not-allowed' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                {{ $presentActive || $isLeavePending ? 'disabled' : '' }}>
                                Present
                            </button>
                            <button 
                                data-teacher-id="{{ $teacher->id }}" 
                                data-action="absent"
                                class="btn-past-action flex-1 items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                {{ $absentActive ? 'bg-red-500 text-white cursor-not-allowed' : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                                {{ $absentActive || $isLeavePending ? 'disabled' : '' }}>
                                Absent
                            </button>
                            <button 
                                data-teacher-id="{{ $teacher->id }}" 
                                data-action="late_arrival"
                                class="btn-past-action flex-1 items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                {{ $lateActive ? 'bg-yellow-500 text-white cursor-not-allowed' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}"
                                {{ $lateActive || $isLeavePending ? 'disabled' : '' }}>
                                Late
                            </button>
                            
                            {{-- UPDATED: Past Leave Button --}}
                            <button 
                                data-teacher-id="{{ $teacher->id }}" 
                                data-action="leave"
                                data-teacher-name="{{ optional($teacher->user)->name }}"
                                class="btn-past-leave-modal flex-1 items-center justify-center gap-2 text-sm font-semibold px-3 py-2 rounded-lg shadow-sm transition-all
                                {{ $isLeavePending ? 'bg-yellow-500 text-white cursor-not-allowed' : ($leaveActive ? 'bg-blue-500 text-white cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200') }}"
                                {{ $isLeavePending || $leaveActive ? 'disabled' : '' }}>
                                {{ $isLeavePending ? 'Pending' : ($leaveActive ? 'On Leave' : 'Leave') }}
                            </button>
                        </div>
                        
                        @if ($attendance && $attendance->notes)
                            <p class="text-xs text-gray-600 mt-2">
                                <strong>Notes:</strong> {{ $attendance->notes }}
                                @if ($isLeaveRejected)
                                    <span class="font-bold text-red-600">(Leave Rejected)</span>
                                @endif
                            </p>
                        @endif

                    @endif
                </div>

            </div>
        @endforeach
    </div>

    <div id="leave-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900" id="modal-title">Apply Leave</h3>
                <button id="modal-close-btn" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            
            <form id="leave-modal-form" data-form-type="today"> {{-- Naya attribute --}}
                <input type="hidden" id="modal_teacher_id" name="teacher_id">
                <input type="hidden" id="modal_leave_type" name="leave_type">

                <div class="mb-4">
                    <p class="text-sm">Applying for: <span id="modal-teacher-name" class="font-semibold"></span></p>
                </div>
                
                <div>
                    <label for="modal_reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea id="modal_reason" name="reason" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="Please provide a reason..."></textarea>
                    <p id="modal-error" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" id="modal-cancel-btn" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-200 transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="modal-submit-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-blue-700 transition-all">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- 1. AAJ (Today) ke Quick Actions (Check In, Out, Absent, Late) ---
        document.body.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-attendance-action');
            if (button) {
                const teacherId = button.dataset.teacherId;
                const action = button.dataset.action;

                if (!teacherId || !action) return;

                button.disabled = true;
                button.innerHTML = 'Processing...';

                fetch("{{ route('attendence.teacher.mark') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        teacher_id: teacherId,
                        action: action
                    })
                })
                .then(response => response.json().then(data => ({ok: response.ok, data})))
                .then(({ok, data}) => {
                    if (ok) {
                        location.reload(); 
                    } else {
                        throw new Error(data.error || 'Request failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                    location.reload(); // Error pe bhi reload karein
                });
            }
        });

        // --- 2. AAJ (Today) ka Leave Modal Trigger ---
        const modal = document.getElementById('leave-modal');
        const modalForm = document.getElementById('leave-modal-form');
        const modalCloseBtn = document.getElementById('modal-close-btn');
        const modalCancelBtn = document.getElementById('modal-cancel-btn');
        const modalSubmitBtn = document.getElementById('modal-submit-btn');
        const modalError = document.getElementById('modal-error');
        
        document.body.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-leave-modal');
            if (button) {
                if (button.disabled) return; 

                document.getElementById('modal_teacher_id').value = button.dataset.teacherId;
                document.getElementById('modal_leave_type').value = button.dataset.action;
                document.getElementById('modal-teacher-name').textContent = button.dataset.teacherName;
                document.getElementById('modal-title').textContent = (button.dataset.action === 'leave' ? 'Apply Full-Day Leave' : 'Apply Short Leave');
                
                modalForm.dataset.formType = 'today'; // Set form type TODAY
                
                modalError.classList.add('hidden');
                modalForm.reset(); 
                modal.classList.remove('hidden'); 
            }
        });

        // --- 3. NAYA: PAST Date ke Quick Actions (Present, Absent, Late) ---
        document.body.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-past-action');
            if (button) {
                const teacherId = button.dataset.teacherId;
                const action = button.dataset.action;
                const selectedDate = document.getElementById('date').value;

                if (!teacherId || !action || !selectedDate) return;

                button.disabled = true;
                button.innerHTML = 'Updating...';

                fetch("{{ route('attendence.teacher.update_past') }}", { // Naya Route
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        teacher_id: teacherId,
                        action: action,
                        date: selectedDate // Date ko sath bhejein
                    })
                })
                .then(response => response.json().then(data => ({ok: response.ok, data})))
                .then(({ok, data}) => {
                    if (ok) {
                        location.reload(); 
                    } else {
                        throw new Error(data.error || 'Request failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                    location.reload();
                });
            }
        });

        // --- 4. NAYA: PAST Date ka Leave Modal Trigger ---
        document.body.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-past-leave-modal');
            if (button) {
                if (button.disabled) return; 

                document.getElementById('modal_teacher_id').value = button.dataset.teacherId;
                document.getElementById('modal_leave_type').value = button.dataset.action;
                document.getElementById('modal-teacher-name').textContent = button.dataset.teacherName;
                document.getElementById('modal-title').textContent = 'Update Past Leave';
                
                modalForm.dataset.formType = 'past'; // Set form type PAST
                
                modalError.classList.add('hidden');
                modalForm.reset(); 
                modal.classList.remove('hidden'); 
            }
        });

        // --- 5. MODIFIED: Reusable Modal Submit Handler ---
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            modalSubmitBtn.disabled = true;
            modalSubmitBtn.textContent = 'Submitting...';
            modalError.classList.add('hidden');
            
            const formData = new FormData(modalForm);
            const selectedDate = document.getElementById('date').value;
            
            // Ye check karein ke form "today" ka hai ya "past" ka
            const formType = modalForm.dataset.formType || 'today';
            let url = "";

            if (formType === 'past') {
                url = "{{ route('attendence.teacher.update_past') }}";
                formData.append('date', selectedDate); // Past date ke liye date add karein

                // ==========================================================
                // === THE FIX IS HERE ===
                // The controller expects 'action', but the form only has 'leave_type'.
                // We must read the value from 'leave_type' and add it as 'action'.
                var action = document.getElementById('modal_leave_type').value;
                formData.append('action', action);
                // ==========================================================

            } else {
                url = "{{ route('attendence.teacher.leave') }}";
                // Today ke liye date add karne ki zaroorat nahi, controller khud kar lega
                // The 'apply_leave' controller correctly reads 'leave_type', so no change is needed.
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ok: response.ok, data})))
            .then(({ok, data}) => {
                if (ok) {
                    closeModal();
                    
                    // Give specific feedback
                    if (data.leave_status === 'pending') {
                        alert('Leave request submitted and is pending approval.');
                    } else {
                        alert('Attendance updated successfully!');
                    }
                    
                    location.reload(); 
                } else {
                    // This is where your error was likely coming from (Validation Error)
                    if (data.errors) {
                        if (data.errors.reason) {
                            throw new Error(data.errors.reason[0]);
                        }
                        if (data.errors.action) {
                            throw new Error(data.errors.action[0]);
                        }
                    }
                    throw new Error(data.error || 'An error occurred. Please check console.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalError.textContent = error.message;
                modalError.classList.remove('hidden');
            })
            .finally(() => {
                modalSubmitBtn.disabled = false;
                modalSubmitBtn.textContent = 'Submit';
            });
        });

        // Close Modal ke universal helpers
        function closeModal() {
            modal.classList.add('hidden');
        }
        modalCloseBtn.addEventListener('click', closeModal);
        modalCancelBtn.addEventListener('click', closeModal);
    });
</script>
@endpush