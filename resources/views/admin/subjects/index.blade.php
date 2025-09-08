@extends('layouts.admin')

@section('title', 'Subjects Management')
@section('page-title', 'All Subjects')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-journal-bookmark-fill me-2"></i> Subject List
                </h4>
                @can('Add Subject')
                <a href="{{ route('subjects.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add New Subject
                </a>
                @endcan
            </div>
            <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle" id="subjectsTable">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th>#</th>
                                <th>Subject Name</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subjects as $key => $subject)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="fw-semibold">{{ $subject->name }}</td>
                                <td>{{ $subject->subject_code }}</td>
                                <td>
                                    @can('Manage Subject Status')
                                    <form action="{{ route('subjects.toggleStatus', $subject->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $subject->active ? 'badge-gradient-success' : 'badge-gradient-warning' }}">
                                            {{ $subject->active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                    @else
                                    <span class="btn btn-sm {{ $subject->active ? 'badge-gradient-success' : 'badge-gradient-warning' }} disabled">
                                        {{ $subject->active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @endcan
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        @can('Edit Subject')
                                        <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-icon badge-gradient-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @endcan
                                        @can('Delete Subject')
                                        <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon badge-gradient-danger" onclick="return confirm('Are you sure you want to delete this subject?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown fs-4"></i> No subjects found.
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
        $('#subjectsTable').DataTable({
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
                    "targets": [3, 4]
                } // Disable sorting on Status and Actions columns
            ]
        });
    });
</script>
@endpush