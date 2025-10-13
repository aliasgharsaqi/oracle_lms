@extends('layouts.admin')

@section('title', 'Student Fee Plans')
@section('page-title', 'Student Fee Plans')

@section('content')
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div class="custom-card-header flex flex-wrap justify-between items-center gap-2 px-4 py-3 border-b">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-cash-coin"></i> Manage Student Fee Plans
            </h4>
        </div>

        <!-- Card Body -->
        <div class="p-0">
            <div class="overflow-x-auto">
                <table id="feePlansTable" class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3">Student Name</th>
                            <th class="px-4 py-3">Class</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold max-w-[160px] truncate">
                                {{ $student->user->name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $student->schoolClass->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($student->fee_plans_count > 0)
                                    <span class="badge badge-gradient-success px-3 py-1">Plan Defined</span>
                                @else
                                    <span class="badge badge-gradient-warning px-3 py-1">No Plan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @can('Manage Student Fees Plan')
                                <div class="flex flex-wrap justify-center gap-2">
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
                            <td colspan="4" class="text-center text-gray-500 py-6">
                                <i class="bi bi-emoji-frown text-lg"></i>
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

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#feePlansTable').DataTable({
            dom:
                "<'flex flex-col md:flex-row justify-between items-center my-3 mx-3'<'flex items-center gap-2'B><'ml-auto'f>>" + // Buttons + Search aligned
                "<'overflow-x-auto'tr>" + // Table with responsive scroll
                "<'flex flex-col md:flex-row justify-between items-center my-3 mx-3'<'text-sm'i><'mt-2 md:mt-0'p>>", // Info + Pagination aligned
            buttons: [
                { extend: 'copy' },
                { extend: 'csv' },
                { extend: 'excel' },
                { extend: 'pdf' },
                { extend: 'print' },
                { extend: 'colvis' }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            columnDefs: [
                {
                    orderable: false,
                    targets: 3 // Disable sorting on 'Actions' column
                }
            ]
        });
    });
</script>


@endpush