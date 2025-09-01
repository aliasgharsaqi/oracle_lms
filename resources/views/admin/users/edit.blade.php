@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . $user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-pencil-square me-2"></i> Update User Details
                </h5>
                <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to List
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

                {{-- Edit Form --}}
                <form action="{{ route('users.update', $user->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" class="form-control rounded-3 shadow-sm" 
                               id="name" name="name" 
                               value="{{ old('name', $user->name) }}" 
                               placeholder="Enter full name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control rounded-3 shadow-sm" 
                               id="email" name="email" 
                               value="{{ old('email', $user->email) }}" 
                               placeholder="Enter email address" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New Password (Optional)</label>
                        <input type="password" class="form-control rounded-3 shadow-sm" 
                               id="password" name="password" 
                               placeholder="Leave blank to keep current password">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label fw-semibold">Assign Role</label>
                            <select class="form-select rounded-3 shadow-sm" id="role" name="role" required>
                                <option value="" disabled>Select a role...</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" 
                                        {{ old('role', $user->roles->pluck('name')->first()) == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select rounded-3 shadow-sm" id="status" name="status" required>
                                <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="2" {{ old('status', $user->status) == 2 ? 'selected' : '' }}>Pending</option>
                                <option value="3" {{ old('status', $user->status) == 3 ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('users.index') }}" 
                           class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" 
                                class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
