@extends('layouts.admin')

@section('title', 'Mark Student Attendance')
@section('page-title', 'Mark Attendance')

@push('styles')
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Animated Radio Buttons */
        .attendance-radio {
            display: none;
        }

        .attendance-label {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s ease-in-out;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            color: #64748b;
        }

        .attendance-label:hover {
            border-color: #cbd5e1;
            background-color: #f8fafc;
        }

        .attendance-radio:checked+.attendance-label {
            border-color: transparent;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Color Variants for statuses */
        .attendance-radio[value="present"]:checked+.attendance-label {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .attendance-radio[value="absent"]:checked+.attendance-label {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .attendance-radio[value="late"]:checked+.attendance-label {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .attendance-radio[value="leave"]:checked+.attendance-label {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }
    </style>
@endpush

@section('content')

    {{-- Alerts Section --}}
    <div class="max-w-7xl mx-auto mb-6">
        @if (session('success'))
            <div
                class="flex items-center gap-3 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-lg shadow-sm animate-bounce-in">
                <div class="bg-emerald-100 p-2 rounded-full">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-sm">Success</p>
                    <p class="text-sm opacity-90">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-lg shadow-sm">
                <div class="bg-rose-100 p-2 rounded-full">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-sm">Error</p>
                    <p class="text-sm opacity-90">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Filter Card --}}
    <div class="max-w-7xl mx-auto">
        <div
            class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 p-6 mb-8 relative overflow-hidden">
            {{-- Decorative Background --}}
            <div
                class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-gradient-to-br from-indigo-50 to-blue-50 blur-3xl opacity-60 pointer-events-none">
            </div>

            <form action="{{ route('attendance.create') }}" method="GET">
                <div class="flex flex-col lg:flex-row items-end gap-6 relative z-10">

                    {{-- Title Section --}}
                    <div class="w-full lg:w-1/4">
                        <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                            Attendance
                        </h2>
                        <p class="text-slate-500 text-sm mt-1 ml-1">Manage student daily records</p>
                    </div>

                    {{-- Inputs Section --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-5 w-full">
                        <div>
                            <label for="school_class_id"
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Select
                                Class</label>
                            <div class="relative">
                                <select id="school_class_id" name="school_class_id" required
                                    class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm py-2.5 pl-3 pr-10 appearance-none">
                                    <option value="">Choose a Class...</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ (isset($selectedClassId) && $selectedClassId == $class->id) ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="attendance_date"
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Date</label>
                            <input type="date" id="attendance_date" name="attendance_date" required
                                value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                                class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm py-2.5">
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="w-full lg:w-auto">
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Fetch Students
                        </button>
                    </div>
                </div>

                {{-- Footer Links --}}
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-4 text-sm font-medium">
                    <a href="{{ route('attendance.report') }}"
                        class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1 group">
                        <span class="bg-indigo-50 p-1 rounded-md group-hover:bg-indigo-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0h6m-6 0H5m6 0v-6m6 6v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0h6" />
                            </svg>
                        </span>
                        View Monthly Report
                    </a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('admin.student.leaves.pending', ['school_class_id' => $selectedClassId ?? '', 'attendance_date' => $selectedDate ?? '']) }}"
                        class="text-amber-600 hover:text-amber-800 transition-colors flex items-center gap-2 bg-amber-50 px-3 py-1.5 rounded-full border border-amber-100 hover:border-amber-200">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                        </span>
                        Pending Leaves
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Student List Section --}}
    @if(isset($students))
        @if($students->isEmpty())
            @if($selectedClassId)
                <div class="max-w-lg mx-auto text-center py-16">
                    <div class="bg-slate-50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <svg class="w-12 h-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">No Students Found</h3>
                    <p class="text-slate-500 mt-2">There are no active students enrolled in this class.</p>
                </div>
            @endif
        @else
            <form action="{{ route('attendance.store') }}" method="POST" class="pb-24">
                @csrf
                <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                <div class="max-w-7xl mx-auto">

                    {{-- List Header --}}
                    <div
                        class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex items-center justify-center bg-indigo-100 text-indigo-600 w-10 h-10 rounded-lg font-bold">
                                {{ substr($students->first()->schoolClass->name, 0, 2) }}
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">{{ $students->first()->schoolClass->name }}</h3>
                                <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($selectedDate)->format('l, d M Y') }}</p>
                            </div>
                        </div>
                        <button type="button" id="markAllPresent"
                            class="group flex items-center gap-2 bg-emerald-50 border border-emerald-100 text-emerald-700 hover:bg-emerald-100 hover:border-emerald-200 px-5 py-2.5 rounded-xl transition-all text-sm font-semibold shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 text-emerald-600 group-hover:scale-110 transition-transform" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                            Mark All Present
                        </button>
                    </div>

                    {{-- Students Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach ($students as $student)
                            @php
                                $existingAttendance = $student->attendances->first();
                                $status = $existingAttendance->status ?? 'present';

                                $leaveRequest = \App\Models\StudentLeaveRequest::where('student_id', $student->id)
                                    ->whereDate('start_date', '<=', $selectedDate)
                                    ->whereDate('end_date', '>=', $selectedDate)
                                    ->latest()
                                    ->first();

                                // Dynamic styling based on status
                                $cardClasses = match ($status) {
                                    'absent' => 'border-rose-200 ring-1 ring-rose-100 bg-rose-50/30',
                                    'leave' => 'border-indigo-200 ring-1 ring-indigo-100 bg-indigo-50/30',
                                    'late' => 'border-amber-200 ring-1 ring-amber-100 bg-amber-50/30',
                                    default => 'border-slate-200 bg-white hover:border-indigo-300 hover:shadow-md',
                                };
                            @endphp

                            <div class="relative group rounded-2xl p-5 border {{ $cardClasses }} transition-all duration-300">

                                {{-- Student Identity Section --}}
                                <div class="flex justify-between items-start mb-5">
                                    <div class="flex items-center gap-4">
                                        {{-- Avatar with Status Indicator --}}
                                        <div class="relative shrink-0">
                                            <img class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-slate-200"
                                                src="{{ asset('storage/' . $student->user->user_pic) }}"
                                                alt="{{ $student->user->name }}">

                                            {{-- Status Dot --}}
                                            <span
                                                class="absolute bottom-0.5 right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white
                                            {{ $status == 'present' ? 'bg-emerald-500' : ($status == 'absent' ? 'bg-rose-500' : ($status == 'leave' ? 'bg-indigo-500' : 'bg-amber-500')) }}">
                                            </span>
                                        </div>

                                        {{-- Name & ID --}}
                                        <div>
                                            <h2 class="text-base font-bold text-slate-800 leading-tight">{{ $student->user->name }}</h2>
                                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
                                                ID: <span class="text-slate-600">{{ $student->id_card_number }}</span>
                                            </p>
                                            <input type="hidden" name="attendance[{{ $loop->index }}][student_id]"
                                                value="{{ $student->id }}">

                                            {{-- Leave Status Badge --}}
                                            @if ($leaveRequest)
                                                <div class="mt-1.5">
                                                    @if ($leaveRequest->status == 'approved')
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            APPROVED
                                                        </span>
                                                    @elseif($leaveRequest->status == 'rejected')
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            REJECTED
                                                        </span>
                                                    @elseif($leaveRequest->status == 'pending')
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            PENDING
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Apply Leave Icon Button --}}
                                    <button type="button"
                                        class="btn-student-leave-modal text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 p-2 rounded-xl transition-all duration-200"
                                        data-student-id="{{ $student->id }}" data-student-name="{{ $student->user->name }}"
                                        title="Apply Leave">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Attendance Controls --}}
                                <div class="space-y-4">

                                    {{-- Radio Buttons --}}
                                    <div class="grid grid-cols-4 gap-2">
                                        {{-- Present --}}
                                        <div>
                                            <input type="radio" id="present_{{ $student->id }}"
                                                name="attendance[{{ $loop->index }}][status]" value="present"
                                                class="attendance-radio peer hidden" {{ $status == 'present' ? 'checked' : '' }}>
                                            <label for="present_{{ $student->id }}"
                                                class="attendance-label h-10 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 text-xs font-bold cursor-pointer hover:bg-slate-50 transition-all peer-checked:bg-emerald-500 peer-checked:text-green-400 peer-checked:border-emerald-600 peer-checked:shadow-md shadow-sm">
                                                Present
                                            </label>
                                        </div>

                                        {{-- Absent --}}
                                        <div>
                                            <input type="radio" id="absent_{{ $student->id }}"
                                                name="attendance[{{ $loop->index }}][status]" value="absent"
                                                class="attendance-radio peer hidden" {{ $status == 'absent' ? 'checked' : '' }}>
                                            <label for="absent_{{ $student->id }}"
                                                class="attendance-label h-10 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 text-xs font-bold cursor-pointer hover:bg-slate-50 transition-all peer-checked:bg-rose-500 peer-checked:text-red-500 peer-checked:border-rose-600 peer-checked:shadow-md shadow-sm">
                                                Absent
                                            </label>
                                        </div>

                                        {{-- Late --}}
                                        <div>
                                            <input type="radio" id="late_{{ $student->id }}"
                                                name="attendance[{{ $loop->index }}][status]" value="late"
                                                class="attendance-radio peer hidden" {{ $status == 'late' ? 'checked' : '' }}>
                                            <label for="late_{{ $student->id }}"
                                                class="attendance-label h-10 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 text-xs font-bold cursor-pointer hover:bg-slate-50 transition-all peer-checked:bg-amber-500 peer-checked:text-blue-400 peer-checked:border-amber-600 peer-checked:shadow-md shadow-sm">
                                                Late
                                            </label>
                                        </div>

                                        {{-- Leave --}}
                                        <div>
                                            <input type="radio" id="leave_{{ $student->id }}"
                                                name="attendance[{{ $loop->index }}][status]" value="leave"
                                                class="attendance-radio peer hidden" {{ $status == 'leave' ? 'checked' : '' }}>
                                            <label for="leave_{{ $student->id }}"
                                                class="attendance-label h-10 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 text-xs font-bold cursor-pointer hover:bg-slate-50 transition-all peer-checked:bg-indigo-500 peer-checked:text-yellow-500 peer-checked:border-indigo-600 peer-checked:shadow-md shadow-sm">
                                                Leave
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Remarks Input --}}
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors duration-200"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="remarks_{{ $student->id }}"
                                            name="attendance[{{ $loop->index }}][remarks]"
                                            value="{{ $existingAttendance->remarks ?? '' }}" placeholder="Add remarks (optional)..."
                                            class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition-all duration-200">
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Floating Save Button --}}
                    <div class="fixed bottom-6 right-6 z-40">
                        <button type="submit"
                            class="flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold px-6 py-3.5 rounded-full shadow-2xl hover:shadow-slate-900/40 transform hover:-translate-y-1 transition-all duration-300 border border-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Attendance
                        </button>
                    </div>

                </div>
            </form>
        @endif
    @endif

