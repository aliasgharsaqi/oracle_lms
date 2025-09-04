@extends('layouts.admin')

@section('title', 'Paid Fee')
@section('page-title', 'All Paid Student')

@section('content')
<div class="row justify-content-center">
    <div class="mb-10">
        <div class="card shadow-lg border-0 rounded-4">
            <!-- Card Header -->
            <div
                class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-funnel me-2"></i> Select Class and Month
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.paidFees') }}" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <!-- Class -->
                        <div class="col-md-6">
                            <label for="class_id" class="form-label fw-semibold">Class</label>
                            <select class="form-select rounded-3 shadow-sm" name="class_id" required>
                                <option value="">Choose...</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month -->
                        <div class="col-md-6">
                            <label for="month" class="form-label fw-semibold">Month</label>
                            <input type="month" class="form-control rounded-3 shadow-sm" name="month"
                                value="{{ $selectedMonth }}" required>
                        </div>
                        <!-- Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-search me-1"></i> Load Students
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">

             <div
                class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-funnel me-2"></i>All Paid / Student
                </h5>
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
                                <td class="border px-2 py-1">
                                    {{ \Carbon\Carbon::parse($fee->voucher_month)->format('F Y') }}</td>
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