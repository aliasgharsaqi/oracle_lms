@extends('layouts.admin')

@section('title', 'Student Fee Plans')
@section('page-title', 'Student Fee Plans')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h5 class="card-title mb-0">Select a Student to Manage Their Fee Plan</h5>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Fee Plan Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->user->name }}</td>
                        <td>{{ $student->schoolClass->name ?? 'N/A' }}</td>
                        <td>
                            @if($student->fee_plans_count > 0)
                                <span class="badge bg-success">Plan Defined</span>
                            @else
                                <span class="badge bg-warning">No Plan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('fees.plans.create', $student->id) }}" class="btn btn-sm btn-primary">Manage Plan</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
