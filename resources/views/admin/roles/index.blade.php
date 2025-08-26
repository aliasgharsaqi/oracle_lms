@extends('layouts.admin')

@section('title', 'Roles')
@section('page-title', 'Roles Management')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Roles</h5>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">+ Add Role</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>
                        @foreach($role->permissions as $permission)
                            <span class="badge bg-info">{{ $permission->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $roles->links() }}
    </div>
</div>
@endsection
