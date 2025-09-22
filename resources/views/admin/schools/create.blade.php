@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3 text-gray-800">Add New School</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">New School Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.schools.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- School Details --}}
                <fieldset class="mb-4">
                    <legend>School Information</legend>
                    <div class="form-group">
                        <label for="school_name">School Name</label>
                        <input type="text" name="school_name" id="school_name" class="form-control" value="{{ old('school_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="school_email">School Email</label>
                        <input type="email" name="school_email" id="school_email" class="form-control" value="{{ old('school_email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="subscription_plan">Subscription Plan</label>
                        <select name="subscription_plan" id="subscription_plan" class="form-control" required>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="logo">School Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control-file">
                    </div>
                </fieldset>

                {{-- Admin User Details --}}
                <fieldset>
                    <legend>School Admin Account</legend>
                    <div class="form-group">
                        <label for="admin_name">Admin Name</label>
                        <input type="text" name="admin_name" id="admin_name" class="form-control" value="{{ old('admin_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_email">Admin Email</label>
                        <input type="email" name="admin_email" id="admin_email" class="form-control" value="{{ old('admin_email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Password</label>
                        <input type="password" name="admin_password" id="admin_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password_confirmation">Confirm Password</label>
                        <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" class="form-control" required>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-primary mt-3">Create School & Admin</button>
            </form>
        </div>
    </div>
</div>
@endsection