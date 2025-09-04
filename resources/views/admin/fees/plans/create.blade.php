@extends('layouts.admin')

@section('title', 'Manage Fee Plan')
@section('page-title', 'Manage Fee Plan for ' . $student->user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-lg border-0 rounded-4">
            <!-- Card Header -->
            <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-cash-coin me-2"></i> Yearly Fee Plan for {{ $year }}
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Fee Plan Form --}}
                <form action="{{ route('fees.plans.store', $student->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    
                    <div class="row g-3">
                        @for ($i = 1; $i <= 12; $i++)
                            @php
                                $monthName = \Carbon\Carbon::create()->month($i)->format('F');
                                $amount = $plans[$i]->amount ?? 0;
                            @endphp
                            <div class="col-md-4 col-lg-3">
                                <label for="fee-{{ $i }}" class="form-label fw-semibold">{{ $monthName }}</label>
                                <div class="input-group shadow-sm rounded-3">
                                    <span class="input-group-text">PKR</span>
                                    <input type="number" step="0.01" 
                                           class="form-control rounded-end" 
                                           id="fee-{{ $i }}" 
                                           name="fees[{{ $i }}]" 
                                           value="{{ $amount }}" required>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('fees.plans.index') }}" 
                           class="btn btn-outline-secondary rounded-pill px-4 me-2 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" 
                                class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Save Fee Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
