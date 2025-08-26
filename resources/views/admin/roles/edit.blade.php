@extends('layouts.admin')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Assign Permissions</label><br>
                @foreach($permissions as $permission)
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                            class="form-check-input"
                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $permission->name }}</label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
