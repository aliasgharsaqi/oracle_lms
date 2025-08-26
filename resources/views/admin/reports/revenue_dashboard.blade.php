@extends('layouts.admin')

@section('title', 'Revenue Dashboard')
@section('page-title', 'Revenue Dashboard')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header flex justify-between items-center">
        <h5 class="card-title mb-0">Revenue Dashboard</h5>
        <!-- Filter Form -->
        <form method="GET" action="{{ route('reports.revenueDashboard') }}" class="flex space-x-2">
            <select name="month" class="form-select">
                <option value="">All Months</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="form-select">
                @foreach(range(now()->year, now()->year - 5) as $y)
                    <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    <div class="card-body">
        <!-- Total Revenue Card -->
        <div class="mb-4 text-center">
            <h4 class="fw-bold text-success">Total Revenue: {{ number_format($totalRevenue, 2) }}</h4>
        </div>

        <!-- Monthly Revenue Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Total Collected</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyIncome as $income)
                        <tr>
                            <td>{{ \Carbon\Carbon::create()->month($income->month)->format('F') }}</td>
                            <td>{{ $income->year }}</td>
                            <td>{{ number_format($income->total_collected, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-2">No revenue data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Optional Chart -->
        <div class="mt-5">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($monthlyIncome->pluck('month')->map(fn($m) => \Carbon\Carbon::create()->month($m)->format('F'))),
            datasets: [{
                label: 'Monthly Revenue',
                data: @json($monthlyIncome->pluck('total_collected')),
                borderWidth: 1,
                backgroundColor: '#28a745',
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
@endpush
