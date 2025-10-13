@extends('layouts.admin')

@section('title', 'Weekly Schedule')
@section('page-title', 'Class Schedules')

@section('content')
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div class="custom-card-header flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 px-4 py-3 border-b">
            <h4 class="text-lg font-bold text-primary-custom flex items-center gap-2">
                <i class="bi bi-calendar-week-fill"></i> Assigned Lectures
            </h4>
            @can('Add Schedules')
            <a href="{{ route('schedules.create') }}"
               class="btn btn-gradient-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white w-full sm:w-auto justify-center">
                <i class="bi bi-calendar-plus"></i> Assign New Lecture
            </a>
            @endcan
        </div>

        <!-- Filter Form -->
        <div class="p-4 bg-gray-50 border-b">
            <form action="{{ route('schedules.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="teacher_id_filter" class="form-label fw-semibold">Filter by Teacher:</label>
                        <select name="teacher_id" id="teacher_id_filter" class="form-select rounded-3 shadow-sm">
                            <option value="">All Teachers</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="class_id_filter" class="form-label fw-semibold">Filter by Class:</label>
                        <select name="class_id" id="class_id_filter" class="form-select rounded-3 shadow-sm">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill w-full md:w-auto">Filter</button>
                        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary rounded-pill w-full md:w-auto">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Card Body -->
        <div class="p-0">
            @if (session('success'))
            <div class="m-4 bg-green-100 text-green-800 px-4 py-3 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="overflow-x-auto">
                <table id="schedulesTable" class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3">Day</th>
                            <th class="px-4 py-3">Time</th>
                            <th class="px-4 py-3">Class</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Teacher</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold">{{ $schedule->day_of_week }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                            </td>
                            <td class="px-4 py-3">{{ $schedule->schoolClass->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $schedule->subject->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $schedule->teacher->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    @can('Edit Schedules')
                                    <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-icon badge-gradient-primary"><i class="bi bi-eye"></i></a>
                                    @endcan
                                    @can('Edit Schedules')
                                    <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-icon badge-gradient-primary"><i class="bi bi-pencil-square"></i></a>
                                    @endcan
                                    @can('Delete Schedules')
                                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon badge-gradient-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-6">
                                <i class="bi bi-emoji-frown text-lg block mb-2"></i>
                                No lectures found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Note: We are now using server-side filtering, so the basic DataTable is sufficient.
    $(document).ready(function() {
        $('#schedulesTable').DataTable({
            paging: true,
            searching: true, // This enables local search on the filtered results
            ordering: false, // Ordering is now handled by the controller
            info: true,
            responsive: true,
        });
    });
</script>
@endpush
