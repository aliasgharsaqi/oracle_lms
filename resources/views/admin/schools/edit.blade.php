@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3 text-gray-800">Edit School</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $school->name }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.schools.update', $school) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">School Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $school->name) }}" required>
                </div>
                <div class="form-group">
                    <label for="email">School Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $school->email) }}" required>
                </div>
                <div class="form-group">
                    <label for="subscription_plan">Subscription Plan</label>
                    <select name="subscription_plan" id="subscription_plan" class="form-control" required>
                        <option value="basic" {{ $school->subscription_plan == 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="premium" {{ $school->subscription_plan == 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Account Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" {{ $school->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $school->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="logo">Update School Logo</label>
                    <input type="file" name="logo" id="logo" class="form-control-file">
                    @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="mt-2" width="100">
                    @endif
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update School</button>
            </form>
        </div>
    </div>
</div>
@endsection