@extends('layouts.admin')

@section('title', 'Pending Leaves')
@section('page-title', 'Pending Leave Requests')

@section('content')

{{-- ===== NEW "GO BACK" BUTTON ADDED HERE ===== --}}
<div class="mb-4">
    {{-- This link goes back to the main teacher attendance page. --}}
    <a href="{{ route('attendence.teacher') }}" 
       class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 hover:bg-gray-300 text-sm font-semibold px-4 py-2 rounded-lg shadow-sm transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Attendance
    </a>
</div>
{{-- ===== END OF NEW BUTTON ===== --}}


<div class="bg-white shadow-lg rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full min-w-lg">
            <thead>
                <tr class="border-b-2 border-gray-200">
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Teacher</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Leave Date</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Type</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Reason</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pending_leaves as $leave)
                    <tr class="border-b border-gray-100 hover:bg-gray-50" id="row-{{ $leave->id }}">
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                @if($leave->teacher->user->user_pic ?? '')
                                    <img src="{{ asset('storage/' . $leave->teacher->user->user_pic) }}"
                                        alt="{{ $leave->teacher->user->name }}"
                                        class="rounded-full shadow-sm w-10 h-10 object-cover">
                                @endif
                                <div>
                                    <span class="font-medium text-gray-900">{{ $leave->teacher->user->name ?? 'N/A' }}</span>
                                    <p class="text-xs text-gray-500">{{ $leave->teacher->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-700">
                            {{ $leave->date->format('M d, Y') }}
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-700">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $leave->leave_type == 'short_leave' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst(str_replace('_', '-', $leave->leave_type)) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-600 max-w-xs">
                            {{ $leave->notes }}
                        </td>
                        <td class="py-4 px-4 text-center space-x-2">
                            <button
                                data-id="{{ $leave->id }}"
                                data-action="approve"
                                class="btn-action bg-green-100 text-green-700 hover:bg-green-200 text-sm font-semibold px-3 py-1.5 rounded-lg shadow-sm transition-all">
                                Approve
                            </button>
                            <button
                                data-id="{{ $leave->id }}"
                                data-action="reject"
                                class="btn-action bg-red-100 text-red-700 hover:bg-red-200 text-sm font-semibold px-3 py-1.5 rounded-lg shadow-sm transition-all">
                                Reject
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-500">
                            No pending leave requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.body.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-action');
        if (button) {
            const attendanceId = button.dataset.id;
            const action = button.dataset.action;

            if (!confirm(`Are you sure you want to ${action} this leave?`)) {
                return;
            }

            button.disabled = true;
            button.textContent = 'Processing...';

            fetch("{{ route('attendence.teacher.action_on_leave') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    attendance_id: attendanceId,
                    action: action
                })
            })
            .then(response => response.json().then(data => ({ok: response.ok, data})))
            .then(({ok, data}) => {
                if (ok) {
                    alert(data.message || 'Action successful!');
                    // Remove the row from the table
                    document.getElementById('row-' + attendanceId).remove();
                } else {
                    throw new Error(data.error || 'Request failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                button.disabled = false;
                button.textContent = action.charAt(0).toUpperCase() + action.slice(1);
            });
        }
    });
});
</script>
@endpush