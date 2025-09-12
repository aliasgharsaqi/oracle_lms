@extends('layouts.admin')

@section('title', 'Fee Collection')
@section('page-title', 'Collect Student Fees')

@section('content')
<div class="grid grid-cols-1">
    <!-- Card: Select Class and Month -->
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden mb-6">
        <!-- Card Header -->
        <div class="px-4 py-3 border-b bg-gradient-to-r from-blue-500 to-blue-600 flex justify-between items-center">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-cash-coin"></i> Select Class and Month
            </h4>
        </div>

        <!-- Card Body -->
        <div class="p-4">
            <form method="GET" action="{{ route('fees.payments.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Class -->
                    <div class="md:col-span-2">
                        <label for="class_id" class="block text-sm font-semibold mb-1">Class</label>
                        <select class="w-full rounded-lg border border-blue-500 shadow-sm px-3 py-2 focus:ring focus:ring-blue-300"
                            name="class_id" required>
                            <option value="">-- Select a class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="md:col-span-2">
                        <label for="month" class="block text-sm font-semibold mb-1">Month</label>
                        <input type="month"
                            class="w-full rounded-lg border border-blue-500 shadow-sm px-3 py-2 focus:ring focus:ring-blue-300"
                            name="month" value="{{ $selectedMonth }}" required>
                    </div>

                    <!-- Button -->
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full btn btn-gradient-primary font-bold py-2 px-4 rounded-lg shadow">
                            <i class="bi bi-search me-1"></i> Load
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedClass)
    <!-- Card: Fee Status -->
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div class="px-4 py-3 border-b bg-gradient-to-r from-gray-700 to-gray-900">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-table"></i> Fee Status for {{ $selectedClass->name }} -
                {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}
            </h4>
        </div>

        <!-- Card Body -->
        <div class="p-4">
            @if($students->isEmpty())
                <div class="p-6 text-center text-blue-600">
                    <i class="bi bi-info-circle me-2"></i> There are no students enrolled in this class.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm text-center">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-3">Student Name</th>
                                <th class="px-4 py-3">Amount Due</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Remaining</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-blue-600">{{ $student->user->name ?? '' }}</td>
                                <td class="px-4 py-3 text-indigo-600 font-bold">PKR {{ number_format($student->voucher->amount_due, 2) }}</td>
                                <td class="px-4 py-3">
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
                                <td class="px-4 py-3 font-semibold">
                                    @if ($student->voucher->amount_due > $student->voucher->amount_paid ?? 0 )
                                        {{ $student->voucher->amount_due - $student->voucher->amount_paid ?? 0 }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($student->voucher->status == 'paid')
                                        <a href="{{ route('fees.receipt', $student->voucher->id) }}"
                                            class="btn btn-outline-secondary btn-sm rounded-lg shadow">
                                            <i class="bi bi-receipt me-1"></i> Receipt
                                        </a>
                                    @elseif($student->voucher->status == 'pending')
                                        <button class="btn btn-success btn-sm rounded-lg shadow"
                                            data-bs-toggle="modal"
                                            data-bs-target="#paymentModal-{{ $student->voucher->id }}">
                                            <i class="bi bi-cash-stack me-1"></i> Collect
                                        </button>
                                    @else
                                        <a href="{{ route('fees.plans.create', $student->id) }}"
                                            class="btn btn-info btn-sm rounded-lg shadow text-white">
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
            <div class="modal-content shadow-lg border-0 rounded-2xl">
                <div class="modal-header bg-blue-600 text-white rounded-t-2xl">
                    <h5 class="modal-title font-bold">
                        <i class="bi bi-cash me-2"></i> Collect Fee for {{ $student->user->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('fees.payments.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="voucher_id" value="{{ $student->voucher->id }}">
                        <p><strong>Month:</strong> {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</p>
                        <p><strong>Amount Due:</strong> PKR {{ number_format($student->voucher->amount_due, 2) }}</p>

                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1">Amount Received</label>
                            <input type="number" step="0.01"
                                class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2"
                                name="amount_paid" value="{{ $student->voucher->amount_due }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1">Payment Method</label>
                            <select class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2"
                                name="payment_method" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Card">Card</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer flex justify-end gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-lg px-4 shadow"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-gradient-primary rounded-lg px-4 shadow">
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

@endsection