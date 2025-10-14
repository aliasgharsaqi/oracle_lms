@extends('layouts.admin')

@section('title', 'Manage Fee Plan')
@section('page-title', 'Manage Fee Plan for ' . $student->user->name)

@section('content')
<div class="container-fluid">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-cash-coin me-2"></i> Yearly Fee Plan</h5>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 shadow-sm"><ul class="mb-0">@foreach ($errors->all() as $error)<li><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</li>@endforeach</ul></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger rounded-3 shadow-sm">{{ session('error') }}</div>
            @endif

            <form action="{{ route('fees.plans.store', $student->id) }}" method="POST">
                @csrf
                <div class="row mb-4 align-items-center">
                    <div class="col-md-3">
                        <label for="year" class="form-label fw-semibold">Fee Year</label>
                        <select class="form-select form-select-lg" name="year" id="year">
                            @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ old('year', $year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Fee Sections --}}
                <div class="row g-4">
                    {{-- One-Time & Annual Fees Section --}}
                    <div class="col-lg-4">
                        <div class="card h-100 border-primary border-2 shadow-sm">
                            <div class="card-header bg-primary-soft border-0">
                                <h6 class="fw-bold text-primary mb-0"><i class="bi bi-gem me-2"></i>One-Time & Annual Fees</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label for="admission_fee" class="form-label">Admission Fee (One-Time)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-check-fill text-success"></i></span>
                                        <input type="number" step="0.01" class="form-control fee-component" id="admission_fee" name="admission_fee" value="{{ old('admission_fee', $plan->admission_fee ?? 0) }}" placeholder="e.g., 5000">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="examination_fee" class="form-label">Examination Fee (Annual)</label>
                                    <div class="input-group">
                                         <span class="input-group-text"><i class="bi bi-pencil-fill text-warning"></i></span>
                                        <input type="number" step="0.01" class="form-control fee-component" id="examination_fee" name="examination_fee" value="{{ old('examination_fee', $plan->examination_fee ?? 0) }}" placeholder="e.g., 1500">
                                    </div>
                                     <div class="form-text">Applied in March & September</div>
                                </div>
                                <div>
                                    <label for="other_fees" class="form-label">Other Annual Charges</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-plus-circle-dotted text-info"></i></span>
                                        <input type="number" step="0.01" class="form-control fee-component" id="other_fees" name="other_fees" value="{{ old('other_fees', $plan->other_fees ?? 0) }}" placeholder="e.g., 1000">
                                    </div>
                                    <div class="form-text">Applied in January</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Monthly Tuition Fees Section --}}
                    <div class="col-lg-8">
                         <div class="card h-100 border-light shadow-sm">
                            <div class="card-header bg-light border-0">
                                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-calendar3 me-2"></i>Monthly Tuition Fees</h6>
                            </div>
                            <div class="card-body p-4">
                               <div class="row g-3">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <div class="col-md-6">
                                            <label class="form-label">{{ \Carbon\Carbon::create()->month($i)->format('F') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">PKR</span>
                                                <input type="number" step="0.01" class="form-control tuition-fee-input fee-component" name="tuition_fee[{{ $i }}]" value="{{ old('tuition_fee.'.$i, $monthly_plans[$i]->tuition_fee ?? 0) }}" required>
                                                @if($i == 1)
                                                    <button type="button" id="copy-down-btn" class="btn btn-primary" title="Copy to all months"><i class="bi bi-files me-1"></i> Copy</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endfor
                               </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Totals and Submit --}}
                <div class="mt-4 p-3 bg-light-subtle rounded-4 border-2 border-primary-subtle d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="fw-bold fs-4">
                        Total Yearly Fee: <span id="yearly-total" class="text-success fw-bolder">PKR 0.00</span>
                    </div>
                    <div class="d-flex gap-2 mt-3 mt-md-0">
                        <a href="{{ route('fees.plans.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-gradient-primary rounded-pill px-4">Save Fee Plan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
            yearInput.addEventListener('change', function() {
                window.location.href = "{{ route('fees.plans.create', $student->id) }}?year=" + this.value;
            });
        }
        
        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
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

