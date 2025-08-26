<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentFeeVoucher;
use App\Models\SchoolClass;
use Carbon\Carbon;

class FeeReportController extends Controller
{
    /**
     * Show Pending Fees
     */
    public function pendingFees(Request $request)
    {
    $classes = SchoolClass::all();
    $selectedClass  = null;
    $selectedMonth = null;

    $query = StudentFeeVoucher::with('student.user')
        ->where('status', 'pending');

    // Handle month input (YYYY-MM format)
    if ($request->filled('month')) {
        $selectedMonth = $request->month; // keep for blade
        $date = \Carbon\Carbon::parse($request->month . '-01'); // add day to parse
        $query->whereMonth('voucher_month', $date->month)
              ->whereYear('voucher_month', $date->year);
    }

    // Handle class filter
    if ($request->filled('class_id')) {
        $selectedClass = SchoolClass::find($request->class_id);
        $query->whereHas('student', function($q) use ($request) {
            $q->where('school_class_id', $request->class_id);
        });
    }

        $pendingFees = $query->get();

        return view('admin.reports.pending_fees', compact('pendingFees','selectedMonth','selectedClass','classes'));
    }

    /**
     * Show Paid Fees
     */
public function paidFees(Request $request)
{
    $classes = SchoolClass::all();
    $selectedClass  = null;
    $selectedMonth = null;

    $query = StudentFeeVoucher::with('student.user')
        ->where('status', 'paid');

    // Handle month input (YYYY-MM format)
    if ($request->filled('month')) {
        $selectedMonth = $request->month; // keep for blade
        $date = \Carbon\Carbon::parse($request->month . '-01'); // add day to parse
        $query->whereMonth('voucher_month', $date->month)
              ->whereYear('voucher_month', $date->year);
    }

    // Handle class filter
    if ($request->filled('class_id')) {
        $selectedClass = SchoolClass::find($request->class_id);
        $query->whereHas('student', function($q) use ($request) {
            $q->where('school_class_id', $request->class_id);
        });
    }

    $paidFees = $query->get();

    return view('admin.reports.paid_fees', compact('paidFees','classes','selectedClass','selectedMonth'));
}


    /**
     * Monthly Revenue Report
     */
public function monthlyRevenue(Request $request)
{
    $query = StudentFeeVoucher::selectRaw('YEAR(voucher_month) as year, MONTH(voucher_month) as month, SUM(amount_paid) as total_collected')
        ->where('status','!=', '');

    // 🔹 Apply Filters
    if ($request->filled('year')) {
        $query->whereYear('voucher_month', $request->year);
    }
    if ($request->filled('month')) {
        $query->whereMonth('voucher_month', $request->month);
    }

    $monthlyIncome = $query->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    return view('admin.reports.monthly_revenue', compact('monthlyIncome'));
}


public function totalRevenue()
{
    $totalRevenue = StudentFeeVoucher::where('status', 'paid')->sum('amount_paid');

    return view('admin.reports.total_revenue', compact('totalRevenue'));
}
public function revenueDashboard(Request $request)
{
    $year = $request->input('year', now()->year);
    $month = $request->input('month'); // optional filter

    $query = StudentFeeVoucher::where('status', 'paid');

    if ($year) {
        $query->whereYear('voucher_month', $year);
    }

    if ($month) {
        $query->whereMonth('voucher_month', $month);
    }

    // Total revenue based on filter
    $totalRevenue = $query->sum('amount_paid');

    // Monthly income grouped by month/year
    $monthlyIncome = StudentFeeVoucher::selectRaw('YEAR(voucher_month) as year, MONTH(voucher_month) as month, SUM(amount_paid) as total_collected')
        ->where('status', 'paid')
        ->when($year, fn($q) => $q->whereYear('voucher_month', $year))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    return view('admin.reports.revenue_dashboard', compact('monthlyIncome', 'totalRevenue', 'year', 'month'));
}

}
