@extends('layouts.admin')

@section('title', 'Pending Student Leaves')
@section('page-title', 'Pending Student Leaves')

@section('content')

    <div class="max-w-7xl mx-auto pb-12">
        {{-- Header & Back Button --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">
                    <span class="mr-2">⏳</span>Pending Approvals
                </h2>
                <p class="text-slate-500 text-sm mt-1">Review and manage student leave requests.</p>
            </div>
            
            <a href="{{ route('attendance.create', ['school_class_id' => request('school_class_id'), 'attendance_date' => request('attendance_date')]) }}"
               class="inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Attendance
            </a>
        </div>

        {{-- Main Content --}}
        @if($pending_leaves->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-12 text-center max-w-2xl mx-auto">
                <div class="bg-emerald-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-2">All Caught Up!</h3>
                <p class="text-slate-500">There are no pending leave requests requiring your attention right now.</p>
            </div>
        @else
            {{-- Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pending_leaves as $request)
                    <div class="group bg-white rounded-3xl p-5 shadow-sm hover:shadow-xl border border-slate-100 transition-all duration-300 flex flex-col h-full" id="request-row-{{ $request->id }}">
                        
                        {{-- Header: Student Info --}}
                        <div class="flex items-start gap-4 mb-4">
                            <img src="{{ asset('storage/' . $request->student->user->user_pic) }}" 
                                 alt="{{ $request->student->user->name }}"
                                 class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-md">
                            <div>
                                <h4 class="font-bold text-lg text-slate-800 leading-tight">{{ $request->student->user->name }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 mt-1">
                                    {{ $request->student->schoolClass->name }}
                                </span>
                            </div>
                        </div>

                        {{-- Body: Details --}}
                        <div class="flex-1 space-y-4">
                            {{-- Date & Type Row --}}
                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center text-sm">
                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="font-semibold">
                                        {{ $request->start_date->format('d M') }} 
                                        @if($request->start_date != $request->end_date)
                                            - {{ $request->end_date->format('d M') }}
                                        @endif
                                    </span>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold uppercase tracking-wide
                                    {{ $request->leave_type == 'full_day' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ str_replace('_', ' ', $request->leave_type) }}
                                </span>
                            </div>

                            {{-- Reason Box --}}
                            <div class="relative">
                                <div class="absolute top-3 -left-1 w-2 h-2 bg-slate-100 transform rotate-45"></div>
                                <div class="bg-slate-100 rounded-lg rounded-tl-none p-3 text-sm text-slate-600 italic border-l-4 border-indigo-400">
                                    "{{ $request->reason }}"
                                </div>
                            </div>
                        </div>

                        {{-- Footer: Actions --}}
                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <button onclick="handleLeaveAction({{ $request->id }}, 'approve')"
                                class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl font-semibold text-sm bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100 hover:border-emerald-200 hover:shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                Approve
                            </button>
                            
                            <button onclick="handleLeaveAction({{ $request->id }}, 'reject')"
                                class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl font-semibold text-sm bg-rose-50 text-rose-700 border border-rose-100 hover:bg-rose-100 hover:border-rose-200 hover:shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                Reject
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    function handleLeaveAction(requestId, action) {
        // Visual confirmation text color based on action
        const actionColor = action === 'approve' ? 'green' : 'red';
        
        if (!confirm(`Are you sure you want to ${action.toUpperCase()} this leave request?`)) {
            return;
        }

        // Show loading state on the card
        const row = document.getElementById(`request-row-${requestId}`);
        if(row) row.style.opacity = '0.5';

        const url = "{{ route('admin.student.leaves.action') }}";
        const token = "{{ csrf_token() }}";

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                request_id: requestId,
                action: action,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (row) {
                    // Transformation for smooth removal
                    row.style.transform = 'scale(0.95)';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        // Check if empty and show empty state if needed (optional enhancement)
                        if(document.querySelectorAll('[id^="request-row-"]').length === 0) {
                            location.reload(); // Simple way to show empty state
                        }
                    }, 300);
                }
                // Optional: Replace alert with a toast notification library if you have one
                // alert('Action successful: ' + data.message); 
            } else {
                if(row) row.style.opacity = '1'; // Reset opacity on failure
                alert('An error occurred: ' + (data.message || 'Please try again.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if(row) row.style.opacity = '1';
            alert('A network error occurred. Please try again.');
        });
    }
</script>
@endpush