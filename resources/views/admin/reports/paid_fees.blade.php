@extends('layouts.admin')

@section('title', 'Paid Fee')
@section('page-title', 'All Paid Student')

@section('content')
<div class="card shadow mb-4">
 <div class="card-header">
        <h5 class="card-title mb-0">Select Class and Month</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.paidFees') }}">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label for="class_id" class="form-label">Class</label>
                    <select class="form-select" name="class_id" required>
                        <option value="">Select a class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="month" class="form-label">Month</label>
                    <input type="month" class="form-control" name="month" value="{{ $selectedMonth }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Load Students</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Paid / Student</h5>
                {{-- <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Add New Teacher
                </a> --}}
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="teachersTable">

                        <thead>
        <tr>
            <th class="border px-2 py-1">Student</th>
            <th class="border px-2 py-1">Month</th>
            <th class="border px-2 py-1">Amount Due</th>
            <th class="border px-2 py-1">Paid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($paidFees as $fee)
        <tr>
            <td class="border px-2 py-1">{{ $fee->student->user->name }}</td>
            <td class="border px-2 py-1">{{ \Carbon\Carbon::parse($fee->voucher_month)->format('F Y') }}</td>
            <td class="border px-2 py-1">{{ $fee->amount_due }}</td>
            <td class="border px-2 py-1">{{ $fee->amount_paid ?? 0 }}</td>
        </tr>
        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
 @push('scripts')
<script>
$(document).ready(function() {
    $('#teachersTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ],
        pageLength: 10,
        responsive: true
    });
});
</script>
@endpush