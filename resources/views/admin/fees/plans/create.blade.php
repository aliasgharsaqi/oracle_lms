@extends('layouts.admin')

@section('title', 'Manage Fee Plan')
@section('page-title', 'Fee Plan for ' . $student->user->name)
<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .bg-gradient {
        background: linear-gradient(135deg, #007bff 0%, #0069d9 100%);
    }
</style>
@section('content')
    <div class="container-fluid py-3">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header -->
            <div
                class="custom-card-header bg-gradient bg-primary text-white py-3 px-4 d-flex align-items-center justify-content-between">
                <h4 class="mb-0 fw-bold">
                    <i class="bi bi-cash-coin me-2"></i> Yearly Fee Plan
                </h4>
            </div>

            <div class="card-body p-5 bg-light">
                {{-- Validation Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger rounded-3 shadow-sm">{{ session('error') }}</div>
                @endif

                <!-- Form -->
                <form action="{{ route('fees.plans.store', $student->id) }}" method="POST">
                    @csrf

                    <!-- Year Selection -->
                    <div class="row mb-5 align-items-center">
                        <div class="col-md-3">
                            <label for="year" class="form-label fw-semibold text-primary">Select Fee Year</label>
                            <select class="form-select form-select-lg border-primary shadow-sm" name="year" id="year">
                                @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                    <option value="{{ $y }}" {{ old('year', $year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Fee Sections -->
                    <div class="row g-4">
                        <!-- One-Time & Annual Fees -->
                        <div class="col-lg-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow rounded-4">
                                <div class="card-header bg-gradient bg-primary text-white rounded-top-4">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-gem me-2"></i>One-Time & Annual Fees</h6>
                                </div>
                                <div class="card-body p-4 bg-white rounded-bottom-4">
                                    <div class="mb-3">
                                        <label for="admission_fee" class="form-label fw-semibold">Admission Fee
                                            (One-Time)</label>
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-success text-white"><i
                                                    class="bi bi-person-check-fill"></i></span>
                                            <input type="number" step="0.01" class="form-control fee-component"
                                                id="admission_fee" name="admission_fee"
                                                value="{{ old('admission_fee', $plan->admission_fee ?? 0) }}"
                                                placeholder="e.g., 5000">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="examination_fee" class="form-label fw-semibold">Examination Fee
                                            (Annual)</label>
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-warning text-white"><i
                                                    class="bi bi-pencil-fill"></i></span>
                                            <input type="number" step="0.01" class="form-control fee-component"
                                                id="examination_fee" name="examination_fee"
                                                value="{{ old('examination_fee', $plan->examination_fee ?? 0) }}"
                                                placeholder="e.g., 1500">
                                        </div>
                                        <div class="form-text text-muted">Applied in March & September</div>
                                    </div>

                                    <div>
                                        <label for="other_fees" class="form-label fw-semibold">Other Annual Charges</label>
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-info text-white"><i
                                                    class="bi bi-plus-circle-dotted"></i></span>
                                            <input type="number" step="0.01" class="form-control fee-component"
                                                id="other_fees" name="other_fees"
                                                value="{{ old('other_fees', $plan->other_fees ?? 0) }}"
                                                placeholder="e.g., 1000">
                                        </div>
                                        <div class="form-text text-muted">Applied in January</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Tuition Fees -->
                        <div class="col-lg-8">
                            <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow">
                                <div class="card-header bg-light text-dark rounded-top-4 border-bottom">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2"></i>Monthly Tuition Fees</h6>
                                </div>
                                <div class="card-body p-4 bg-white rounded-bottom-4">
                                    <div class="row g-3">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <div class="col-md-6">
                                                <label
                                                    class="form-label fw-semibold text-secondary">{{ \Carbon\Carbon::create()->month($i)->format('F') }}</label>
                                                <div class="input-group shadow-sm">
                                                    <span class="input-group-text bg-light fw-bold">PKR</span>
                                                    <input type="number" step="0.01"
                                                        class="form-control tuition-fee-input fee-component"
                                                        name="tuition_fee[{{ $i }}]"
                                                        value="{{ old('tuition_fee.' . $i, $monthly_plans[$i]->tuition_fee ?? 0) }}"
                                                        required>
                                                    @if($i == 1)
                                                        <button type="button" id="copy-down-btn" class="btn btn-outline-primary"
                                                            title="Copy to all months">
                                                            <i class="bi bi-files me-1"></i> Apply to all
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Totals & Actions -->
                    <div class="mt-5 p-4 bg-gradient bg-primary text-white rounded-4 shadow-sm 
                        d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                        <div class="fw-bold fs-4 text-center text-md-start mb-3 mb-md-0">
                            Total Yearly Fee: <span id="yearly-total" class="fw-bolder">PKR 0.00</span>
                        </div>

                        <div class="sm:pl-[50%]  gap-2 w-100 w-sm-auto">
                            <a href="{{ route('fees.plans.index') }}"
                                class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-light text-primary fw-semibold rounded-pill px-4">
                                <i class="bi bi-save2-fill me-1"></i> Save Fee Plan
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const yearInput = document.getElementById('year');
            const allFeeInputs = document.querySelectorAll('.fee-component');
            const copyBtn = document.getElementById('copy-down-btn');

            function calculateTotals() {
                let yearlyTotal = 0;
                const admissionFee = parseFloat(document.getElementById('admission_fee').value) || 0;
                const otherFees = parseFloat(document.getElementById('other_fees').value) || 0;
                const examinationFee = parseFloat(document.getElementById('examination_fee').value) || 0;

                for (let i = 1; i <= 12; i++) {
                    const tuition = parseFloat(document.querySelector(`input[name="tuition_fee[${i}]"]`).value) || 0;

                    let monthTotal = tuition;
                    if (i === 1) monthTotal += admissionFee + otherFees;
                    if ([3, 9].includes(i)) monthTotal += examinationFee;

                    yearlyTotal += monthTotal;
                }
                document.getElementById('yearly-total').innerText = 'PKR ' + yearlyTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            if (yearInput) {
                yearInput.addEventListener('change', function () {
                    window.location.href = "{{ route('fees.plans.create', $student->id) }}?year=" + this.value;
                });
            }

            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    const firstMonthFee = document.querySelector('input[name="tuition_fee[1]"]').value;
                    document.querySelectorAll('.tuition-fee-input').forEach(input => {
                        input.value = firstMonthFee;
                    });
                    calculateTotals();
                });
            }

            allFeeInputs.forEach(input => {
                input.addEventListener('input', calculateTotals);
            });

            // Initial calculation on page load
            calculateTotals();
        });
    </script>
@endpush