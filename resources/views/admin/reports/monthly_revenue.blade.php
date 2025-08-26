@extends('layouts.admin')

@section('title', 'Monthly Revenue Report')
@section('page-title', 'Monthly Revenue Report')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Monthly Revenue Report</h5>
    </div>
    <div class="card-body">
        
        <!-- 🔹 Filter Form -->
        <form method="GET" action="{{ route('reports.monthlyRevenue') }}" class="row mb-3">
            <div class="col-md-3">
                <select name="year" class="form-control">
                    <option value="">Select Year</option>
                    @foreach(range(date('Y'), date('Y')-5) as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="month" class="form-control">
                    <option value="">Select Month</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <!-- 🔹 Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="teachersTable">
                <thead>
                    <tr>
                        <th class="border px-2 py-1">Month</th>
                        <th class="border px-2 py-1">Year</th>
                        <th class="border px-2 py-1">Total Collected</th>
                        <th class="border px-2 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyIncome as $income)
                        <tr>
                            <td class="border px-2 py-1">
                                {{ \Carbon\Carbon::create()->month($income->month)->format('F') }}
                            </td>
                            <td class="border px-2 py-1">{{ $income->year }}</td>
                            <td class="border px-2 py-1">{{ number_format($income->total_collected, 2) }}</td>
                  <td class="border px-2 py-1">
    @if($income->month && $income->year)
        <a href="{{ route('reports.paidFees', ['month' => $income->year . '-' . str_pad($income->month, 2, '0', STR_PAD_LEFT)]) }}" 
           class="btn btn-success btn-sm">
           Paid
        </a>

        <a href="{{ route('reports.pendingFees', ['month' => $income->year . '-' . str_pad($income->month, 2, '0', STR_PAD_LEFT)]) }}" 
           class="btn btn-warning btn-sm">
           Pending
        </a>
    @else
        <span class="text-muted">N/A</span>
    @endif
</td>


                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-2">No revenue data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#teachersTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ],
        pageLength: 10,
        responsive: true
    });
});
</script>
@endpush
