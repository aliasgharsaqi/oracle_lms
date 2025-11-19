@extends('layouts.admin')

@section('title', 'Mark Student Attendance')
@section('page-title', 'Mark Attendance')

@push('styles')
    <style>
        /* Custom Scrollbar for a cleaner look */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Attendance Radio Styling */
        .attendance-radio-group {
            display: flex;
            justify-content: space-between; /* Spread them out evenly */
            gap: 0.25rem;
            background: #f8fafc;
            padding: 0.35rem;
            border-radius: 1rem;
        }

        .attendance-radio { display: none; }

        .attendance-label {
            cursor: pointer;
            flex: 1;
            padding: 0.5rem 0;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #64748b;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }

        .attendance-label:hover {
            background: #e2e8f0;
            color: #475569;
        }

        /* Checked States with glowing effects */
        .attendance-radio:checked + .attendance-label {
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .attendance-radio[value="present"]:checked + .attendance-label {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .attendance-radio[value="absent"]:checked + .attendance-label {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .attendance-radio[value="late"]:checked + .attendance-label {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .attendance-radio[value="leave"]:checked + .attendance-label {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        }
    </style>
@endpush

@section('content')

    {{-- Alerts --}}
    <div class="max-w-7xl mx-auto mb-6">
        @if (session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-2xl shadow-sm animate-fade-in-down">
                <div class="bg-emerald-100 p-2 rounded-full">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                </div>
                <div>
                    <p class="font-bold text-sm">Success</p>
                    <p class="text-sm opacity-90">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 bg-rose-50 border border-rose-100 text-rose-700 p-4 rounded-2xl shadow-sm animate-fade-in-down">
                 <div class="bg-rose-100 p-2 rounded-full">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </div>
                <div>
                    <p class="font-bold text-sm">Error</p>
                    <p class="text-sm opacity-90">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Filter Section --}}
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-100/50 border border-gray-100 p-6 mb-8 relative overflow-hidden">
            {{-- Decorative background blob --}}
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-indigo-50 blur-3xl opacity-50 pointer-events-none"></div>

            <form action="{{ route('attendance.create') }}" method="GET">
                <div class="flex flex-col lg:flex-row items-end gap-6 relative z-10">
                    
                    {{-- Header --}}
                    <div class="w-full lg:w-1/4">
                        <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <span>📅</span> Mark Attendance
                        </h2>
                        <p class="text-slate-500 text-sm mt-1">Select class and date to proceed.</p>
                    </div>

                    {{-- Inputs --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                        <div class="group">
                            <label for="school_class_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Class</label>
                            <select id="school_class_id" name="school_class_id" required
                                class="w-full bg-slate-50 border-slate-200 text-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm py-2.5">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ (isset($selectedClassId) && $selectedClassId == $class->id) ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="attendance_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Date</label>
                            <input type="date" id="attendance_date" name="attendance_date" required
                                value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                                class="w-full bg-slate-50 border-slate-200 text-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm py-2.5">
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="w-full lg:w-auto flex gap-3">
                        <button type="submit"
                            class="flex-1 lg:flex-none bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            Fetch
                        </button>
                    </div>
                </div>

                {{-- Quick Links Footer --}}
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-4 text-sm font-medium">
                    <a href="{{ route('attendance.report') }}" class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                        View Monthly Report <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('admin.student.leaves.pending', ['school_class_id' => $selectedClassId ?? '', 'attendance_date' => $selectedDate ?? '']) }}" 
                       class="text-amber-600 hover:text-amber-800 transition-colors flex items-center gap-1 bg-amber-50 px-3 py-1 rounded-full border border-amber-100">
                        <span class="relative flex h-2 w-2 mr-1">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        View Pending Leaves
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Student List Section --}}
    @if(isset($students))
        @if($students->isEmpty())
            @if($selectedClassId)
                <div class="max-w-md mx-auto text-center py-12">
                    <div class="bg-slate-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">No Students Found</h3>
                    <p class="text-slate-500 mt-1">There are no active students in this class.</p>
                </div>
            @endif
        @else
            <form action="{{ route('attendance.store') }}" method="POST" class="pb-20">
                @csrf
                <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                <div class="max-w-7xl mx-auto">
                    
                    {{-- List Header --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-700 text-sm py-1 px-3 rounded-lg">{{ $students->first()->schoolClass->name }}</span>
                                <span class="text-slate-400 font-light">|</span>
                                <span class="text-slate-600">{{ \Carbon\Carbon::parse($selectedDate)->format('d M, Y') }}</span>
                            </h3>
                        </div>
                        <button type="button" id="markAllPresent"
                            class="group flex items-center gap-2 bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 px-5 py-2 rounded-xl shadow-sm hover:shadow-md transition-all text-sm font-semibold">
                            <div class="bg-emerald-100 p-1 rounded-md group-hover:bg-emerald-200 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            Mark All Present
                        </button>
                    </div>

                    {{-- Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($students as $student)
                            @php
                                $existingAttendance = $student->attendances->first();
                                $status = $existingAttendance->status ?? 'present';
                                
                                // Fetch leave request for this specific date
                                $leaveRequest = \App\Models\StudentLeaveRequest::where('student_id', $student->id)
                                                ->whereDate('start_date', '<=', $selectedDate)
                                                ->whereDate('end_date', '>=', $selectedDate)
                                                ->latest()
                                                ->first();
                            @endphp

                            <div class="group bg-white rounded-3xl p-5 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_25px_-5px_rgba(0,0,0,0.1),0_10px_10px_-5px_rgba(0,0,0,0.04)] border border-slate-100 transition-all duration-300 relative overflow-hidden">
                                {{-- Top Accent --}}
                                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                                {{-- Student Info --}}
                                <div class="flex items-start gap-4 mb-5">
                                    <div class="relative">
                                        <img class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-md"
                                            src="{{ asset('storage/' . $student->user->user_pic) }}"
                                            alt="{{ $student->user->name }}">
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-800 leading-tight mb-0.5">{{ $student->user->name }}</h2>
                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">ID: {{ $student->id_card_number }}</p>
                                        <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                        
                                        {{-- NEW: Leave Status Badge --}}
                                        @if($leaveRequest)
                                            <div class="mt-2">
                                                @if($leaveRequest->status == 'approved')
                                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                                        Leave Approved
                                                    </span>
                                                @elseif($leaveRequest->status == 'rejected')
                                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        Leave Rejected
                                                    </span>
                                                @elseif($leaveRequest->status == 'pending')
                                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        Pending Approval
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        {{-- END NEW --}}
                                    </div>
                                </div>

                                {{-- Controls --}}
                                <div class="space-y-4">
                                    {{-- Radio Group --}}
                                    <div>
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
                                    </div>

                                    {{-- Leave Button --}}
                                    <button type="button"
                                        class="btn-student-leave-modal w-full flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl border border-dashed border-amber-300 text-amber-600 hover:bg-amber-50 transition-colors"
                                        data-student-id="{{ $student->id }}"
                                        data-student-name="{{ $student->user->name }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        Apply Official Leave
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div> 

                </div>
            </form>
        @endif
    @endif

@endsection

{{-- Modal (Restyled with Active Highlight) --}}
<div id="leave-modal" class="fixed inset-0 z-[60] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                
                <div class="bg-indigo-600 px-4 py-4 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-white flex items-center gap-2" id="modal-title">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Apply Student Leave
                    </h3>
                    <button id="modal-close-btn" class="text-indigo-100 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form id="leave-modal-form" action="{{ route('admin.student.leaves.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="modal_student_id" name="student_id">
                    
                    <div class="px-4 py-6 sm:px-6 space-y-5">
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 flex items-center gap-3">
                             <div class="bg-indigo-100 p-2 rounded-full text-indigo-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                             </div>
                             <div>
                                 <p class="text-xs text-indigo-500 font-bold uppercase tracking-wide">Applying For</p>
                                 <p id="modal-student-name" class="text-indigo-900 font-bold text-base"></p>
                             </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-xs font-bold text-slate-500 uppercase mb-1">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ now()->format('Y-m-d') }}"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5" required>
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs font-bold text-slate-500 uppercase mb-1">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ now()->format('Y-m-d') }}"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5" required>
                            </div>
                        </div>

                        {{-- UPDATED LEAVE TYPE SECTION --}}
                      <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Leave Type</label>
                        <div class="grid grid-cols-2 gap-4">

                            {{-- Full Day --}}
                            <label class="relative flex cursor-pointer rounded-xl border p-3 shadow-sm transition-all bg-white border-slate-200 hover:bg-slate-50
                                peer-checked:bg-indigo-50 peer-checked:border-indigo-600 peer-checked:ring-2 peer-checked:ring-indigo-600">

                                <input type="radio" name="leave_type" value="full_day"
                                    class="peer sr-only" checked>

                                <span class="flex items-center">
                                    <span class="flex flex-col text-sm">
                                        <span class="font-bold text-slate-900">Full Day</span>
                                        <span class="text-slate-500 text-xs">Standard leave</span>
                                    </span>
                                </span>

                                <svg class="ml-auto h-5 w-5 text-indigo-600 hidden peer-checked:block"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>

                            {{-- Short Leave --}}
                            <label class="relative flex cursor-pointer rounded-xl border p-3 shadow-sm transition-all bg-white border-slate-200 hover:bg-slate-50
                                peer-checked:bg-indigo-50 peer-checked:border-indigo-600 peer-checked:ring-2 peer-checked:ring-indigo-600">

                                <input type="radio" name="leave_type" value="short_leave"
                                    class="peer sr-only">

                                <span class="flex items-center">
                                    <span class="flex flex-col text-sm">
                                        <span class="font-bold text-slate-900">Short Leave</span>
                                        <span class="text-slate-500 text-xs">Partial day</span>
                                    </span>
                                </span>

                                <svg class="ml-auto h-5 w-5 text-indigo-600 hidden peer-checked:block"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>

                        </div>
                      </div>


                        <div>
                            <label for="modal_reason" class="block text-xs font-bold text-slate-500 uppercase mb-1">Reason</label>
                            <textarea id="modal_reason" name="reason" rows="3" required
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Explain the reason for leave..."></textarea>
                            <p id="modal-error" class="text-rose-500 text-sm mt-2 hidden bg-rose-50 p-2 rounded-lg"></p>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" id="modal-submit-btn" class="inline-flex w-full justify-center rounded-xl border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Submit Request
                        </button>
                        <button type="button" id="modal-cancel-btn" class="mt-3 inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-base font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';

            // --- 1. Mark All Present script ---
            const markAllPresentButton = document.getElementById('markAllPresent');
            if (markAllPresentButton) {
                markAllPresentButton.addEventListener('click', function () {
                    document.querySelectorAll('input[type="radio"][value="present"]').forEach(radio => {
                        radio.checked = true;
                    });
                });
            }

            // --- 2. Modal Setup ---
            const modal = document.getElementById('leave-modal');
            const modalForm = document.getElementById('leave-modal-form');
            const modalCloseBtn = document.getElementById('modal-close-btn');
            const modalCancelBtn = document.getElementById('modal-cancel-btn');
            const modalSubmitBtn = document.getElementById('modal-submit-btn');
            const modalError = document.getElementById('modal-error');
            const modalStudentIdInput = document.getElementById('modal_student_id');
            const modalStudentNameSpan = document.getElementById('modal-student-name');
            
            // --- 3. Modal Trigger ---
            document.body.addEventListener('click', function(e) {
                const button = e.target.closest('.btn-student-leave-modal');
                if (button) {
                    if (button.disabled) return; 

                    modalStudentIdInput.value = button.dataset.studentId;
                    modalStudentNameSpan.textContent = button.dataset.studentName;
                    
                    modalError.classList.add('hidden');
                    modalForm.reset(); 
                    document.getElementById('start_date').value = '{{ now()->format('Y-m-d') }}';
                    document.getElementById('end_date').value = '{{ now()->format('Y-m-d') }}';
                    
                    // Since we changed radio buttons for leave type, select the first one manually
                    const firstRadio = modalForm.querySelector('input[name="leave_type"]');
                    if(firstRadio) firstRadio.checked = true;

                    modal.classList.remove('hidden'); 
                }
            });

            // --- 4. Modal Submit Handler ---
            modalForm.addEventListener('submit', function(e) {
                e.preventDefault();
                modalSubmitBtn.disabled = true;
                modalSubmitBtn.textContent = 'Submitting...';
                modalError.classList.add('hidden');
                
                const formData = new FormData(modalForm);
                const url = modalForm.getAttribute('action');

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
                        // Success alert and redirect logic
                        alert('Leave request submitted successfully! Redirecting to pending leaves...');
                        
                        const classId = document.getElementById('school_class_id').value;
                        const date = document.getElementById('attendance_date').value;
                        const pendingUrl = "{{ route('admin.student.leaves.pending') }}";
                        
                        window.location.href = `${pendingUrl}?school_class_id=${classId}&attendance_date=${date}`;

                    } else {
                        if (data.errors) {
                            let errorMsg = Object.values(data.errors).flat().join('\n');
                            throw new Error(errorMsg);
                        }
                        throw new Error(data.message || 'An error occurred.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalError.textContent = error.message;
                    modalError.classList.remove('hidden');
                })
                .finally(() => {
                    modalSubmitBtn.disabled = false;
                    modalSubmitBtn.textContent = 'Submit Request';
                });
            });

            // --- 5. Modal Close Helpers ---
            function closeModal() {
                modal.classList.add('hidden');
            }
            modalCloseBtn.addEventListener('click', closeModal);
            modalCancelBtn.addEventListener('click', closeModal);
        });
    </script>
@endpush