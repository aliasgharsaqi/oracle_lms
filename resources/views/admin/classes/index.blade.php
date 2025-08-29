@extends('layouts.admin')

@section('title', 'Class Management')
@section('page-title', 'All Classes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-journal-bookmark-fill me-2"></i> Class List
                </h4>
                <a href="{{ route('classes.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add New Class
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle mb-0">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th><i class="bi bi-hash"></i> #</th>
                                <th><i class="bi bi-journal-text"></i> Class Name</th>
                                <th><i class="bi bi-calendar3"></i> Created On</th>
                                <th class="text-center"><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $key => $class)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="fw-semibold text-truncate" style="max-width: 200px;">{{ $class->name }}</td>
                                <td>{{ $class->created_at->format('M d, Y') }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-icon badge-gradient-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon badge-gradient-danger" onclick="return confirm('Are you sure you want to delete this class?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown fs-4"></i> No classes found.
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
@endsection
