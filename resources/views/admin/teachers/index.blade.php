@extends('layouts.admin')

@section('title', 'Teacher Management')
@section('page-title', 'All Teachers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Staff / Teachers</h5>
                <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Add New Teacher
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
                                <th>Phone</th>
                                <th>Education</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teachers as $teacher)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/' . $teacher->user->user_pic) }}" alt="{{ $teacher->user->name }}" class="rounded-circle" width="40" height="40">
                                    </td>
                                    <td>{{ $teacher->user->name }}</td>
                                    <td>{{ $teacher->user->email }}</td>
                                    <td>{{ $teacher->user->phone }}</td>
                                    <td>{{ $teacher->education }}</td>
                                    <td>
                                        {{-- <a href="#" class="btn btn-sm btn-info">View</a> --}}
                                        
    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-sm btn-warning">Edit</a>
    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this teacher?')">
            Delete
        </button>
    </form>
</td>

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
@endsection
