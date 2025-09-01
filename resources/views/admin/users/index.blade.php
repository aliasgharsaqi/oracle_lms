@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'All Users')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-people-fill me-2"></i> Active Users
                </h4>
                @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add User
                </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle mb-0">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th><i class="bi bi-person"></i> Name</th>
                                <th><i class="bi bi-envelope"></i> Email</th>
                                <th><i class="bi bi-shield-lock"></i> Role</th>
                                <th><i class="bi bi-check2-circle"></i> Status</th>
                                <th><i class="bi bi-calendar3"></i> Joined</th>
                                <th class="text-center"><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr>
                                <td class="fw-semibold text-truncate" style="max-width: 160px;">{{ $user->name }}</td>
                                <td class="text-truncate" style="max-width: 200px;">{{ $user->email }}</td>
                                <td>
                                    @if($user->roles->isNotEmpty())
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-gradient-primary">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge badge-light">No Role</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status == 1) 
                                        <span class="badge badge-gradient-success">Active</span>
                                    @elseif($user->status == 2) 
                                        <span class="badge badge-gradient-warning">Pending</span>
                                    @else 
                                        <span class="badge badge-gradient-danger">Suspended</span> 
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-icon badge-gradient-warning">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('update', $user)
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-icon badge-gradient-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @endcan
                                        @can('delete', $user)
                                        <button type="button" class="btn btn-icon badge-gradient-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal-{{ $user->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown fs-4"></i> No active users found.
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

{{-- Trashed Users --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-header custom-card-header">
                <h4 class="card-title mb-0 text-danger-custom fw-bold">
                    <i class="bi bi-trash-fill me-2"></i> Trashed Users
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle mb-0">
                        <thead class="table-header-secondary text-nowrap">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Deleted On</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($trashedUsers as $user)
                            <tr>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td class="text-truncate" style="max-width: 200px;">{{ $user->email }}</td>
                                <td>{{ $user->deleted_at->format('M d, Y') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-gradient-success">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox"></i> No trashed users found.
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
