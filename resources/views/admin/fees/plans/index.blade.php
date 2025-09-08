@extends('layouts.admin')

@section('title', 'Student Fee Plans')
@section('page-title', 'Student Fee Plans')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card shadow-lg border-0">
            <!-- Card Header -->
            <div class="card-header custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0 text-primary-custom fw-bold">
                    <i class="bi bi-cash-coin me-2"></i> Manage Student Fee Plans
                </h4>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Success Message -->
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover custom-table align-middle" id="feePlansTable">
                        <thead class="table-header text-nowrap">
                            <tr>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td class="fw-semibold">{{ $student->user->name }}</td>
                                <td>{{ $student->schoolClass->name ?? 'N/A' }}</td>
                                <td>
                                    @if($student->fee_plans_count > 0)
                                    <span class="badge badge-gradient-success px-3 py-2">Plan Defined</span>
                                    @else
                                    <span class="badge badge-gradient-warning px-3 py-2">No Plan</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @can('Manage Student Fees Plan')
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <a href="{{ route('fees.plans.create', $student->id) }}"
                                            class="btn btn-icon badge-gradient-primary"
                                            title="Manage Plan">
                                            <i class="bi bi-gear-fill"></i>
                                        </a>
                                    </div>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown fs-4 d-block mb-2"></i>
                                    No students available for fee plan management.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#feePlansTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            "columnDefs": [{
                    "orderable": false,
                    "targets": 3
                } // Disable sorting on 'Actions' column
            ]
        });
    });
</script>
@endpush