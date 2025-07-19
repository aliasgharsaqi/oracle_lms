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
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Subject Name</th>
                                <th>Subject Code</th>
                                <th>Created On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $subject)
                            <tr>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->subject_code }}</td>
                                <td>{{ $subject->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info">Edit</a>
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
