@extends('layouts.admin')

@section('title', 'Total Revenue')
@section('page-title', 'Total Revenue Summary')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Total Revenue Collected</h5>
    </div>
    <div class="card-body text-center">
        <h3 class="fw-bold text-success">
            {{ number_format($totalRevenue, 2) }}
        </h3>
        <p class="text-muted">This is the total revenue collected from all paid student fees.</p>
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