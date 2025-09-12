@extends('layouts.admin')

@section('title', 'Class Management')
@section('page-title', 'All Classes')

@section('content')
<!-- Class List -->
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div
            class="custom-card-header flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 px-4 py-3 border-b bg-primary">
            <!-- Title -->
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-journal-bookmark-fill"></i> Class List
            </h4>

            <!-- Button -->
            @can('Add Classes')
            <a href="{{ route('classes.create') }}"
                class="btn btn-gradient-primary inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-white w-full sm:w-auto">
                <i class="bi bi-plus-circle"></i> Add New Class
            </a>
            @endcan
        </div>


        <!-- Card Body -->
        <div class="p-0">
            @if (session('success'))
            <div class="px-4 py-3">
                <div class="alert alert-success flex items-center justify-between rounded-md px-4 py-3 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Class Name</th>
                            <th class="px-4 py-3">Created On</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $key => $class)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $key + 1 }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $class->name }}</td>
                            <td class="px-4 py-3">{{ $class->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    @can('Edit Classes')
                                    <a href="{{ route('classes.edit', $class->id) }}"
                                        class="btn btn-icon badge-gradient-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan

                                    @can('Delete Classes')
                                    <form action="{{ route('classes.destroy', $class->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this class?')"
                                            class="btn btn-icon badge-gradient-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-6">
                                <i class="bi bi-emoji-frown text-lg"></i> No classes found.
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