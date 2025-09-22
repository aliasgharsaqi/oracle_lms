@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-3 text-gray-800">Manage Schools</h1>
        <a href="{{ route('admin.schools.create') }}" class="btn btn-primary mb-3">Add New School</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Schools List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Subscription Plan</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schools as $school)
                            <tr>
                                <td>
                                    @if($school->logo)
                                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" width="50">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $school->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($school->status) }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($school->subscription_plan) }}</td>
                                <td>
                                    <a href="{{ route('admin.schools.edit', $school) }}" class="btn btn-info btn-sm">Edit</a>
                                    <form action="{{ route('admin.schools.destroy', $school) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No schools found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $schools->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


