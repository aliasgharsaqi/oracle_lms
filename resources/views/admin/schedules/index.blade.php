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


        <!-- Card Body -->
        <div class="p-0">
            <!-- Success Message -->
            @if (session('success'))
            <div class="mx-4 my-3 bg-green-100 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between shadow-sm"
                 role="alert">
                <span class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </span>
                <button type="button" class="text-green-700 hover:text-green-900" data-bs-dismiss="alert">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            @endif

            <!-- Responsive Table -->
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
                        @forelse($schedules->sortBy(['day_of_week', 'start_time']) as $schedule)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold">{{ $schedule->day_of_week }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                            </td>
                            <td class="px-4 py-3">{{ $schedule->schoolClass->name ?? '' }}</td>
                            <td class="px-4 py-3">{{ $schedule->subject->name ?? '' }}</td>
                            <td class="px-4 py-3">{{ $schedule->teacher->user->name ?? '' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    @can('View Schedules')
                                    <a href="{{ route('schedules.show', $schedule->id) }}"
                                       class="btn btn-icon badge-gradient-warning">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endcan
                                    @can('Edit Schedules')
                                    <a href="{{ route('schedules.edit', $schedule->id) }}"
                                       class="btn btn-icon badge-gradient-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan
                                    @can('Delete Schedules')
                                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this lecture?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon badge-gradient-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-6">
                                <i class="bi bi-emoji-frown text-lg block mb-2"></i>
                                No lectures have been scheduled yet.
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
    $(document).ready(function() {
        $('#schedulesTable').DataTable({
            dom: 
                "<'flex flex-col md:flex-row justify-between items-center my-3 mx-3'<'flex items-center gap-2'B><'ml-auto'f>>" + // Buttons + Search aligned
                "<'overflow-x-auto'tr>" + // Table with responsive scroll
                "<'flex flex-col md:flex-row justify-between items-center my-3 mx-3'<'text-sm'i><'mt-2 md:mt-0'p>>", // Info + Pagination aligned
            buttons: [
                {
                    extend: 'copy',
                },
                {
                    extend: 'csv',
                },
                {
                    extend: 'excel',
                },
                {
                    extend: 'pdf',
                },
                {
                    extend: 'print',
                },
                {
                    extend: 'colvis',
                }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            columnDefs: [
                {
                    orderable: false,
                    targets: 5 // Disable sorting on 'Actions' column
                }
            ]
        });
    });
</script>

@endpush