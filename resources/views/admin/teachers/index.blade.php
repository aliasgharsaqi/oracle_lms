@extends('layouts.admin')

@section('title', 'Teacher Management')
@section('page-title', 'All Teachers')

@section('content')
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div
            class="custom-card-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 px-4 py-3 border-b bg-primary">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-people-fill"></i> All Staff / Teachers
            </h4>
            @can('Add Teachers')
            <a href="{{ route('teachers.create') }}"
                class="btn btn-gradient-primary inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-white w-full sm:w-auto">
                <i class="bi bi-person-plus-fill"></i> Add New Teacher
            </a>
            @endcan
        </div>

        <!-- Card Body -->
        <div class="p-0">
            {{-- Success Message --}}
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
                <table id="teachersTable" class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3">Photo</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Education</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers ?? [] as $teacher)
                        <tr class="border-b hover:bg-gray-50">
                            {{-- Column 1: Photo --}}
                            <td class="px-4 py-3">
                                <img src="{{ asset('storage/' . $teacher->user->user_pic) }}"
                                    alt="{{ $teacher->user->name }}"
                                    class="rounded-full shadow-sm w-11 h-11 object-cover">
                            </td>
                            {{-- Column 2: Name --}}
                            <td class="px-4 py-3 font-semibold max-w-[160px] truncate">{{ $teacher->user->name }}</td>
                            {{-- Column 3: Email --}}
                            <td class="px-4 py-3 max-w-[200px] truncate">{{ $teacher->user->email }}</td>
                            {{-- Column 4: Phone --}}
                            <td class="px-4 py-3">{{ $teacher->user->phone }}</td>
                            {{-- Column 5: Education --}}
                            <td class="px-4 py-3">{{ $teacher->education }}</td>
                            {{-- Column 6: Actions --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    @can('Edit Teachers')
                                    <a href="{{ route('teachers.edit', $teacher->id) }}"
                                        class="btn btn-icon badge-gradient-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan
                                    @can('Delete Teachers')
                                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this teacher?')">
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
                        @endforeach
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
        $('#teachersTable').DataTable({
            dom: "<'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-2'<'flex flex-wrap gap-2'B><'ml-auto'f>>" + // Top: buttons left, search right
                "<'overflow-x-auto'tr>" + // Table
                "<'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-2'<'text-sm text-gray-600'i><'ml-auto'p>>", // Bottom: info left, pagination right

            buttons: [{
                    extend: 'copy'
                },
                {
                    extend: 'csv'
                },
                {
                    extend: 'excel'
                },
                {
                    extend: 'pdf'
                },
                {
                    extend: 'print'
                },
                {
                    extend: 'colvis'
                }
            ],

            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,

            columnDefs: [{
                    orderable: false,
                    targets: [0, 5]
                } // Disable sorting on Photo & Actions
            ],

            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search teachers..."
            }
        });
    });
</script>

@endpush