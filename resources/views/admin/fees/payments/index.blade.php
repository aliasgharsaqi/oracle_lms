@extends('layouts.admin')

@section('title', 'Fee Collection')
@section('page-title', 'Collect Student Fees')

@section('content')
<div class="container-fluid">
    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="custom-card-header bg-primary text-white rounded-top-4"><h5 class="card-title mb-0 fw-bold"><i class="bi bi-filter-circle-fill me-2"></i>Select Class and Month</h5></div>
        <div class="card-body">
            <form method="GET" action="{{ route('fees.payments.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5"><label for="class_id" class="form-label fw-semibold">Class</label><select class="form-select form-select-lg" name="class_id" required><option value="">-- Select a class --</option>@foreach($classes as $class)<option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>@endforeach</select></div>
                    <div class="col-md-5"><label for="month" class="form-label fw-semibold">Month</label><input type="month" class="form-control form-control-lg" name="month" value="{{ $selectedMonth }}" required></div>
                    <div class="col-md-2 d-grid"><button type="submit" class="btn btn-lg btn-primary"><i class="bi bi-search me-1"></i> Load Students</button></div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedClass)
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-light border-0 py-3"><h5 class="mb-0 fw-bold">Fee Status for {{ $selectedClass->name }} - {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">Student Name</th>
                            <th class="py-3 px-4">Total Payable (YTD)</th>
                            <th class="py-3 px-4">Total Paid (YTD)</th>
                            <th class="py-3 px-4">Total Remaining (YTD)</th>
                            <th class="py-3 px-4">Current Month Status</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="{{ $student->total_remaining > 0.01 ? 'table-danger' : 'table-success' }}">
                            <td class="py-3 px-4 fw-bold align-middle">
                                {{ $student->user->name ?? '' }}
                                @if($student->is_defaulter)
                                    <span class="badge bg-danger ms-2">Defaulter</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 align-middle">PKR {{ number_format($student->total_payable, 2) }}</td>
                            <td class="py-3 px-4 align-middle">PKR {{ number_format($student->total_paid, 2) }}</td>
                            <td class="py-3 px-4 fw-bold align-middle">PKR {{ number_format($student->total_remaining, 2) }}</td>
                            <td class="py-3 px-4 align-middle">
                                @if(optional($student->voucher)->status == 'paid') <span class="badge bg-success-soft text-success">Paid</span>
                                @elseif(optional($student->voucher)->status == 'pending') <span class="badge bg-warning-soft text-warning">Pending</span>
                                @elseif(optional($student->voucher)->status == 'partial') <span class="badge bg-info-soft text-info">Partial</span>
                                @else <span class="badge bg-secondary-soft text-secondary">No Plan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center align-middle">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-info view-ledger-btn" data-student-id="{{ $student->id }}" data-student-name="{{ $student->user->name ?? '' }}" data-year="{{ \Carbon\Carbon::parse($selectedMonth)->year }}">Ledger</button>
                                    @if(in_array(optional($student->voucher)->status, ['paid', 'partial']))
                                        <a href="{{ route('fees.receipt', $student->voucher->id) }}" class="btn btn-sm btn-outline-secondary">Receipt</a>
                                    @elseif(in_array(optional($student->voucher)->status, ['pending', 'overdue']))
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal-{{ $student->voucher->id }}">Collect</button>
                                    @else
                                        <a href="{{ route('fees.plans.create', ['student' => $student->id, 'year' => \Carbon\Carbon::parse($selectedMonth)->year]) }}" class="btn btn-sm btn-info text-white">Set Plan</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted p-5">No students found for this class.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach($students as $student)
    @if(isset($student->voucher->id))
    <div class="modal fade" id="paymentModal-{{ $student->voucher->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-cash me-2"></i> Collect Fee: {{ $student->user->name ?? '' }}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <form action="{{ route('fees.payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="voucher_id" value="{{ $student->voucher->id }}">
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-7">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3">Fee Breakdown ({{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }})</h6>
                                <table class="table">
                                    <thead><tr><th>Description</th><th class="text-end">Due</th><th>Paid</th></tr></thead>
                                    <tbody>
                                        @if($student->voucher->tuition_fee > 0)<tr><td>Tuition Fee</td><td class="text-end">PKR {{number_format($student->voucher->tuition_fee, 2)}}</td><td><input type="number" name="paid_tuition" class="form-control form-control-sm payment-input" value="{{$student->voucher->tuition_fee}}" step="0.01"></td></tr>@endif
                                        @if($student->voucher->admission_fee > 0)<tr><td>Admission Fee</td><td class="text-end">PKR {{number_format($student->voucher->admission_fee, 2)}}</td><td><input type="number" name="paid_admission" class="form-control form-control-sm payment-input" value="{{$student->voucher->admission_fee}}" step="0.01"></td></tr>@endif
                                        @if($student->voucher->examination_fee > 0)<tr><td>Examination Fee</td><td class="text-end">PKR {{number_format($student->voucher->examination_fee, 2)}}</td><td><input type="number" name="paid_examination" class="form-control form-control-sm payment-input" value="{{$student->voucher->examination_fee}}" step="0.01"></td></tr>@endif
                                        @if($student->voucher->other_fees > 0)<tr><td>Other Charges</td><td class="text-end">PKR {{number_format($student->voucher->other_fees, 2)}}</td><td><input type="number" name="paid_other" class="form-control form-control-sm payment-input" value="{{$student->voucher->other_fees}}" step="0.01"></td></tr>@endif
                                        @if($student->voucher->arrears > 0)<tr><td class="text-danger">Arrears</td><td class="text-end text-danger">PKR {{number_format($student->voucher->arrears, 2)}}</td><td><input type="number" name="paid_arrears" class="form-control form-control-sm payment-input" value="{{$student->voucher->arrears}}" step="0.01"></td></tr>@endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-5">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3">Payment Details</h6>
                                <p class="fs-5"><strong>Total Due:</strong> <span class="fw-bold text-danger">PKR {{ number_format($student->voucher->amount_due, 2) }}</span></p>
                                <hr>
                                <div class="mb-3"><label class="form-label fw-semibold">Total Amount Received</label><input type="number" step="0.01" class="form-control form-control-lg total-paid-input" value="{{ $student->voucher->amount_due }}" required readonly></div>
                                <div class="mb-3"><label class="form-label fw-semibold">Payment Method</label><select class="form-select form-select-lg" name="payment_method" required><option value="Cash">Cash</option><option value="Bank Transfer">Bank Transfer</option><option value="Card">Card</option></select></div>
                                <div class="mb-3"><label class="form-label fw-semibold">Notes (Optional)</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary rounded-pill px-4">Confirm & Print</button></div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    <div class="modal fade" id="ledgerModal" tabindex="-1">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title" id="ledgerModalTitle"></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr class="bg-light"><th>Month</th><th>Amount Due</th><th>Amount Paid</th><th>Balance</th><th>Status</th><th>Paid On</th></tr></thead>
                    <tbody id="ledgerTableBody"></tbody>
                    <tfoot class="fw-bold"><tr class="table-dark"><td colspan="1" class="text-end">Yearly Totals</td><td id="ledgerTotalPayable"></td><td id="ledgerTotalPaid"></td><td id="ledgerTotalBalance"></td><td colspan="2"></td></tr></tfoot>
                </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paymentModals = document.querySelectorAll('[id^="paymentModal-"]');
    paymentModals.forEach(modal => {
        const paymentInputs = modal.querySelectorAll('.payment-input');
        const totalPaidInput = modal.querySelector('.total-paid-input');
        function updateTotal() {
            let total = 0;
            paymentInputs.forEach(input => { total += parseFloat(input.value) || 0; });
            totalPaidInput.value = total.toFixed(2);
        }
        paymentInputs.forEach(input => input.addEventListener('input', updateTotal));
    });

    const ledgerModal = new bootstrap.Modal(document.getElementById('ledgerModal'));
    const ledgerModalTitle = document.getElementById('ledgerModalTitle');
    const ledgerTableBody = document.getElementById('ledgerTableBody');
    const ledgerTotalPayable = document.getElementById('ledgerTotalPayable');
    const ledgerTotalPaid = document.getElementById('ledgerTotalPaid');
    const ledgerTotalBalance = document.getElementById('ledgerTotalBalance');

    document.querySelectorAll('.view-ledger-btn').forEach(button => {
        button.addEventListener('click', function () {
            const studentId = this.dataset.studentId;
            const studentName = this.dataset.studentName;
            const year = this.dataset.year;

            ledgerModalTitle.innerText = `Fee Ledger for ${studentName} (${year})`;
            ledgerTableBody.innerHTML = '<tr><td colspan="6" class="text-center p-5">Loading...</td></tr>';
            ledgerModal.show();

            fetch(`/fees/student-ledger/${studentId}/${year}`)
                .then(response => response.json())
                .then(data => {
                    ledgerTableBody.innerHTML = '';
                    if (data.ledger && data.ledger.length > 0) {
                        data.ledger.forEach(item => {
                            let rowHtml;
                            if (item.status !== 'not_generated') {
                                const balance = item.amount_due - (item.amount_paid || 0);
                                rowHtml = `<tr>
                                    <td>${item.month}</td>
                                    <td>${parseFloat(item.amount_due).toFixed(2)}</td>
                                    <td>${parseFloat(item.amount_paid || 0).toFixed(2)}</td>
                                    <td class="${balance > 0.01 ? 'text-danger fw-bold' : ''}">${balance.toFixed(2)}</td>
                                    <td><span class="badge bg-${item.status === 'paid' ? 'success' : (item.status === 'partial' ? 'info' : 'warning')}">${item.status}</span></td>
                                    <td>${item.paid_on}</td>
                                </tr>`;
                            } else {
                                rowHtml = `<tr><td>${item.month}</td><td colspan="5" class="text-center text-muted small">Not Generated</td></tr>`;
                            }
                            ledgerTableBody.insertAdjacentHTML('beforeend', rowHtml);
                        });
                        
                        ledgerTotalPayable.innerText = 'PKR ' + parseFloat(data.totals.payable).toFixed(2);
                        ledgerTotalPaid.innerText = 'PKR ' + parseFloat(data.totals.paid).toFixed(2);
                        ledgerTotalBalance.innerText = 'PKR ' + parseFloat(data.totals.balance).toFixed(2);
                        ledgerTotalBalance.className = data.totals.balance > 0.01 ? 'text-danger' : '';
                        
                    } else {
                        ledgerTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted p-5">No fee records found for this year.</td></tr>';
                    }
                })
                .catch(error => {
                    ledgerTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger p-5">Failed to load data. Please try again.</td></tr>';
                    console.error('Error:', error);
                });
        });
    });
});
</script>
@endpush