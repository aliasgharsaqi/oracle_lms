@extends('layouts.admin')

@section('title', 'Class Management')
@section('page-title', 'All Classes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-journal-bookmark-fill me-2"></i> Class List
                </h4>
                @can('Add Classes')
                <a href="{{ route('classes.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add New Class
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
                    <table class="table table-hover custom-table align-middle" id="classesTable">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th>#</th>
                                <th>Class Name</th>
                                <th>Created On</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $key => $class)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="fw-semibold">{{ $class->name }}</td>
                                <td>{{ $class->created_at->format('M d, Y') }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        @can('Edit Classes')
                                        <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-icon badge-gradient-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @endcan

                                        @can('Delete Classes')
                                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon badge-gradient-danger" onclick="return confirm('Are you sure you want to delete this class?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown fs-4"></i> No classes found.
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
        $('#classesTable').DataTable({
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
                    "targets": 3
                } // Disable sorting on 'Actions' column
            ]
        });
    });
</script>
@endpush