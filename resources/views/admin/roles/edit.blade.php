@extends('layouts.admin')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
<div class="row justify-content-center">
    <div class="">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-shield-lock-fill me-2"></i> Update Role
                </h5>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to Roles
                </a>
            </div>
            <div class="card-body p-4">
                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Edit Role Form --}}
                <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Name</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-sm" 
                               value="{{ old('name', $role->name) }}" placeholder="Enter role name" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Assign Permissions</label>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($permissions as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="form-check-input" 
                                        id="perm-{{ $permission->id }}"
                                        {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.roles.index') }}" 
                           class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" 
                                class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
