@extends('layouts.admin')

@section('title', 'Student Fee Plans')
@section('page-title', 'Student Fee Plans')
<style>
    /* Gradient backgrounds for icons */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056d2);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #009b5c);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #ff9800);
    }

    /* Icon Circle */
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s ease;
    }

    /* Card hover effects */
    .stats-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    /* Hover animation for icon */
    .stats-card:hover .icon-circle {
        transform: rotate(10deg) scale(1.05);
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
    }

    .table-row-hover:hover {
        background-color: #f8faff !important;
        transform: scale(1.01);
        transition: all 0.2s ease-in-out;
    }

    .hover-glow:hover {
        box-shadow: 0 0 12px rgba(13, 110, 253, 0.3);
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
    }

    .bg-success-subtle {
        background-color: #e6f4ea !important;
    }

    .bg-warning-subtle {
        background-color: #fff8e1 !important;
    }

    /* Smooth input hover and focus effects */
    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 6px rgba(13, 110, 253, 0.3);
        transition: all 0.2s ease-in-out;
    }

    /* Card shadow animation */
    .shadow-sm {
        transition: all 0.3s ease;
    }

    .shadow-sm:hover {
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08) !important;
    }

    /* Button hover glow effect */
    .hover-glow:hover {
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.4);
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    /* Reset button hover style */
    .hover-reset:hover {
        background-color: #f8f9fa;
        color: #000 !important;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
</style>
@section('content')
    <div class="">
        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Students -->
            <div class="col-md-4">
                <div class="card stats-card border-0 rounded-4 h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle bg-gradient-primary text-white shadow-sm me-3">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold mb-1 small">Total Students</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ $totalStudents }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Plans Set -->
            <div class="col-md-4">
                <div class="card stats-card border-0 rounded-4 h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle bg-gradient-success text-white shadow-sm me-3">
                            <i class="bi bi-check-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold mb-1 small">Fee Plans Set ({{ $year }})</h6>
                            <h3 class="fw-bold text-success mb-0">{{ $studentsWithPlan }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Plans -->
            <div class="col-md-4">
                <div class="card stats-card border-0 rounded-4 h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle bg-gradient-warning text-white shadow-sm me-3">
                            <i class="bi bi-hourglass-split fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold mb-1 small">Plans Pending ({{ $year }})</h6>
                            <h3 class="fw-bold text-warning mb-0">{{ $studentsWithoutPlan }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Main Content Card -->
        <div class="card shadow-lg border-0 rounded-4">
            <div
                class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-cash-coin me-2"></i> Manage Student Fee Plans</h5>
            </div>

            <!-- Filter Bar -->
            <div class="card-body border-bottom bg-light p-4 rounded-bottom-4 shadow-sm">
                <form action="{{ route('fees.plans.index') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <!-- Student Name -->
                        <div class="col-md-3">
                            <label for="student_name" class="form-label fw-semibold text-secondary">
                                <i class="bi bi-person me-1 text-primary"></i> Student Name
                            </label>
                            <input type="text" class="form-control border-0 shadow-sm rounded-3" name="student_name"
                                id="student_name" value="{{ request('student_name') }}" placeholder="Search by name...">
                        </div>

                        <!-- Class -->
                        <div class="col-md-3">
                            <label for="class_id" class="form-label fw-semibold text-secondary">
                                <i class="bi bi-book me-1 text-success"></i> Class
                            </label>
                            <select name="class_id" id="class_id" class="form-select border-0 shadow-sm rounded-3">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-2">
                            <label for="status" class="form-label fw-semibold text-secondary">
                                <i class="bi bi-check2-circle me-1 text-warning"></i> Plan Status
                            </label>
                            <select name="status" id="status" class="form-select border-0 shadow-sm rounded-3">
                                <option value="">All</option>
                                <option value="defined" {{ request('status') == 'defined' ? 'selected' : '' }}>Defined
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                            </select>
                        </div>

                        <!-- Year -->
                        <div class="col-md-2">
                            <label for="year" class="form-label fw-semibold text-secondary">
                                <i class="bi bi-calendar3 me-1 text-info"></i> Year
                            </label>
                            <select name="year" id="year" class="form-select border-0 shadow-sm rounded-3">
                                @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm rounded-3 hover-glow">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            <a href="{{ route('fees.plans.index') }}"
                                class="btn btn-outline-secondary w-100 shadow-sm rounded-3 hover-reset">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>


            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 rounded-4 overflow-hidden shadow-sm">
                        <thead class="bg-gradient bg-primary text-white">
                            <tr>
                                <th class="py-3 px-4 text-uppercase small fw-semibold">Student Name</th>
                                <th class="py-3 px-4 text-uppercase small fw-semibold">Class</th>
                                <th class="py-3 px-4 text-uppercase small fw-semibold">Status ({{ $year }})</th>
                                <th class="py-3 px-4 text-center text-uppercase small fw-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($students as $student)
                                <tr class="border-bottom table-row-hover">
                                    <td class="py-3 px-4 fw-semibold text-dark">
                                        <i class="bi bi-person-circle me-2 text-primary"></i>
                                        {{ $student->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 text-secondary">
                                        <i class="bi bi-bookmark-fill me-2 text-success"></i>
                                        {{ $student->schoolClass->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($student->fee_plans_count > 0)
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i> Plan Defined
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-hourglass-split me-1"></i> Plan Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @can('Manage Student Fees Plan')
                                            @if($student->fee_plans_count > 0)
                                                <a href="{{ route('fees.plans.create', ['student' => $student->id, 'year' => $year]) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm hover-glow"
                                                    title="View/Edit Plan">
                                                    <i class="bi bi-pencil-square me-1"></i> View / Edit
                                                </a>
                                            @else
                                                <a href="{{ route('fees.plans.create', ['student' => $student->id, 'year' => $year]) }}"
                                                    class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm hover-glow"
                                                    title="Set Plan">
                                                    <i class="bi bi-plus-circle-fill me-1"></i> Set Plan
                                                </a>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-5">
                                        <i class="bi bi-emoji-frown fs-2 d-block mb-2"></i>
                                        No students found matching your criteria.
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