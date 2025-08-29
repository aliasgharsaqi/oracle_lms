@extends('layouts.admin')

@section('title', 'Student Admissions')
@section('page-title', 'Enrolled Students')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <!-- Card Header -->
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-people-fill me-2"></i> Enrolled Students
                </h4>
                <a href="{{ route('students.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-plus-circle me-1"></i> Enroll New Student
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle mb-0">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Father's Name</th>
                                <th>Father's Phone</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                            <tr>
                                <td>
                                    <img src="{{ asset('storage/' . ($student->user->user_pic ?? '')) }}" 
                                         alt="{{ $student->user->name }}" 
                                         class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                </td>
                                <td class="fw-semibold">{{ $student->user->name }}</td>
                                <td class="text-truncate" style="max-width: 200px;">{{ $student->user->email }}</td>
                                <td>{{ $student->schoolClass->name }}</td>
                                <td>{{ $student->section }}</td>
                                <td>{{ $student->father_name }}</td>
                                <td>{{ $student->father_phone }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <a href="{{ route('students.show', $student->id) }}" class="btn btn-icon badge-gradient-warning">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-icon badge-gradient-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-icon badge-gradient-danger" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $student->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown fs-4"></i> No students enrolled yet.
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

<!-- Delete Modals -->
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
