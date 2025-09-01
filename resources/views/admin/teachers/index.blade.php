@extends('layouts.admin')

@section('title', 'Teacher Management')
@section('page-title', 'All Teachers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-people-fill me-2"></i> All Staff / Teachers
                </h4>
                <a href="{{ route('teachers.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-person-plus-fill me-1"></i> Add New Teacher
                </a>
            </div>
            <div class="card-body">
                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle mb-0">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th><i class="bi bi-image"></i> Photo</th>
                                <th><i class="bi bi-person"></i> Name</th>
                                <th><i class="bi bi-envelope"></i> Email</th>
                                <th><i class="bi bi-telephone"></i> Phone</th>
                                <th><i class="bi bi-mortarboard"></i> Education</th>
                                <th class="text-center"><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teachers as $teacher)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/' . $teacher->user->user_pic) }}" 
                                             alt="{{ $teacher->user->name }}" 
                                             class="rounded-circle shadow-sm" width="45" height="45">
                                    </td>
                                    <td class="fw-semibold text-truncate" style="max-width: 160px;">
                                        {{ $teacher->user->name }}
                                    </td>
                                    <td class="text-truncate" style="max-width: 200px;">
                                        {{ $teacher->user->email }}
                                    </td>
                                    <td>{{ $teacher->user->phone }}</td>
                                    <td>{{ $teacher->education }}</td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center gap-2">
                                            <a href="{{ route('teachers.edit', $teacher->id) }}" 
                                               class="btn btn-icon badge-gradient-warning">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('teachers.destroy', $teacher->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-icon badge-gradient-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this teacher?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-emoji-frown fs-4"></i> No teachers found.
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
