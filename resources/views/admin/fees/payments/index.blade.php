@extends('layouts.admin')

@section('title', 'Fee Collection')
@section('page-title', 'Collect Student Fees')

@section('content')
<div class="row justify-content-center">
    <div class="">
        <div class="card shadow-lg border-0 rounded-4">
            <!-- Card Header -->
            <div
                class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-cash-coin me-2"></i> Select Class and Month
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                <form method="GET" action="{{ route('fees.payments.index') }}">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="class_id" class="form-label fw-semibold">Class</label>
                            <select class="form-select rounded-3 shadow-sm" name="class_id" required>
                                <option value="">-- Select a class --</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label for="month" class="form-label fw-semibold">Month</label>
                            <input type="month" class="form-control rounded-3 shadow-sm" name="month"
                                value="{{ $selectedMonth }}" required>
                        </div>

                        <div class="col-md-2 px-3" style="margin-top: 50px;">
                            <button type="submit" class="btn w-100 rounded-pill shadow-sm border-0 
                            text-white fw-semibold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                transition: all 0.3s ease;">
                                <i class="bi bi-search me-1"></i> Load
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedClass)
        <div class="card shadow-lg border-0 rounded-4 mt-4">
            <!-- Card Header -->
            <div class="custom-card-header bg-dark text-white rounded-top-4">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-table me-2"></i> Fee Status for {{ $selectedClass->name }} -
                    {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                @if($students->isEmpty())
                <div class="alert alert-info rounded-3 shadow-sm">
                    <i class="bi bi-info-circle me-2"></i> There are no students enrolled in this class.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-primary">
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
                                <td class="fw-semibold">{{ $student->user->name ?? '' }}</td>
                                <td class="text-primary fw-bold">PKR
                                    {{ number_format($student->voucher->amount_due, 2) }}</td>
                                <td>
                                    @if($student->voucher->status == 'paid')
                                    <span class="badge bg-success px-3 py-2">Paid</span>
                                    @elseif($student->voucher->status == 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                                    @elseif($student->voucher->status == 'no_plan')
                                    <span class="badge bg-secondary px-3 py-2">No Plan</span>
                                    @else
                                    <span class="badge bg-danger px-3 py-2">Overdue</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">
                                    @if ($student->voucher->amount_due > $student->voucher->amount_paid ?? 0 )
                                    {{ $student->voucher->amount_due - $student->voucher->amount_paid ?? 0 }}
                                    @else
                                    0
                                    @endif
                                </td>
                                <td>
                                    @if($student->voucher->status == 'paid')
                                    <a href="{{ route('fees.receipt', $student->voucher->id) }}"
                                        class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-receipt me-1"></i> Receipt
                                    </a>
                                    @elseif($student->voucher->status == 'pending')
                                    <button class="btn btn-success btn-sm rounded-pill px-3 shadow-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#paymentModal-{{ $student->voucher->id }}">
                                        <i class="bi bi-cash-stack me-1"></i> Collect
                                    </button>
                                    @else
                                    <a href="{{ route('fees.plans.create', $student->id) }}"
                                        class="btn btn-info btn-sm rounded-pill px-3 shadow-sm text-white">
                                        <i class="bi bi-gear me-1"></i> Set Plan
                                    </a>
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
        <div class="modal fade" id="paymentModal-{{ $student->voucher->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-cash me-2"></i> Collect Fee for {{ $student->user->name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('fees.payments.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <input type="hidden" name="voucher_id" value="{{ $student->voucher->id }}">
                            <p><strong>Month:</strong> {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</p>
                            <p><strong>Amount Due:</strong> PKR {{ number_format($student->voucher->amount_due, 2) }}
                            </p>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Amount Received</label>
                                <input type="number" step="0.01" class="form-control rounded-3 shadow-sm"
                                    name="amount_paid" value="{{ $student->voucher->amount_due }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Payment Method</label>
                                <select class="form-select rounded-3 shadow-sm" name="payment_method" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Card">Card</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm"
                                data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Confirm & Print
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @endforeach
        @endif
    </div>
</div>
@endsection