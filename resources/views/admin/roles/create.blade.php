@extends('layouts.admin')

@section('title', 'Create Role')
@section('page-title', 'Create New Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-shield-plus-fill me-2"></i> Create New Role
                </h5>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to Roles
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="roleName" class="form-label fw-bold">Role Name</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-sm" id="roleName" placeholder="e.g., Teacher, Accountant" required>
                    </div>

                    <h5 class="mb-3 text-dark">Assign Permissions</h5>

                    @php
                    $groupedPermissions = [];
                    foreach ($permissions as $permission) {
                    if (str_contains($permission->name, 'User')) $group = 'User Management';
                    elseif (str_contains($permission->name, 'Role')) $group = 'Role Management';
                    elseif (str_contains($permission->name, 'Class')) $group = 'Class Management';
                    elseif (str_contains($permission->name, 'Teacher')) $group = 'Teacher Management';
                    elseif (str_contains($permission->name, 'Subject')) $group = 'Subject Management';
                    elseif (str_contains($permission->name, 'Schedule')) $group = 'Schedule Management';
                    elseif (str_contains($permission->name, 'Admission')) $group = 'Admission Management';
                    elseif (str_contains($permission->name, 'Mark')) $group = 'Marks Management';
                    elseif (str_contains($permission->name, 'Fee')) $group = 'Fee Management';
                    elseif (str_contains($permission->name, 'Report')) $group = 'Report Management';
                    elseif (str_contains($permission->name, 'Attendence')) $group = 'Attendence Management';
                    else $group = 'General';

                    $groupedPermissions[$group][] = $permission;
                    }
                    @endphp

                    @foreach($groupedPermissions as $group => $permissionsInGroup)
                    <div class="card bg-light p-3 mb-3 border-start border-primary border-4 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0">{{ $group }}</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="selectAll-{{ Str::slug($group) }}">
                                <label class="form-check-label" for="selectAll-{{ Str::slug($group) }}">Select All</label>
                            </div>
                        </div>

                        <hr class="my-2">
                        <div class="row permission-group" data-group-id="{{ Str::slug($group) }}">
                            @foreach($permissionsInGroup as $permission)
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="form-check-input permission-checkbox" id="permission-{{ $permission->id }}">
                                    <label class="form-check-label text-capitalize" for="permission-{{ $permission->id }}">
                                        {{ str_replace(['-', '_'], ' ', $permission->name) }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">Cancel</a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[id^="selectAll-"]').forEach(function(selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const groupId = this.id.replace('selectAll-', '');
                const checkboxes = document.querySelectorAll('.permission-group[data-group-id="' + groupId + '"] .permission-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        });
    });
</script>
@endpush