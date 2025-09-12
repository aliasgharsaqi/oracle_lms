@extends('layouts.admin')

@section('title', 'Subjects Management')
@section('page-title', 'All Subjects')

@section('content')
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div class="custom-card-header flex flex-wrap justify-between items-center gap-2 px-4 py-3 border-b">
            <h4 class="text-lg font-bold text-primary-custom flex items-center gap-2">
                <i class="bi bi-journal-bookmark-fill"></i> Subject List
            </h4>
            @can('Add Subject')
            <a href="{{ route('subjects.create') }}"
               class="btn btn-gradient-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white">
                <i class="bi bi-plus-circle"></i> Add New Subject
            </a>
            @endcan
        </div>

        <!-- Card Body -->
        <div class="p-0">
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

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Subject Name</th>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subjects as $key => $subject)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $key + 1 }}</td>
                            <td class="px-4 py-3 font-semibold max-w-[200px] truncate">{{ $subject->name }}</td>
                            <td class="px-4 py-3">{{ $subject->subject_code }}</td>
                            <td class="px-4 py-3">
                                @can('Manage Subject Status')
                                <form action="{{ route('subjects.toggleStatus', $subject->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $subject->active ? 'badge-gradient-success' : 'badge-gradient-warning' }}">
                                        {{ $subject->active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                                @else
                                <span class="btn btn-sm {{ $subject->active ? 'badge-gradient-success' : 'badge-gradient-warning' }} disabled">
                                    {{ $subject->active ? 'Active' : 'Inactive' }}
                                </span>
                                @endcan
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    @can('Edit Subject')
                                    <a href="{{ route('subjects.edit', $subject->id) }}"
                                        class="btn btn-icon badge-gradient-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan
                                    @can('Delete Subject')
                                    <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this subject?')">
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
                            <td colspan="5" class="text-center text-gray-500 py-6">
                                <i class="bi bi-emoji-frown text-lg"></i> No subjects found.
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