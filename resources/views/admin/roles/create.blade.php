@extends('layouts.admin')

@section('title', 'Create Role')
@section('page-title', 'Create Role')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header custom-card-header py-3">
        <h6 class="m-0 font-weight-bold">Create New Role</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="mb-4">
                <label for="roleName" class="form-label fw-bold">Role Name</label>
                <input type="text" name="name" class="form-control" id="roleName" placeholder="e.g., Administrator, Editor" required>
            </div>

            <h5 class="mb-3 text-dark">Assign Permissions</h5>
            <div class="card bg-light p-4 mb-4 border-start rounded-3">
                <div class="row">
                    @foreach($permissions as $permission)
                        <div class="col-md-4 col-sm-6">
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="form-check-input" id="permission-{{ $permission->id }}">
                                <label class="form-check-label text-capitalize" for="permission-{{ $permission->id }}">
                                    {{ str_replace('-', ' ', $permission->name) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Role</button>
            </div>
        </form>
    </div>
</div>
@endsection