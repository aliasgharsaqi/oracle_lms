@extends('layouts.admin')

@section('title', 'Fee Collection')
@section('page-title', 'Collect Student Fees')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Select Class and Month</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('fees.payments.index') }}">
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

@if($selectedClass)
    <div class="card shadow">
        <div class="card-header">
            <h5 class="card-title mb-0">Fee Status for {{ $selectedClass->name }} - {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</h5>
        </div>
        <div class="card-body">
            @if($students->isEmpty())
                 <div class="alert alert-info">
                    There are no students enrolled in this class.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Student Name</th>
                                <th>Amount Due</th>
                                <th>Status</th>
                                <th>Remaining</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->user->name }}</td>
                                    <td>PKR{{ number_format($student->voucher->amount_due, 2) }}</td>
                                    <td>
                                        @if($student->voucher->status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($student->voucher->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($student->voucher->status == 'no_plan')
                                            <span class="badge bg-secondary">No Plan Defined</span>
                                        @else
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif
                                    </td>
                                    <td>@if ($student->voucher->amount_due > $student->voucher->amount_paid )
                                        {{ $student->voucher->amount_due - $student->voucher->amount_paid }}
                                    @else
                                        0
                                    @endif</td>
                                    <td>
                                        @if($student->voucher->status == 'paid')
                                            <a href="{{ route('fees.receipt', $student->voucher->id) }}" class="btn btn-sm btn-secondary">View Receipt</a>
                                        @elseif($student->voucher->status == 'pending')
                                             <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal-{{ $student->voucher->id }}">
                                                Collect Fee
                                            </button>
                                        @else
                                            <a href="{{ route('fees.plans.create', $student->id) }}" class="btn btn-sm btn-info">Set Fee Plan</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Payment Modals -->
    @foreach($students as $student)
        @if(isset($student->voucher->id))
            <div class="modal fade" id="paymentModal-{{ $student->voucher->id }}" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel">Collect Fee for {{ $student->user->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('fees.payments.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="voucher_id" value="{{ $student->voucher->id }}">
                                <p><strong>Month:</strong> {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</p>
                                <p><strong>Amount Due:</strong> ${{ number_format($student->voucher->amount_due, 2) }}</p>
                                
                                <div class="mb-3">
                                    <label for="amount_paid" class="form-label">Amount Received</label>
                                    <input type="number" step="0.01" class="form-control" name="amount_paid" value="{{ $student->voucher->amount_due }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select" name="payment_method" required>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Card">Card</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Confirm Payment & Print Receipt</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection
