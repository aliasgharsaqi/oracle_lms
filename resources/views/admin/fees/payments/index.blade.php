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
                        <label for="month-select" class="form-label fw-semibold text-muted">Month</label>
                        <input type="month" class="form-control form-control-lg" id="month-select" name="month"
                            value="{{ $selectedMonth }}" required> {{-- Added an id --}}
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
                            <th>Total Payable (YTD) </th>
                            <th>Total Paid (YTD)</th>
                            <th>Details (YTD)</th>
                            <th>Current Month Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
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
                                        <small class="text-muted">St. Id: {{ 'std-00'.$student->id ?? 'N/A' }}</small>
                                        @if($student->is_defaulter)
                                        <span class="badge bg-dark text-white rounded-pill mt-1 px-2 py-1 small">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Defaulter
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Payable, Paid, Balance Cells --}}
                            <td class="align-middle fw-medium">PKR {{ $student->total_payable  ?? '0.0'}} </td>
                            <td class="align-middle fw-medium">PKR {{ $student->total_paid  ?? '0.0'}} </td>
                            <td class="text-center align-middle">
                                <div class="btn-group actions-btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-dark view-ledger-btn"
                                        data-student-id="{{ $student->id }}"
                                        data-student-name="{{ $student->user->name ?? '' }}"
                                        data-year="{{ \Carbon\Carbon::parse($selectedMonth)->year }}"
                                        data-bs-toggle="tooltip" title="View Fee Ledger">
                                        <i class="bi bi-eye"></i>Ledger
                                    </button>
                                    @if($student->has_plan_for_month)
                                    <a class="btn bg-blue-600 hover:bg-blue-600 text-white" href="{{ route('fees.plans.create', ['student' => $student->id, 'year' => \Carbon\Carbon::parse($selectedMonth)->year]) }}"
                                        class="btn btn-sm btn-light" data-bs-toggle="tooltip"
                                        title="Setup Fee Plan">
                                        <i class="bi bi-gear-fill"></i> See Plan
                                    </a>
                                    @endif
                                </div>
                            </td>
                            {{-- Current Month Status --}}
                            <td class="align-middle">
                                @php
                                $status = optional($student->voucher)->status;
                                $badgeClass = 'secondary';
                                $text = 'Not Collected';
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

                                    {{-- NEW: Check if the plan exists AND if the selected month is the current month --}}
                                    @php
                                        $isCurrentMonthSelected = \Carbon\Carbon::parse($selectedMonth)->isSameMonth(now());
                                        $status = optional($student->voucher)->status;
                                    @endphp

                                    @if($student->has_plan_for_month && $isCurrentMonthSelected || $student->has_plan_for_month && $status != 'partial' && $status != 'paid')
                                    <button class="btn bg-green-700 hover:bg-green-700 text-white collect-fee-btn"
                                        data-student-id="{{ $student->id }}"
                                        data-student-name="{{ $student->user->name ?? 'Student' }}"
                                        data-bs-toggle="tooltip" title="Collect Fee">
                                        <i class="bi bi-cash-stack"></i> Collect
                                    </button>
                                    {{-- Show "Set Plan" if no plan exists for the month, regardless of whether it's the current month --}}
                                    @elseif(!$student->has_plan_for_month)
                                    <a class="btn bg-blue-600 hover:bg-blue-600 text-white" href="{{ route('fees.plans.create', ['student' => $student->id, 'year' => \Carbon\Carbon::parse($selectedMonth)->year]) }}"
                                        class="btn btn-sm btn-light" data-bs-toggle="tooltip"
                                        title="Setup Fee Plan">
                                        <i class="bi bi-gear-fill"></i> Set Plan
                                    </a>
                                    @endif

                                    {{-- Receipt button should show if a voucher *ever* existed for that month --}}
                                    @if($student->voucher)
                                    <a href="{{ route('fees.receipt', $student->voucher->id) }}"
                                        class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                                        title="Download Receipt">
                                        <i class="bi bi-receipt"></i> Receipt
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

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-cash me-2"></i> Collect Fee: <span id="modal_student_name"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('fees.payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="voucher_id" id="modal_voucher_id">
                    <div class="modal-body p-4" id="modal_body_content">

                        {{-- Content will be loaded here by JavaScript --}}
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Generating voucher, please wait...</p>
                        </div>

                    </div>
                    <div class="modal-footer" id="modal_footer_buttons">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="modal_submit_button">Confirm & Print</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ledger Modal -->
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
                            <thead>
                                <tr class="bg-light">
                                    <th>Month</th>
                                    <th>Amount Due</th>
                                    <th>Amount Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Paid On</th>
                                </tr>
                            </thead>
                            <tbody id="ledgerTableBody"></tbody>
                            <tfoot class="fw-bold">
                                <tr class="table-dark">
                                    <td colspan="1" class="text-end">Yearly Totals</td>
                                    <td id="ledgerTotalPayable"></td>
                                    <td id="ledgerTotalPaid"></td>
                                    <td id="ledgerTotalBalance"></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
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
    document.addEventListener('DOMContentLoaded', function() {
        const paymentModals = document.querySelectorAll('[id^="paymentModal-"]');
        paymentModals.forEach(modal => {
            const paymentInputs = modal.querySelectorAll('.payment-input');
            const totalPaidInput = modal.querySelector('.total-paid-input');

            function updateTotal() {
                let total = 0;
                paymentInputs.forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
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
            button.addEventListener('click', function() {
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
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Ledger Modal Script ---
        const ledgerModalElement = document.getElementById('ledgerModal'); // Get element
        const ledgerModal = new bootstrap.Modal(ledgerModalElement); // Init BS Modal
        const ledgerModalTitle = document.getElementById('ledgerModalTitle');
        const ledgerTableBody = document.getElementById('ledgerTableBody');
        const ledgerTotalPayable = document.getElementById('ledgerTotalPayable');
        const ledgerTotalPaid = document.getElementById('ledgerTotalPaid');
        const ledgerTotalBalance = document.getElementById('ledgerTotalBalance');

        document.querySelectorAll('.view-ledger-btn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.dataset.studentId;
                const studentName = this.dataset.studentName;
                const year = this.dataset.year;

                if (!studentId || !studentName || !year) {
                    console.error('Missing data attributes on ledger button for student:', this);
                    alert('Could not load ledger: Missing student information.');
                    return;
                }

                ledgerModalTitle.innerText = `Fee Ledger for ${studentName} (${year})`;
                ledgerTableBody.innerHTML = '<tr><td colspan="6" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading...</td></tr>';
                ledgerTotalPayable.innerText = 'Calculating...'; // Placeholder
                ledgerTotalPaid.innerText = 'Calculating...';
                ledgerTotalBalance.innerText = 'Calculating...';
                ledgerModal.show();

                // Construct URL safely
                const ledgerUrl = `/fees/student-ledger/${encodeURIComponent(studentId)}/${encodeURIComponent(year)}`;

                fetch(ledgerUrl)
                    .then(response => {
                        if (!response.ok) {
                             // Try to get error text, fallback to status
                             return response.text().then(text => {
                                 throw new Error(`HTTP error ${response.status}: ${text || response.statusText}`);
                             });
                        }
                        return response.json();
                    })
                    .then(data => {
                        ledgerTableBody.innerHTML = ''; // Clear loading message
                        if (data.ledger && Array.isArray(data.ledger) && data.ledger.length > 0) {
                            data.ledger.forEach(item => {
                                let rowHtml = ''; // Initialize rowHtml
                                if (item && item.status !== 'not_generated') {
                                    const amountDue = parseFloat(item.amount_due || 0);
                                    const amountPaid = parseFloat(item.amount_paid || 0);
                                    const balance = amountDue - amountPaid;

                                    let statusBadgeClass = 'secondary'; // Default
                                    if (item.status === 'paid') statusBadgeClass = 'success';
                                    else if (item.status === 'partial') statusBadgeClass = 'info';
                                    else if (item.status === 'pending') statusBadgeClass = 'warning';
                                    else if (item.status === 'overdue') statusBadgeClass = 'danger';

                                    rowHtml = `<tr>
                                        <td>${item.month || 'N/A'}</td>
                                        <td>${amountDue.toFixed(2)}</td>
                                        <td>${amountPaid.toFixed(2)}</td>
                                        <td class="${balance > 0.01 ? 'text-danger fw-bold' : ''}">${balance.toFixed(2)}</td>
                                        <td><span class="badge bg-${statusBadgeClass}-subtle text-${statusBadgeClass}-emphasis">${item.status || 'N/A'}</span></td>
                                        <td>${item.paid_on || 'N/A'}</td>
                                    </tr>`;
                                } else if (item && item.month) { // Handle not_generated case
                                    rowHtml = `<tr><td>${item.month}</td><td colspan="5" class="text-center text-muted small fst-italic">Not Generated</td></tr>`;
                                } else {
                                    console.warn('Skipping invalid ledger item:', item); // Log invalid items
                                }
                                if(rowHtml) { // Only append if rowHtml was created
                                    ledgerTableBody.insertAdjacentHTML('beforeend', rowHtml);
                                }
                            });

                            // Validate and format totals
                            const totalPayable = parseFloat(data.totals?.payable || 0); // Use optional chaining
                            const totalPaid = parseFloat(data.totals?.paid || 0);
                            const totalBalance = parseFloat(data.totals?.balance || 0);

                            ledgerTotalPayable.innerText = 'PKR ' + totalPayable.toFixed(2);
                            ledgerTotalPaid.innerText = 'PKR ' + totalPaid.toFixed(2);
                            ledgerTotalBalance.innerText = 'PKR ' + totalBalance.toFixed(2);
                            ledgerTotalBalance.className = totalBalance > 0.01 ? 'text-danger fw-bold' : 'fw-bold'; // Ensure fw-bold always

                        } else {
                            ledgerTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted p-5 fst-italic">No fee records found for this year.</td></tr>';
                        }
                    })
                    .catch(error => {
                        ledgerTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger p-5">Failed to load ledger data. Please try again.</td></tr>';
                        console.error('Ledger Fetch Error:', error);
                        // Clear totals on error
                        ledgerTotalPayable.innerText = 'Error';
                        ledgerTotalPaid.innerText = 'Error';
                        ledgerTotalBalance.innerText = 'Error';
                    });
            });
        });

        // --- Payment Modal Script ---

        const paymentModalElement = document.getElementById('paymentModal');
        const paymentModal = new bootstrap.Modal(paymentModalElement);
        const modalStudentName = document.getElementById('modal_student_name');
        const modalVoucherId = document.getElementById('modal_voucher_id');
        const modalBodyContent = document.getElementById('modal_body_content');
        const modalSubmitButton = document.getElementById('modal_submit_button');
        const ledgerModalInstance = new bootstrap.Modal(document.getElementById('ledgerModal')); // Ensure ledger modal instance is available here too

        document.querySelectorAll('.collect-fee-btn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.dataset.studentId;
                const studentName = this.dataset.studentName;
                const selectedMonthInput = document.querySelector('input[name="month"]');
                const selectedMonth = selectedMonthInput ? selectedMonthInput.value : null;

                if (!studentId || !studentName) {
                     console.error('Missing data attributes on collect button:', this);
                     alert('Cannot collect fee: Missing student information.');
                     return;
                }
                if (!selectedMonth) {
                    alert('Could not determine the selected month. Please ensure the month input exists and has a value.');
                    return;
                }

                // Set initial loading state and show modal
                modalStudentName.innerText = studentName;
                modalBodyContent.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3">Generating voucher for ${studentName} (${selectedMonth})...</p></div>`;
                modalSubmitButton.disabled = true;
                paymentModal.show();

                // Construct URL safely
                const generateVoucherUrl = `/fees/generate-voucher/${encodeURIComponent(studentId)}`;

                // *** CRITICAL FIX: Ensure fetch uses POST ***
                fetch(generateVoucherUrl, {
                        method: 'POST', // Explicitly setting POST
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') // Robust CSRF token retrieval
                        },
                        body: JSON.stringify({
                            month: selectedMonth
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                // Provide more specific error messages
                                let errorMessage = err.error || `HTTP error ${response.status}: ${response.statusText}`;
                                if (response.status === 404) {
                                    errorMessage = `Fee plan not found for ${selectedMonth}. Please set up the plan first.`;
                                } else if (response.status === 403) {
                                     errorMessage = 'Unauthorized action.';
                                }
                                throw new Error(errorMessage);
                            }).catch(() => { // Fallback if error isn't JSON
                                throw new Error(`HTTP error ${response.status}: ${response.statusText}`);
                            });
                        }
                         // Check content type before parsing JSON
                         const contentType = response.headers.get("content-type");
                         if (contentType && contentType.indexOf("application/json") !== -1) {
                             return response.json();
                         } else {
                             // Handle cases where response might not be JSON (e.g., unexpected HTML error page)
                             return response.text().then(text => {
                                 throw new Error("Received non-JSON response from server: " + text.substring(0, 100) + "..."); // Show snippet
                             });
                         }
                    })
                    .then(voucher => {
                        if (!voucher || typeof voucher !== 'object' || !voucher.id) {
                            // Validate the received voucher object
                            console.error('Invalid voucher data received:', voucher);
                            throw new Error('Received invalid voucher data from the server.');
                        }

                        modalVoucherId.value = voucher.id;
                        const year = selectedMonth.substring(0, 4);

                        // Calculate remaining amounts safely
                        let voucherAmountDue = parseFloat(voucher.amount_due || 0);
                        let voucherAmountPaid = parseFloat(voucher.amount_paid || 0);
                        let remainingTuition = Math.max(0, (parseFloat(voucher.tuition_fee || 0)) - (parseFloat(voucher.paid_tuition || 0)));
                        let remainingAdmission = Math.max(0, (parseFloat(voucher.admission_fee || 0)) - (parseFloat(voucher.paid_admission || 0)));
                        let remainingExamination = Math.max(0, (parseFloat(voucher.examination_fee || 0)) - (parseFloat(voucher.paid_examination || 0)));
                        let remainingOther = Math.max(0, (parseFloat(voucher.other_fees || 0)) - (parseFloat(voucher.paid_other || 0)));
                        let remainingArrears = Math.max(0, (parseFloat(voucher.arrears || 0)) - (parseFloat(voucher.paid_arrears || 0)));
                        let remainingTotal = Math.max(0, voucherAmountDue - voucherAmountPaid);

                        // Build Enhanced Modal Content
                        let tableHtml = `<div class="row g-4">
                            <div class="col-lg-7">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2"><i class="bi bi-list-ul me-2"></i>Fee Breakdown (${selectedMonth})</h6>
                                <table class="table table-sm table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-end" style="width: 120px;">Amount Due</th>
                                            <th style="width: 150px;">Amount to Pay Now</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        // Add rows only if the original amount due > 0
                        if (parseFloat(voucher.tuition_fee || 0) > 0) tableHtml += `<tr><td>Tuition Fee</td><td class="text-end">PKR ${parseFloat(voucher.tuition_fee || 0).toFixed(2)}</td><td><input type="number" name="paid_tuition" class="form-control form-control-sm payment-input" value="${remainingTuition.toFixed(2)}" step="0.01" min="0"></td></tr>`;
                        if (parseFloat(voucher.admission_fee || 0) > 0) tableHtml += `<tr><td>Admission Fee</td><td class="text-end">PKR ${parseFloat(voucher.admission_fee || 0).toFixed(2)}</td><td><input type="number" name="paid_admission" class="form-control form-control-sm payment-input" value="${remainingAdmission.toFixed(2)}" step="0.01" min="0"></td></tr>`;
                        if (parseFloat(voucher.examination_fee || 0) > 0) tableHtml += `<tr><td>Examination Fee</td><td class="text-end">PKR ${parseFloat(voucher.examination_fee || 0).toFixed(2)}</td><td><input type="number" name="paid_examination" class="form-control form-control-sm payment-input" value="${remainingExamination.toFixed(2)}" step="0.01" min="0"></td></tr>`;
                        if (parseFloat(voucher.other_fees || 0) > 0) tableHtml += `<tr><td>Other Charges</td><td class="text-end">PKR ${parseFloat(voucher.other_fees || 0).toFixed(2)}</td><td><input type="number" name="paid_other" class="form-control form-control-sm payment-input" value="${remainingOther.toFixed(2)}" step="0.01" min="0"></td></tr>`;
                        if (parseFloat(voucher.arrears || 0) > 0) tableHtml += `<tr><td class="text-danger fw-bold">Arrears</td><td class="text-end text-danger fw-bold">PKR ${parseFloat(voucher.arrears || 0).toFixed(2)}</td><td><input type="number" name="paid_arrears" class="form-control form-control-sm payment-input" value="${remainingArrears.toFixed(2)}" step="0.01" min="0"></td></tr>`;

                        tableHtml += `</tbody></table>
                            </div>
                            <div class="col-lg-5">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2"><i class="bi bi-credit-card me-2"></i>Payment Summary & Details</h6>
                                <div class="alert alert-light border shadow-sm p-3 mb-3">
                                     <div class="d-flex justify-content-between mb-1"><span>Total Due:</span> <strong class="text-primary">PKR ${voucherAmountDue.toFixed(2)}</strong></div>
                                     <div class="d-flex justify-content-between mb-1"><span>Already Paid:</span> <strong class="text-success">PKR ${voucherAmountPaid.toFixed(2)}</strong></div>
                                     <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2"><span>Remaining Balance:</span> <strong class="text-danger">PKR ${remainingTotal.toFixed(2)}</strong></div>
                                </div>
                                 <div class="mb-3">
                                     <label class="form-label fw-semibold small text-muted">Amount Being Paid Now</label>
                                     <input type="number" step="0.01" class="form-control form-control-lg total-paid-input" value="${remainingTotal.toFixed(2)}" required readonly style="background-color: #e9ecef; border: 1px solid #ced4da; font-weight: bold;">
                                 </div>
                                 <div class="mb-3">
                                     <label class="form-label fw-semibold small text-muted">Payment Method</label>
                                     <select class="form-select form-select-lg" name="payment_method" required><option value="Cash" selected>Cash</option><option value="Bank Transfer">Bank Transfer</option><option value="Card">Card</option></select>
                                 </div>
                                 <div class="mb-3">
                                      <label class="form-label fw-semibold small text-muted">Notes (Optional)</label>
                                      <textarea name="notes" class="form-control" rows="2" placeholder="e.g., Paid by father, Check #123"></textarea>
                                 </div>
                                 <div class="d-grid mt-3">
                                    <button type="button" class="btn btn-outline-secondary view-ledger-inside-modal-btn"
                                        data-student-id="${studentId}"
                                        data-student-name="${studentName}"
                                        data-year="${year}">
                                        <i class="bi bi-journal-text me-2"></i>View Full Fee Ledger (${year})
                                    </button>
                                 </div>
                            </div>
                        </div>`;

                        modalBodyContent.innerHTML = tableHtml;

                        // Check status AFTER inserting HTML
                        if (voucher.status === 'paid' || remainingTotal < 0.01) {
                            modalSubmitButton.disabled = true;
                            modalBodyContent.insertAdjacentHTML('beforeend', '<div class="alert alert-success mt-3"><i class="bi bi-check-circle-fill me-2"></i>This voucher is already fully paid.</div>');
                        } else {
                            modalSubmitButton.disabled = false;
                        }

                        // Re-initialize total calculator
                        updateModalTotalCalculator();

                        // Add event listener for the ledger button *inside* the modal
                        const ledgerButtonInsideModal = modalBodyContent.querySelector('.view-ledger-inside-modal-btn');
                        if (ledgerButtonInsideModal) {
                            ledgerButtonInsideModal.addEventListener('click', function() {
                                const ledgerStudentId = this.dataset.studentId;
                                const ledgerStudentName = this.dataset.studentName;
                                const ledgerYear = this.dataset.year;

                                paymentModal.hide(); // Hide payment modal first

                                // Trigger ledger modal manually (reuse existing elements/logic)
                                ledgerModalTitle.innerText = `Fee Ledger for ${ledgerStudentName} (${ledgerYear})`;
                                ledgerTableBody.innerHTML = '<tr><td colspan="6" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading...</td></tr>';
                                ledgerTotalPayable.innerText = '';
                                ledgerTotalPaid.innerText = '';
                                ledgerTotalBalance.innerText = '';
                                ledgerModalInstance.show(); // Show ledger modal

                                const ledgerUrlInner = `/fees/student-ledger/${encodeURIComponent(ledgerStudentId)}/${encodeURIComponent(ledgerYear)}`;
                                fetch(ledgerUrlInner) // Fetch ledger data
                                    .then(response => { if (!response.ok) { throw new Error('Network response was not ok for ledger.'); } return response.json(); })
                                    .then(data => { /* ... populate ledger table ... */ }) // Reuse ledger population logic
                                    .catch(error => { console.error('Ledger Fetch Error (inside modal):', error); /* ... ledger error handling ... */ });
                            });
                        } else {
                             console.warn('Could not find ledger button inside modal to attach listener.');
                        }

                    })
                    .catch(error => {
                        console.error('Voucher Fetch/Processing Error:', error);
                        modalBodyContent.innerHTML = `<div class="alert alert-danger text-center p-4">
                            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i> Error</h5>
                            <p>${error.message || 'An unexpected error occurred.'}</p>
                            <p class="mb-0 small">Please check the fee plan, ensure the server is running, or contact support.</p>
                        </div>`;
                        modalSubmitButton.disabled = true;
                    });
            });
        });

        // Function to wire up the total calculator
        function updateModalTotalCalculator() {
            const currentModal = document.getElementById('paymentModal');
            if (!currentModal) return;

            const paymentInputs = currentModal.querySelectorAll('.payment-input');
            const totalPaidInput = currentModal.querySelector('.total-paid-input');

            if (paymentInputs.length > 0 && totalPaidInput) {
                const updateTotal = () => {
                    let currentTotal = 0;
                    paymentInputs.forEach(input => {
                        currentTotal += parseFloat(input.value) || 0;
                    });
                    totalPaidInput.value = currentTotal.toFixed(2);
                };
                paymentInputs.forEach(input => {
                    input.removeEventListener('input', updateTotal); // Remove previous listener if any
                    input.addEventListener('input', updateTotal);   // Add new listener
                });
                updateTotal(); 
            }
        }

        if(paymentModalElement) { 
             paymentModalElement.addEventListener('shown.bs.modal', updateModalTotalCalculator);
        }

    }); 

    document.addEventListener('DOMContentLoaded', function() {
        const monthInput = document.getElementById('month-select');

        if (monthInput) {
            const now = new Date();
            const year = now.getFullYear();
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const currentMonth = `${year}-${month}`;

            monthInput.max = currentMonth;
            if (!monthInput.value || !/^\d{4}-\d{2}$/.test(monthInput.value)) {
                 const phpDefault = "{{ \Carbon\Carbon::now()->format('Y-m') }}";
                 if (monthInput.value === phpDefault || !monthInput.value) {
                    monthInput.value = currentMonth;
                 }
            }
        }
    });
</script>
@endpush