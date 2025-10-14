<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentFeeVoucher;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeReportController extends Controller
{
    /**
     * Show the main revenue dashboard with advanced filtering.
     */
    public function revenueDashboard(Request $request)
    {
        $school_id = Auth::user()->school_id;
        $baseQuery = StudentFeeVoucher::where('school_id', $school_id);

        // --- Date Filtering Logic ---
        $filters = [
            'year' => $request->input('year'),
            'month' => $request->input('month'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $vouchersQuery = (clone $baseQuery)->with('student.user');

        // Prioritize date range filter
        if ($filters['start_date'] && $filters['end_date']) {
            $vouchersQuery->whereBetween('voucher_month', [$filters['start_date'], $filters['end_date']]);
        } 
        // Else, use year/month filters
        else {
            if ($filters['year']) {
                $vouchersQuery->whereYear('voucher_month', $filters['year']);
            }
            if ($filters['month']) {
                $vouchersQuery->whereMonth('voucher_month', $filters['month']);
            }
        }

        $filteredVouchers = $vouchersQuery->get();

        // --- KPI Calculations ---
        $totalCollected = $filteredVouchers->whereIn('status', ['paid', 'partial'])->sum('amount_paid');
        $totalExpected = $filteredVouchers->sum('amount_due');
        $totalPending = $totalExpected - $totalCollected;

        // --- Fee Breakdown Calculation ---
        $feeBreakdown = new \stdClass();
        $feeBreakdown->tuition = $filteredVouchers->whereIn('status', ['paid', 'partial'])->sum('paid_tuition');
        $feeBreakdown->admission = $filteredVouchers->whereIn('status', ['paid', 'partial'])->sum('paid_admission');
        $feeBreakdown->examination = $filteredVouchers->whereIn('status', ['paid', 'partial'])->sum('paid_examination');
        $feeBreakdown->other = $filteredVouchers->whereIn('status', ['paid', 'partial'])->sum('paid_other');


        // --- Monthly Revenue Chart Data ---
        // The chart should always show the full year's data for context.
        $chartYear = $filters['year'] ?? now()->year;
        $monthlyRevenue = DB::table('student_fee_vouchers')
            ->where('school_id', $school_id)
            ->whereIn('status', ['paid', 'partial'])
            ->whereYear('voucher_month', $chartYear)
            ->select(
                DB::raw('MONTH(voucher_month) as month_num'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create()->month($item->month_num)->format('F'),
                    'total' => $item->total,
                ];
            });

        // --- Defaulters List (3+ months due) ---
        $defaulters = Student::where('school_id', $school_id)
            ->with(['user', 'schoolClass'])
            ->whereHas('feeVouchers', function ($query) {
                $query->whereIn('status', ['pending', 'partial'])
                      ->where('due_date', '<', Carbon::now());
            }, '>=', 3)
            ->get()
            ->map(function($student) {
                $student->total_due = $student->feeVouchers->whereIn('status', ['pending', 'partial'])->sum(function($voucher) {
                    return $voucher->amount_due - $voucher->amount_paid;
                });
                return $student;
            });


        return view('admin.reports.revenue_dashboard', compact(
            'totalCollected',
            'totalPending',
            'totalExpected',
            'feeBreakdown',
            'monthlyRevenue',
            'defaulters',
            'filters'
        ));
    }


    /**
     * Show Pending Fees
     */
    public function pendingFees(Request $request)
    {
        $school_id = Auth::user()->school_id;
        $classes = SchoolClass::where('school_id', $school_id)->get();
        $selectedClass  = null;
        $selectedMonth = null;

        $query = StudentFeeVoucher::with('student.user', 'student.schoolClass')
            ->where('school_id', $school_id)
            ->whereIn('status', ['pending', 'partial']);

        if ($request->filled('month')) {
            $selectedMonth = $request->month;
            $date = Carbon::parse($request->month . '-01');
            $query->whereMonth('voucher_month', $date->month)
                  ->whereYear('voucher_month', $date->year);
        }

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::find($request->class_id);
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('school_class_id', $request->class_id);
            });
        }

        $pendingFees = $query->get();

        return view('admin.reports.pending_fees', compact('pendingFees', 'selectedMonth', 'selectedClass', 'classes'));
    }

    /**
     * Show Paid Fees
     */
    public function paidFees(Request $request)
    {
        $school_id = Auth::user()->school_id;
        $classes = SchoolClass::where('school_id', $school_id)->get();
        $selectedClass  = null;
        $selectedMonth = null;

        $query = StudentFeeVoucher::with('student.user', 'student.schoolClass')
            ->where('school_id', $school_id)
            ->whereIn('status', ['paid', 'partial']);

        if ($request->filled('month')) {
            $selectedMonth = $request->month;
            $date = Carbon::parse($request->month . '-01');
            $query->whereMonth('voucher_month', $date->month)
                  ->whereYear('voucher_month', $date->year);
        }

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::find($request->class_id);
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('school_class_id', $request->class_id);
            });
        }

        $paidFees = $query->get();

        return view('admin.reports.paid_fees', compact('paidFees', 'classes', 'selectedClass', 'selectedMonth'));
    }
}

