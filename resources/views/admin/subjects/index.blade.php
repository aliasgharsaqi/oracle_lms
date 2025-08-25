@extends('layouts.admin')

@section('title', 'Subjects Management')
@section('page-title', 'All Subjects')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Subject List</h5>
                <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add New Subject
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
                <table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($subjects as $subject)
            <tr>
                <td>{{ $subject->name }}</td>
                <td>{{ $subject->subject_code }}</td>
                <td>
                    <form action="{{ route('subjects.toggleStatus', $subject->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $subject->active ? 'btn-success' : 'btn-warning' }}">
                            {{ $subject->active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-sm btn-info">Edit</a>
                    <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
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
