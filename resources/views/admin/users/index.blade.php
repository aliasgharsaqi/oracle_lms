@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'All Users')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Active Users</h5>
                @can('create', App\Models\User::class)
                    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add New User</a>
                @endcan
            </div>
            <div class="card-body">
                {{-- @include('partials.alerts') success / error alerts --}}
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
    @if($user->roles->isNotEmpty())
        @foreach($user->roles as $role)
            <span class="badge bg-primary">{{ $role->name }}</span>
        @endforeach
    @else
        <span class="badge bg-secondary">No Role</span>
    @endif
</td>

                                    <td>
                                        @if($user->status == 1) <span class="badge bg-success">Active</span>
                                        @elseif($user->status == 2) <span class="badge bg-warning">Pending</span>
                                        @else <span class="badge bg-danger">Suspended</span> @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                        @can('update', $user)
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                        @endcan
                                        @can('delete', $user)
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $user->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No active users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modals --}}
@foreach($users as $user)
<div class="modal fade" id="deleteModal-{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the user: <strong>{{ $user->name }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ================= Trashed Users Section ================= --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">Trashed Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Deleted On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($trashedUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->deleted_at->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No trashed users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