@endsection

{{-- Modal (Clean & Modern) --}}
<div id="leave-modal" class="fixed inset-0 z-[60] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">

                {{-- Header --}}
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modal-title">
                        <svg class="w-5 h-5 text-indigo-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Apply Student Leave
                    </h3>
                    <button id="modal-close-btn"
                        class="text-indigo-100 hover:text-white hover:bg-white/10 rounded-full p-1 transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="leave-modal-form" action="{{ route('admin.student.leaves.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="modal_student_id" name="student_id">

                    <div class="px-6 py-6 space-y-5">
                        {{-- User Info --}}
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center gap-4">
                            <div class="bg-white p-2.5 rounded-full shadow-sm text-indigo-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Applying For</p>
                                <p id="modal-student-name" class="text-indigo-900 font-bold text-lg leading-tight"></p>
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date"
                                    class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">From</label>
                                <input type="date" id="start_date" name="start_date"
                                    value="{{ now()->format('Y-m-d') }}"
                                    class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 bg-slate-50/50"
                                    required>
                            </div>
                            <div>
                                <label for="end_date"
                                    class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">To</label>
                                <input type="date" id="end_date" name="end_date" value="{{ now()->format('Y-m-d') }}"
                                    class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 bg-slate-50/50"
                                    required>
                            </div>
                        </div>

                        {{-- Leave Type --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Leave Type</label>
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Full Day --}}
                                <label
                                    class="relative flex cursor-pointer rounded-xl border p-3 shadow-sm transition-all bg-white border-slate-200 hover:bg-slate-50 peer-checked:bg-indigo-50 peer-checked:border-indigo-600 peer-checked:ring-1 peer-checked:ring-indigo-600">
                                    <input type="radio" name="leave_type" value="full_day" class="peer sr-only" checked>
                                    <span class="flex flex-col">
                                        <span
                                            class="block text-sm font-bold text-slate-800 peer-checked:text-indigo-700">Full
                                            Day</span>
                                        <span class="text-xs text-slate-500 mt-0.5">Standard</span>
                                    </span>
                                    <svg class="ml-auto h-5 w-5 text-indigo-600 hidden peer-checked:block"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span
                                        class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-indigo-600 pointer-events-none"></span>
                                </label>

                                {{-- Short Leave --}}
                                <label
                                    class="relative flex cursor-pointer rounded-xl border p-3 shadow-sm transition-all bg-white border-slate-200 hover:bg-slate-50 peer-checked:bg-indigo-50 peer-checked:border-indigo-600 peer-checked:ring-1 peer-checked:ring-indigo-600">
                                    <input type="radio" name="leave_type" value="short_leave" class="peer sr-only">
                                    <span class="flex flex-col">
                                        <span
                                            class="block text-sm font-bold text-slate-800 peer-checked:text-indigo-700">Short
                                            Leave</span>
                                        <span class="text-xs text-slate-500 mt-0.5">Partial</span>
                                    </span>
                                    <svg class="ml-auto h-5 w-5 text-indigo-600 hidden peer-checked:block"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span
                                        class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-indigo-600 pointer-events-none"></span>
                                </label>
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div>
                            <label for="modal_reason"
                                class="block text-xs font-bold text-slate-500 uppercase mb-1.5 ml-1">Reason</label>
                            <textarea id="modal_reason" name="reason" rows="3" required
                                class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50/50 p-3"
                                placeholder="Please provide a valid reason..."></textarea>

                            {{-- Error Container --}}
                            <div id="modal-error"
                                class="hidden mt-3 bg-rose-50 border border-rose-100 rounded-lg p-3 flex gap-2 items-start">
                                <svg class="w-4 h-4 text-rose-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs text-rose-600 font-medium">Error message</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                        <button type="submit" id="modal-submit-btn"
                            class="inline-flex w-full justify-center rounded-xl border border-transparent bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto transition-all">
                            Submit Request
                        </button>
                        <button type="button" id="modal-cancel-btn"
                            class="mt-3 inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-all">
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
                    this.classList.add('scale-95');
                    setTimeout(() => this.classList.remove('scale-95'), 150);

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
            document.body.addEventListener('click', function (e) {
                const button = e.target.closest('.btn-student-leave-modal');
                if (button) {
                    if (button.disabled) return;

                    modalStudentIdInput.value = button.dataset.studentId;
                    modalStudentNameSpan.textContent = button.dataset.studentName;

                    modalError.classList.add('hidden');
                    modalForm.reset();
                    document.getElementById('start_date').value = '{{ now()->format('Y-m-d') }}';
                    document.getElementById('end_date').value = '{{ now()->format('Y-m-d') }}';

                    const firstRadio = modalForm.querySelector('input[name="leave_type"]');
                    if (firstRadio) firstRadio.checked = true;

                    modal.classList.remove('hidden');
                }
            });

            // --- 4. Modal Submit Handler ---
            modalForm.addEventListener('submit', function (e) {
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
                    .then(response => response.json().then(data => ({ ok: response.ok, data })))
                    .then(({ ok, data }) => {
                        if (ok) {
                            closeModal();
                            alert('Leave request submitted successfully!');
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
                        modalError.querySelector('span').textContent = error.message;
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

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        });
    </script>
@endpush