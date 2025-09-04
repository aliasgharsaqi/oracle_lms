@extends('layouts.admin')

@section('title', 'Total Revenue')
@section('page-title', 'Total Revenue Summary')

@section('content')
<div class="row justify-content-center">
    <div class="mb-10">
        <div class="card shadow-lg border-0 rounded-4">
            <!-- Card Header -->
            <div
                class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-cash-stack me-2"></i> Total Revenue Collected
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body text-center p-5">
                <h2 class="fw-bold text-success mb-3">
                    <i class="bi bi-currency-dollar me-2"></i> {{ number_format($totalRevenue, 2) }}
                </h2>
                <p class="text-muted fs-5">
                    This is the total revenue collected from all paid student fees.
                </p>
            </div>
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
