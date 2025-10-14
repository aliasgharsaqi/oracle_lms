@extends('layouts.admin')

@section('title', 'Student Fee Plans')
@section('page-title', 'Student Fee Plans')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Total Students</h6>
                        <h4 class="card-title fw-bold mb-0">{{ $totalStudents }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Fee Plans Set ({{$year}})</h6>
                        <h4 class="card-title fw-bold mb-0">{{ $studentsWithPlan }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Plans Pending ({{$year}})</h6>
                        <h4 class="card-title fw-bold mb-0">{{ $studentsWithoutPlan }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="custom-card-header bg-primary text-white rounded-top-4 d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-cash-coin me-2"></i> Manage Student Fee Plans</h5>
        </div>
        
        <!-- Filter Bar -->
        <div class="card-body border-bottom p-3 bg-light">
            <form action="{{ route('fees.plans.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="student_name" class="form-label fw-semibold">Student Name</label>
                        <input type="text" class="form-control" name="student_name" id="student_name" value="{{ request('student_name') }}" placeholder="Search by name...">
                    </div>
                    <div class="col-md-3">
                        <label for="class_id" class="form-label fw-semibold">Class</label>
                        <select name="class_id" id="class_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label fw-semibold">Plan Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All</option>
                            <option value="defined" {{ request('status') == 'defined' ? 'selected' : '' }}>Defined</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                     <div class="col-md-2">
                        <label for="year" class="form-label fw-semibold">Year</label>
                        <select name="year" id="year" class="form-select">
                             @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                        <a href="{{ route('fees.plans.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">Student Name</th>
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Status ({{$year}})</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="py-3 px-4 fw-semibold">{{ $student->user->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4">{{ $student->schoolClass->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4">
                                @if($student->fee_plans_count > 0)
                                    <span class="badge bg-success-soft text-success px-2 py-1">Plan Defined</span>
                                @else
                                    <span class="badge bg-warning-soft text-warning px-2 py-1">Plan Pending</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @can('Manage Student Fees Plan')
                                    @if($student->fee_plans_count > 0)
                                        <a href="{{ route('fees.plans.create', ['student' => $student->id, 'year' => $year]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="View/Edit Plan">
                                            <i class="bi bi-pencil-square me-1"></i> View / Edit Plan
                                        </a>
                                    @else
                                        <a href="{{ route('fees.plans.create', ['student' => $student->id, 'year' => $year]) }}" class="btn btn-sm btn-primary rounded-pill px-3" title="Set Plan">
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

