@extends('layouts.admin')

@section('title', 'Manage Fee Plan')
@section('page-title', 'Manage Fee Plan for ' . $student->user->name)

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h5 class="card-title mb-0">Yearly Fee Plan for {{ $year }}</h5>
    </div>
    <form action="{{ route('fees.plans.store', $student->id) }}" method="POST">
        @csrf
        <input type="hidden" name="year" value="{{ $year }}">
        <div class="card-body">
            <div class="row">
                @for ($i = 1; $i <= 12; $i++)
                    @php
                        $monthName = \Carbon\Carbon::create()->month($i)->format('F');
                        $amount = $plans[$i]->amount ?? 0;
                    @endphp
                    <div class="col-md-4 col-lg-3 mb-3">
                        <label for="fee-{{ $i }}" class="form-label">{{ $monthName }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="fee-{{ $i }}" name="fees[{{ $i }}]" value="{{ $amount }}" required>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('fees.plans.index') }}" class="btn btn-secondary">Back to List</a>
            <button type="submit" class="btn btn-primary">Save Fee Plan</button>
        </div>
    </form>
</div>
@endsection
