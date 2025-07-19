@extends('layouts.admin')

@section('title', 'Add New Class')
@section('page-title', 'Create Class')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">New Class Name</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                         @foreach ($errors->all() as $error)
                            {{ $error }}
                         @endforeach
                    </div>
                @endif
                <form action="{{ route('classes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Class Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g., Grade 10 - Section A">
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('classes.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
