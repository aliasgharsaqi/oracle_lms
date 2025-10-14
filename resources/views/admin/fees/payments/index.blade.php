@extends('layouts.admin')

@section('title', 'Fee Collection')
@section('page-title', 'Collect Student Fees')

@push('styles')
    <style>
        /* --- Professional Theme Variables --- */
        :root {
            --custom-primary: #007bff;
            /* A classic, professional blue */
            --custom-primary-dark: #0056b3;
            --custom-light-gray: #f8f9fa;
            --custom-border-color: #dee2e6;
            --custom-text-muted: #6c757d;
        }

        /* --- General Enhancements --- */
        body {
            background-color: #f4f7f9;
            /* A slightly off-white background */
        }

        .card {
            border: none;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        }

        /* --- Button Styles --- */
        .btn-primary {
            background-color: var(--custom-primary);
            border-color: var(--custom-primary);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--custom-primary-dark);
            border-color: var(--custom-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.25);
        }

        /* --- Professional Table Header --- */
        .table-header-professional th {
            background-color: var(--custom-light-gray);
            color: #343a40;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--custom-primary);
            border-top: none;
        }

        /* --- Table Body Styling --- */
        .table td,
        .table th {
            padding: 1rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.04);
        }

        .table-danger-light {
            --bs-table-bg: rgba(220, 53, 69, 0.07);
            --bs-table-border-color: rgba(220, 53, 69, 0.15);
        }

        .table-success-light {
            --bs-table-bg: rgba(25, 135, 84, 0.05);
            --bs-table-border-color: rgba(25, 135, 84, 0.1);
        }

        /* --- Student Avatar --- */
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--custom-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* --- Responsive Adjustments --- */
        @media (max-width: 767px) {
            .actions-btn-group {
                display: grid;
                gap: 0.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm mb-5">
            <div class="custom-card-header text-white bg-primary border-bottom-0 py-3">
                <h5 class="mb-0 fw-bold d-flex align-items-center">
                    <i class="bi bi-filter-circle-fill me-2 fs-4 "></i>
                    Select Criteria to Load Students
                </h5>
            </div>
            <div class="card-body p-4 pt-3">
                <form method="GET" action="{{ route('fees.payments.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-lg">
                            <label for="class_id" class="form-label fw-semibold text-muted">Class</label>
                            <select class="form-select form-select-lg" name="class_id" required>
                                <option value="" selected disabled>-- Select a class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-lg">
                            <label for="month" class="form-label fw-semibold text-muted">Month</label>
                            <input type="month" class="form-control form-control-lg" name="month"
                                value="{{ $selectedMonth }}" required>
                        </div>

                        <div class="col-12 col-lg-auto d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold rounded-3 px-4">
                                <i class="bi bi-search me-2"></i> Load Students
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedClass)
            <div class="card shadow-sm rounded-4 mb-5">
                <div class="custom-card-header text-white bg-primary border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-bold">
                        Fee Status for <span class="">{{ $selectedClass->name }}</span>
                        <span class="fw-normal mx-2">|</span>
                        {{ \Carbon\Carbon::parse($selectedMonth)->format('F, Y') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-header-professional">
                                <tr>
                                    <th class="px-4" style="min-width: 250px;">Student Name</th>
                                    <th>Total Payable (YTD)</th>
                                    <th>Total Paid (YTD)</th>
                                    <th>Balance (YTD)</th>
                                    <th>Current Month Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                         <tbody>
    @forelse($students as $student)
        {{-- Yahan class 'table-danger' mein badal di gayi hai --}}
        <tr class="{{ $student->total_remaining > 0.01 ? 'table-danger' : 'table-success-light' }}">

            {{-- Student Name Cell --}}
            <td class="px-4 py-3 align-middle">
                <div class="d-flex align-items-center">
                    @php
                        $name = $student->user->name ?? 'N A';
                        $words = explode(" ", $name);
                        $initials = (isset($words[0][0]) ? strtoupper($words[0][0]) : '') . (count($words) > 1 && isset($words[count($words) - 1][0]) ? strtoupper($words[count($words) - 1][0]) : '');
                    @endphp

                    {{-- Avatar --}}
                    <div class="student-avatar me-3 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold shadow-sm"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, {{ $student->total_remaining > 0.01 ? '#c82333' : '#007bff' }}, {{ $student->total_remaining > 0.01 ? '#dc3545' : '#6610f2' }}); font-size: 1rem;">
                        {{ $initials }}
                    </div>

                    {{-- Info --}}
                    <div class="d-flex flex-column">
                        <span class="fw-semibold">{{ $name }}</span>
                        <small class="text-muted">Roll No: {{ $student->roll_number ?? 'N/A' }}</small>
                        @if($student->is_defaulter)
                            <span class="badge bg-dark text-white rounded-pill mt-1 px-2 py-1 small">
                                <i class="bi bi-exclamation-triangle me-1"></i> Defaulter
                            </span>
                        @endif
                    </div>
                </div>
            </td>

            {{-- Payable, Paid, Balance Cells --}}
            <td class="align-middle fw-medium">PKR {{ number_format($student->total_payable, 2) }}</td>
            <td class="align-middle fw-medium">PKR {{ number_format($student->total_paid, 2) }}</td>
            <td class="align-middle fw-bolder">
                PKR {{ number_format($student->total_remaining, 2) }}
            </td>

            {{-- Current Month Status --}}
            <td class="align-middle">
                @php
                    $status = optional($student->voucher)->status;
                    $badgeClass = 'secondary';
                    $text = 'No Plan';
                    if ($status == 'paid') { $badgeClass = 'success'; $text = 'Paid'; }
                    elseif ($status == 'pending') { $badgeClass = 'warning'; $text = 'Pending'; }
                    elseif ($status == 'partial') { $badgeClass = 'info'; $text = 'Partial'; }
                    elseif ($status == 'overdue') { $badgeClass = 'danger'; $text = 'Overdue'; }
                @endphp
                <span class="badge fs-6 rounded-pill bg-{{$badgeClass}}-subtle text-{{$badgeClass}}-emphasis fw-semibold">{{ $text }}</span>
            </td>

            {{-- Actions --}}
            <td class="text-center align-middle">
                <div class="btn-group actions-btn-group">
                    <button type="button" class="btn btn-sm btn-outline-dark view-ledger-btn"
                        data-student-id="{{ $student->id }}"
                        data-student-name="{{ $student->user->name ?? '' }}"
                        data-year="{{ \Carbon\Carbon::parse($selectedMonth)->year }}"
                        data-bs-toggle="tooltip" title="View Fee Ledger">
                        <i class="bi bi-journal-text"></i> Ledger
                    </button>
                    {{-- Other buttons... --}}
                     @if(in_array($status, ['paid', 'partial']))
                        <a href="{{ route('fees.receipt', $student->voucher->id) }}"
                           class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                           title="Download Receipt">
                            <i class="bi bi-receipt"></i> Receipt
                        </a>
                    @elseif(in_array($status, ['pending', 'overdue']))
                        <button class="btn bg-green-700 hover:bg-green-700 text-white" data-bs-toggle="modal"
                                data-bs-target="#paymentModal-{{ $student->voucher->id }}"
                                data-bs-toggle="tooltip" title="Collect Fee">
                            <i class="bi bi-cash-stack"></i> Collect
                        </button>
                    @else
                        <a class="btn bg-blue-600 hover:bg-blue-600 text-white" href="{{ route('fees.plans.create', ['student' => $student->id, 'year' => \Carbon\Carbon::parse($selectedMonth)->year]) }}"
                           class="btn btn-sm btn-light" data-bs-toggle="tooltip"
                           title="Setup Fee Plan">
                            <i class="bi bi-gear-fill"></i> Set Plan
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center py-5">
                <div class="py-4">
                    <i class="bi bi-people-fill display-4 text-muted"></i>
                    <h5 class="mt-3">No Students Found</h5>
                    <p class="text-muted">There are no students enrolled in the selected class.</p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

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

        <div class="modal fade" id="ledgerModal" tabindex="-1" aria-labelledby="ledgerModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white border-0">
                        <h5 class="modal-title" id="ledgerModalTitle">Fee Ledger</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div id="ledger-content">
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div> @endsection

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