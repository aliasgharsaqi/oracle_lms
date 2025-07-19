@extends('layouts.admin')

@section('title', 'Class Management')
@section('page-title', 'All Classes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Class List</h5>
                <a href="{{ route('classes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add New Class
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
                                <th>#</th>
                                <th>Class Name</th>
                                <th>Created On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach($classes as $key => $class)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $class->name }}</td>
                                <td>{{ $class->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
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
