@extends('layouts.admin')

@section('title', 'Teacher Management')
@section('page-title', 'All Teachers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-people-fill me-2"></i> All Staff / Teachers
                </h4>
                @can('Add Teachers')
                <a href="{{ route('teachers.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-person-plus-fill me-1"></i> Add New Teacher
                </a>
                @endcan
            </div>
            <div class="card-body">
                {{-- Success Message --}}
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle" id="teachersTable">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Education</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teachers as $teacher)
                            <tr>
                                <td>
                                    <img src="{{ asset('storage/' . $teacher->user->user_pic) }}"
                                        alt="{{ $teacher->user->name }}"
                                        class="rounded-circle shadow-sm" width="45" height="45">
                                </td>
                                <td class="fw-semibold">{{ $teacher->user->name }}</td>
                                <td>{{ $teacher->user->email }}</td>
                                <td>{{ $teacher->user->phone }}</td>
                                <td>{{ $teacher->education }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        @can('Edit Teachers')
                                        <a href="{{ route('teachers.edit', $teacher->id) }}"
                                            class="btn btn-icon badge-gradient-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @endcan

                                        @can('Delete Teachers')
                                        <form action="{{ route('teachers.destroy', $teacher->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-icon badge-gradient-danger"
                                                onclick="return confirm('Are you sure you want to delete this teacher?')">
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
                                    <i class="bi bi-emoji-frown fs-4"></i> No teachers found.
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
        $('#teachersTable').DataTable({
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
                    "targets": [0, 5]
                } // Disable sorting on Photo and Actions columns
            ]
        });
    });
</script>
@endpush