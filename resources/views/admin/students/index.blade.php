@extends('layouts.admin')

@section('title', 'Student Admissions')
@section('page-title', 'Enrolled Students')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Students</h5>
                <a href="{{ route('students.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Enroll New Student
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Father's Name</th>
                                <th>Father's Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                            <tr>
                                <td>
                                    <img src="{{ asset('storage/' . $student->user->user_pic ?? '') }}" alt="{{ $student->user->name }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                </td>
                                <td>{{ $student->user->name }}</td>
                                <td>{{ $student->user->email }}</td>
                                <td>{{ $student->schoolClass->name }}</td>
                                <td>{{ $student->section }}</td>
                                <td>{{ $student->father_name }}</td>
                                <td>{{ $student->father_phone }}</td>
                                <td>
                                    <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $student->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the record for <strong>{{ $student->user->name }}</strong>? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('students.destroy', $student->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Student</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
