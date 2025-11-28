@extends('layouts.admin')

@section('title', 'Attendance')
@section('page-title', 'Teacher Attendance')

@section('content')

{{-- ========================================================= --}}
{{-- 1. TOP SETTINGS & FILTER CARD --}}
{{-- ========================================================= --}}
<div class="p-6 rounded-2xl shadow-lg max-w-4xl mx-auto my-6 bg-white">
    {{-- Filter Form --}}
    <form method="GET" action="{{ route('attendence.teacher') }}" class="space-y-4" id="filter-form">
        
        <div class="flex justify-between items-center mb-3 border-b border-gray-100 pb-2">
            <h2 class="text-xl font-semibold text-gray-800">📅 Attendance Settings</h2>
            
            {{-- SAVE BUTTON (Saves time to Database via AJAX) --}}
            <button type="button" id="btn-save-time" 
                class="text-xs bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-black transition-all flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                Save Default Time
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            {{-- Date Input --}}
            <div>
                <label for="date" class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Date</label>
                <input type="date" name="date" id="date" value="{{ $selected_date->format('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-gray-700">
            </div>
            
            {{-- Start Time Input --}}
            <div>
                <label for="school_start_time" class="block text-xs font-bold text-gray-500 uppercase mb-1">School Start Time</label>
                {{-- Value Controller se aa rahi hai (Database/Default) --}}
                <input type="time" name="school_start_time" id="school_start_time" value="{{ $schoolStartTime }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-3 py-2 text-gray-700 font-mono bg-green-50">
                <p class="text-[10px] text-gray-400 mt-1">Check-in after this is marked <b>LATE</b> automatically.</p>
            </div>

            {{-- End Time Input --}}
            <div>
                <label for="school_end_time" class="block text-xs font-bold text-gray-500 uppercase mb-1">School End Time</label>
                <input type="time" name="school_end_time" id="school_end_time" value="{{ $schoolEndTime }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-3 py-2 text-gray-700 font-mono bg-red-50">
                <p class="text-[10px] text-gray-400 mt-1">Used for check-out reference.</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2 border-t border-gray-100">
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg transition-all">
                Filter Records
            </button>

            <div class="flex gap-4 text-sm font-medium">
                <a href="{{ route('attendence.teacher.monthly_report') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    Monthly Report <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('attendence.teacher.pending_leaves') }}" class="text-yellow-600 hover:text-yellow-800 flex items-center gap-1">
                    Pending Leaves <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ========================================================= --}}
{{-- 2. TEACHERS GRID --}}
{{-- ========================================================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($teachers as $teacher)
        @php
            // --- LOGIC BLOCK ---
            $attendance = $teacher->attendanceRecord; 
            $isToday = $selected_date->isToday();
            $status = $attendance->status ?? 'none';
            
            $leaveStatus = $attendance->leave_status ?? null;
            $leaveType = $attendance->leave_type ?? null;
            $isLeavePending = $leaveStatus === 'pending';
            $isLeaveApproved = $leaveStatus === 'approved';
            
            $hasCheckedIn = $attendance && $attendance->check_in && !$attendance->check_out; 
            $hasCheckedOut = $attendance && $attendance->check_out;
            $isAbsent = $status === 'absent';
            $isLeave = $status === 'leave' && $isLeaveApproved;
            $isShortLeave = $status === 'short_leave' && $isLeaveApproved;
            $isLate = $status === 'late_arrival';
            $isPresent = $status === 'present';

            // Late Time Formatter (e.g., 1h 15m)
            $lateTimeText = null;
            if ($attendance && $attendance->late_minutes > 0) {
                $lateMins = $attendance->late_minutes;
                $h = floor($lateMins / 60);
                $m = $lateMins % 60;
                $lateTimeText = ($h > 0 ? "{$h}h " : "") . "{$m}m";
            }

            // Status Dot Color
            $dotColor = 'bg-gray-300';
            if ($isPresent) $dotColor = 'bg-green-500';
            if ($isLate) $dotColor = 'bg-yellow-500'; 
            if ($isAbsent) $dotColor = 'bg-red-500';
            if ($isLeave || $isShortLeave) $dotColor = 'bg-blue-500';
            if ($isLeavePending) $dotColor = 'bg-yellow-500 animate-pulse'; 

            $statusTitle = ucfirst(str_replace('_', ' ', $status));
            if($isLate && $lateTimeText) $statusTitle .= " ($lateTimeText)";
        @endphp

        <div class="bg-white w-full shadow-lg rounded-2xl p-4 space-y-4 border border-gray-100 relative overflow-hidden">
            
            {{-- Card Header --}}
            <div class="flex items-center justify-between z-10 relative">
                <div class="flex items-center gap-3">
                     @if($teacher->user->user_pic ?? '')
                        <img src="{{ asset('storage/' . $teacher->user->user_pic) }}" alt="{{ $teacher->user->name }}" class="rounded-full shadow-sm w-12 h-12 object-cover border border-gray-200">
                     @endif
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ optional($teacher->user)->name ?? 'No Name' }}</h2>
                        <p class="text-xs text-gray-500">{{ optional($teacher->user)->email ?? 'No Email' }}</p>
                    </div>
                </div>
                <div class="w-4 h-4 {{ $dotColor }} rounded-full shadow-sm ring-2 ring-white" title="{{ $statusTitle }}"></div>
            </div>

            {{-- Salary Info --}}
            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Salary</span>
                <span class="text-lg font-bold text-blue-600">{{ number_format($teacher->monthly_salary ?? 0) }} <span class="text-xs text-gray-400">PKR</span></span>
            </div>

            {{-- ACTION AREA --}}
            <div class="pt-2">
                
                @if ($isToday)
                    {{-- ====== A. TODAY'S ACTIONS (Live) ====== --}}
                    
                    @if ($hasCheckedOut)
                        {{-- 1. Checked Out State --}}
                        <div class="bg-blue-50 p-3 rounded-xl text-center border border-blue-100">
                            <p class="text-blue-700 font-bold text-sm">Session Completed</p>
                            <div class="flex justify-center gap-4 mt-2 text-xs text-gray-600">
                                <div><span class="block text-gray-400 uppercase text-[10px]">In</span> {{ $attendance->check_in->format('h:i A') }}</div>
                                <div><span class="block text-gray-400 uppercase text-[10px]">Out</span> {{ $attendance->check_out->format('h:i A') }}</div>
                            </div>
                        </div>

                    @else
                        @if ($hasCheckedIn)
                            {{-- 2. Checked In State (Show Check Out) --}}
                             <button data-teacher-id="{{ $teacher->id }}" data-action="check_out"
                                class="btn-attendance-action w-full py-2.5 bg-blue-100 text-blue-700 font-bold rounded-xl hover:bg-blue-200 transition-colors flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                Check Out Now
                            </button>
                        @else
                            {{-- 3. Not Marked (Show Check In) --}}
                            @php $disableCheckIn = $isPresent || $isLate || $isLeave || $isShortLeave || $isLeavePending; @endphp
                            
                            <button data-teacher-id="{{ $teacher->id }}" data-action="check_in"
                                class="btn-attendance-action w-full py-3 rounded-xl font-bold shadow-sm transition-all transform hover:-translate-y-0.5
                                {{ $disableCheckIn ? 'bg-green-500 text-white opacity-70 cursor-not-allowed' : 'bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700' }}"
                                {{ $disableCheckIn ? 'disabled' : '' }}>
                                {{ $disableCheckIn ? ($isLate ? 'Marked Late' : 'Checked In') : 'Check In' }}
                            </button>
                        @endif

                        {{-- Secondary Actions (Absent / Leaves) --}}
                        <div class="grid grid-cols-3 gap-2 mt-3">
                            <button data-teacher-id="{{ $teacher->id }}" data-action="absent" 
                                class="btn-attendance-action bg-red-50 text-red-600 py-2 rounded-lg text-xs font-bold hover:bg-red-100 border border-red-100">
                                Absent
                            </button>
                            <button data-teacher-id="{{ $teacher->id }}" data-action="short_leave" data-teacher-name="{{ optional($teacher->user)->name }}" 
                                class="btn-leave-modal bg-purple-50 text-purple-600 py-2 rounded-lg text-xs font-bold hover:bg-purple-100 border border-purple-100">
                                Short Lv
                            </button>
                            <button data-teacher-id="{{ $teacher->id }}" data-action="leave" data-teacher-name="{{ optional($teacher->user)->name }}" 
                                class="btn-leave-modal bg-blue-50 text-blue-600 py-2 rounded-lg text-xs font-bold hover:bg-blue-100 border border-blue-100">
                                Full Lv
                            </button>
                        </div>
                    @endif

                @else
                    {{-- ====== B. PAST DATE ACTIONS (Manual Updates) ====== --}}
                    
                    <div class="grid grid-cols-2 gap-2">
                         <button data-teacher-id="{{ $teacher->id }}" data-action="present" class="btn-past-action bg-green-50 text-green-700 py-2 rounded-lg text-xs font-bold hover:bg-green-100 border border-green-100">Present</button>
                         <button data-teacher-id="{{ $teacher->id }}" data-action="absent" class="btn-past-action bg-red-50 text-red-700 py-2 rounded-lg text-xs font-bold hover:bg-red-100 border border-red-100">Absent</button>
                    </div>
                    
                    {{-- Past Manual Late & Leave --}}
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        {{-- Manual Late Button -> Opens Modal --}}
                        <button data-teacher-id="{{ $teacher->id }}" data-teacher-name="{{ optional($teacher->user)->name }}" 
                            class="btn-late-modal bg-yellow-50 text-yellow-700 py-2 rounded-lg text-xs font-bold hover:bg-yellow-100 border border-yellow-100 flex items-center justify-center gap-1">
                            Late @if($isLate && $lateTimeText)<span class="bg-white px-1 rounded shadow-sm text-[10px]">{{ $lateTimeText }}</span>@endif
                        </button>
                        
                        <button data-teacher-id="{{ $teacher->id }}" data-action="leave" data-teacher-name="{{ optional($teacher->user)->name }}" 
                            class="btn-past-leave-modal bg-blue-50 text-blue-700 py-2 rounded-lg text-xs font-bold hover:bg-blue-100 border border-blue-100">
                            {{ $isLeave ? 'On Leave' : 'Leave' }}
                        </button>
                    </div>

                     @if ($attendance && $attendance->notes) 
                        <p class="text-[10px] text-gray-500 mt-2 bg-gray-50 p-1.5 rounded border border-gray-100">
                            <strong>Note:</strong> {{Str::limit($attendance->notes, 40)}}
                        </p> 
                    @endif
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- ========================================================= --}}
{{-- 3. MODALS (Hidden by Default) --}}
{{-- ========================================================= --}}

