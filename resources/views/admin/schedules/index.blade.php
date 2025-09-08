@extends('layouts.admin')

@section('title', 'Weekly Schedule')
@section('page-title', 'Class Schedules')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <!-- Card Header -->
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-calendar-week-fill me-2"></i> Assigned Lectures
                </h4>
                @can('Add Schedules')
                <a href="{{ route('schedules.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-calendar-plus me-1"></i> Assign New Lecture
                </a>
                @endcan
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Success Message -->
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle" id="schedulesTable">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules->sortBy(['day_of_week', 'start_time']) as $schedule)
                            <tr>
                                <td class="fw-semibold">{{ $schedule->day_of_week }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                </td>
                                <td>{{ $schedule->schoolClass->name  ?? '' }}</td>
                                <td>{{ $schedule->subject->name ?? '' }}</td>
                                <td>{{ $schedule->teacher->user->name ?? '' }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        @can('View Schedules')
                                        <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-icon badge-gradient-warning">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @endcan
                                        @can('Edit Schedules')
                                        <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-icon badge-gradient-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @endcan
                                        @can('Delete Schedules')
                                        <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon badge-gradient-danger" onclick="return confirm('Are you sure you want to delete this lecture?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown fs-4 d-block mb-2"></i>
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
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#schedulesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            "columnDefs": [{
                    "orderable": false,
                    "targets": 5
                } // Disable sorting on 'Actions' column
            ]
        });
    });
</script>
@endpush