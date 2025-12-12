@extends('layouts.admin')

@section('title', 'Student Admissions')
@section('page-title', 'Enrolled Students')

@section('content')
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <div class="custom-card-header flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 px-4 py-3 border-b">
            <h4 class="text-lg font-bold text-primary-custom flex items-center gap-2">
                <i class="bi bi-people-fill"></i> Enrolled Students
            </h4>
            @can('Add Admission')
            <a href="{{ route('students.create') }}"
               class="btn btn-gradient-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white w-full sm:w-auto justify-center">
                <i class="bi bi-plus-circle"></i> Enroll New Student
            </a>
            @endcan
        </div>

        <div class="p-0">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm m-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="overflow-x-auto">
                <table id="studentsTable" class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3">Photo</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Class</th>
                            <th class="px-4 py-3">Section</th>
                            <th class="px-4 py-3">Father's Name</th>
                            <th class="px-4 py-3">Father's Phone</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                        <tr class="border-b hover:bg-gray-50">
                            {{-- START IMAGE FIX WITH FALLBACK --}}
                            <td class="px-4 py-3">
                                @php
                                    $imagePath = $student->user->user_pic ?? null;
                                    // Get the first letter of the student's name for a placeholder
                                    $initials = $student->user->name ? strtoupper(substr($student->user->name, 0, 1)) : 'S';
                                @endphp

                                @if ($imagePath)
                                <img src="{{ asset('storage/' . $imagePath) }}"
                                     alt="{{ $student->user->name ?? 'Student Photo' }}"
                                     class="rounded-full w-12 h-12 object-cover">
                                @else
                                {{-- Placeholder when image is missing (using Tailwind classes for style) --}}
                                <div class="rounded-full w-12 h-12 bg-gray-300 flex items-center justify-center text-lg font-bold text-gray-600">
                                    {{ $initials }}
                                </div>
                                @endif
                            </td>
                            {{-- END IMAGE FIX WITH FALLBACK --}}
                            <td class="px-4 py-3 font-semibold max-w-[160px] truncate">{{ $student->user->name ?? '' }}</td>
                            <td class="px-4 py-3 max-w-[200px] truncate">{{ $student->user->email ?? '' }}</td>
                            <td class="px-4 py-3">{{ $student->schoolClass->name ?? ''}}</td>
                            <td class="px-4 py-3">{{ $student->section ?? '' }}</td>
                            <td class="px-4 py-3">{{ $student->father_name ?? '' }}</td>
                            <td class="px-4 py-3">{{ $student->father_phone ?? ''}}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    @can('View Admission')
                                    <a href="{{ route('students.show', $student->id) }}" class="btn btn-icon badge-gradient-warning">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endcan
                                    @can('Edit Admission')
                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-icon badge-gradient-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan
                                    @can('Delete Admission')
                                    <button type="button" class="btn btn-icon badge-gradient-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $student->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 py-6">
                                <i class="bi bi-emoji-frown text-lg"></i> No students enrolled yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@foreach ($students as $student)
<div class="modal fade" id="deleteModal-{{ $student->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow-sm">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-trash-fill me-1"></i> Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the record for <strong>{{ $student->user->name }}</strong>? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('students.destroy', $student->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-gradient-danger rounded-pill px-4">
                        <i class="bi bi-trash me-1"></i> Delete Student
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#studentsTable').DataTable({
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
                    targets: 7 // Corrected target index for 'Actions' column (0-based)
                }
            ]
        });
    });
</script>
@endpush