{{-- A. LEAVE MODAL --}}
<div id="leave-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md transform transition-all scale-100">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-bold text-gray-800">Apply Leave</h3>
            <button onclick="document.getElementById('leave-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="leave-modal-form" data-form-type="today">
            <input type="hidden" id="modal_teacher_id" name="teacher_id">
            <input type="hidden" id="modal_leave_type" name="leave_type">
            
            <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                <p class="text-sm text-gray-600">Applicant: <span id="modal-teacher-name" class="font-bold text-gray-800"></span></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Reason</label>
                <textarea name="reason" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" placeholder="Enter valid reason..." required></textarea>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('leave-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-medium">Submit Request</button>
            </div>
        </form>
    </div>
</div>

{{-- B. MANUAL LATE MODAL (For Past Dates Only) --}}
<div id="late-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Manual Late Entry</h3>
        <form id="late-modal-form">
            <input type="hidden" id="late_modal_teacher_id" name="teacher_id">
            
            <p class="text-sm text-gray-600 mb-4">Editing for: <span id="late-modal-teacher-name" class="font-bold"></span></p>
            
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Minutes Late</label>
            <input type="number" id="late_minutes" name="late_minutes" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-lg p-2" placeholder="e.g. 15" min="1" required>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('late-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 shadow-md">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // =========================================================
    // 1. SAVE TIME SETTINGS (Top Black Button)
    // =========================================================
    const saveBtn = document.getElementById('btn-save-time');
    if(saveBtn) {
        saveBtn.addEventListener('click', function() {
            const startTime = document.getElementById('school_start_time').value;
            const endTime = document.getElementById('school_end_time').value;

            if(!startTime || !endTime) { alert('Please select both Start and End time.'); return; }

            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = 'Saving...'; saveBtn.disabled = true;

            fetch("{{ route('attendence.save_settings') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ start_time: startTime, end_time: endTime })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) alert(data.message);
                else alert('Error saving settings.');
            })
            .catch(err => alert('Error occurred.'))
            .finally(() => { saveBtn.innerHTML = originalText; saveBtn.disabled = false; });
        });
    }

    // =========================================================
    // 2. LIVE ACTIONS (Check In/Out, Absent)
    // =========================================================
    document.body.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-attendance-action');
        if (button) {
            const teacherId = button.dataset.teacherId;
            const action = button.dataset.action;
            
            // Get Current Values from Inputs for Auto-Calc
            const startTime = document.getElementById('school_start_time').value;
            const endTime = document.getElementById('school_end_time').value;

            if (!teacherId || !action) return;
            button.disabled = true; button.innerHTML = 'Processing...';

            fetch("{{ route('attendence.teacher.mark') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ 
                    teacher_id: teacherId, 
                    action: action,
                    school_start_time: startTime, // Send dynamic time
                    school_end_time: endTime
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) location.reload();
                else { alert(data.error || 'Something went wrong'); button.disabled = false; button.innerHTML = 'Try Again'; }
            })
            .catch(err => { console.error(err); location.reload(); });
        }
    });

    // =========================================================
    // 3. PAST ACTIONS (Present/Absent Buttons)
    // =========================================================
    document.body.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-past-action');
        if (button) {
            const teacherId = button.dataset.teacherId;
            const action = button.dataset.action;
            const selectedDate = document.getElementById('date').value;

            if (!teacherId || !action) return;
            button.disabled = true; button.innerHTML = '...';

            fetch("{{ route('attendence.teacher.update_past') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ teacher_id: teacherId, action: action, date: selectedDate })
            }).then(() => location.reload());
        }
    });

    // =========================================================
    // 4. LEAVE MODAL LOGIC (Shared for Today & Past)
    // =========================================================
    const leaveModal = document.getElementById('leave-modal');
    const leaveForm = document.getElementById('leave-modal-form');
    
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-leave-modal, .btn-past-leave-modal');
        if(btn && !btn.disabled) {
            document.getElementById('modal_teacher_id').value = btn.dataset.teacherId;
            document.getElementById('modal_leave_type').value = btn.dataset.action;
            document.getElementById('modal-teacher-name').textContent = btn.dataset.teacherName;
            
            // Distinguish Today vs Past based on class
            if(btn.classList.contains('btn-past-leave-modal')) {
                leaveForm.dataset.formType = 'past';
            } else {
                leaveForm.dataset.formType = 'today';
            }
            leaveModal.classList.remove('hidden');
        }
    });

    leaveForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(leaveForm);
        const selectedDate = document.getElementById('date').value;
        let url = "{{ route('attendence.teacher.leave') }}";

        // Logic switch for Past dates
        if(leaveForm.dataset.formType === 'past') {
            url = "{{ route('attendence.teacher.update_past') }}";
            formData.append('date', selectedDate);
            formData.append('action', document.getElementById('modal_leave_type').value);
        }

        fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData })
        .then(res => res.json())
        .then(data => { location.reload(); });
    });

    // =========================================================
    // 5. MANUAL LATE MODAL (Past Dates Only)
    // =========================================================
    const lateModal = document.getElementById('late-modal');
    const lateForm = document.getElementById('late-modal-form');

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-late-modal');
        if(btn) {
            document.getElementById('late_modal_teacher_id').value = btn.dataset.teacherId;
            document.getElementById('late-modal-teacher-name').textContent = btn.dataset.teacherName;
            document.getElementById('late_minutes').value = '';
            lateModal.classList.remove('hidden');
        }
    });

    lateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const minutes = document.getElementById('late_minutes').value;
        const teacherId = document.getElementById('late_modal_teacher_id').value;
        const selectedDate = document.getElementById('date').value;

        fetch("{{ route('attendence.teacher.update_past') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ 
                teacher_id: teacherId, 
                action: 'late_arrival', 
                date: selectedDate,
                late_minutes: minutes 
            })
        }).then(() => location.reload());
    });
});
</script>
@endpush