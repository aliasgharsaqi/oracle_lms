{{-- resources/views/student/leave/create.blade.php --}}
@extends('layouts.student_dashboard')

@section('content')
<form action="{{ route('leave.store') }}" method="POST">
    @csrf

    {{-- 1.1 & 1.3: Multi-date picker (past & future) --}}
    <div>
        <label for="dates">Select Date(s)</label>
        {{-- Is input ko Flatpickr se attach karein --}}
        <input type="text" id="dates" name="dates" required 
               placeholder="Select one or more dates">
    </div>

    {{-- 1.2: Leave Type --}}
    <div>
        <label for="leave_type">Leave Type</label>
        <select name="leave_type" id="leave_type">
            <option value="full_day">Full Day Leave</option>
            <option value="short_leave">Short Leave</option>
        </select>
    </div>

    <div>
        <label for="reason">Reason</label>
        <textarea name="reason" id="reason" rows="4"></textarea>
    </div>

    <button type="submit">Apply for Leave</button>
</form>
@endsection

@push('scripts')
{{-- Flatpickr CDN (example) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Flatpickr ko initialize karein
        flatpickr("#dates", {
            mode: "multiple", // (Req 1.1)
            dateFormat: "Y-m-d",
            // enable: [{ from: "today", to: "2025-12-31" }] // Sirf future ke liye
            // Agar past/future dono chahiye (Req 1.3), 'enable' option ko remove kardein
        });
    });
</script>
@endpush