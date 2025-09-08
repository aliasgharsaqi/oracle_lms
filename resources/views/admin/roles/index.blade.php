@extends('layouts.admin')

@section('title', 'Roles')
@section('page-title', 'Roles Management')

@section('content')
<div class="row justify-content-center">
    <div class="">
        <div class="card shadow border-0 rounded-xl">
            <div class="custom-card-header d-flex justify-content-between rounded-xl">
                <h5 class="mb-0">Roles</h5>
                @can('Add Role')
                <a href="{{ route('admin.roles.create') }}" class="btn btn-gradient-primary">
                    <i class="bi bi-plus-circle"></i> Add Role
                </a>
                @endcan
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td class="fw-semibold">{{ $role->name }}</td>
                                <td>
                                    @foreach($role->permissions as $permission)
                                    <span class="badge bg-info text-dark me-1 mb-1">{{ $permission->name }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @can('Edit Role')
                                    <a href="{{ route('admin.roles.edit', $role->id) }}"
                                        class="btn btn-icon badge-gradient-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan

                                    @can('Delete Role')
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this role?')"
                                            class="btn btn-icon badge-gradient-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $roles->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection