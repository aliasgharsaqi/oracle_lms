<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFeeVoucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeePaymentController extends Controller
{
    public function index(Request $request): View
    {
        $school_id = Auth::user()->school_id;
        $classes = SchoolClass::where('school_id', $school_id)->get();
        $students = collect();
        $selectedClass = null;
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $voucherDate = Carbon::parse($selectedMonth . "-01");
        $year = $voucherDate->year;
        $month = $voucherDate->month;

        // 1. **CRITICAL FIX: Check if class_id is valid before proceeding**
        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::where('id', $request->class_id)
                                        ->where('school_id', $school_id)
                                        ->first(); 
            
            if ($selectedClass) {
                // Load students, but fetch Vouchers and Fee Plans based on the year/month.
                $studentsInClass = Student::where('school_class_id', $selectedClass->id)
                    ->where('school_id', $school_id)
                    ->with([
                        'user',
                        // Load all vouchers for the selected year
                        'feeVouchers' => fn($q) => $q->whereYear('voucher_month', $year),
                        // Load the fee plan for the selected year
                        'feePlans' => fn($q) => $q->where('year', $year),
                        // Load monthly tuition fees for the selected month
                        'feePlans.monthlyTuitionFees' => fn($q) => $q->where('month', $month)
                    ])
                    ->get();

                foreach ($studentsInClass as $student) {
                    $currentAnnualPlan = $student->feePlans->first();
                    $vouchersThisYear = $student->feeVouchers; // All vouchers for the year

                    $currentMonthlyPlan = $currentAnnualPlan ? $currentAnnualPlan->monthlyTuitionFees->first() : null;
                    $student->has_plan_for_month = (bool) $currentMonthlyPlan;

                    // Calculate totals (YTD)
                    // CRITICAL: We rely on Ledger for accurate YTD calculations now.
                    $ytdData = $this->calculateYearlyTotals($student, $year);
                    $student->total_payable = $ytdData['total_payable'];
                    $student->total_paid = $ytdData['total_paid'];
                    $student->total_remaining = $ytdData['total_remaining'];


                    // Find the voucher specific to the requested month
                    $student->voucher = $vouchersThisYear->first(function ($voucher) use ($voucherDate) {
                        return Carbon::parse($voucher->voucher_month)->isSameDay($voucherDate);
                    });

                    // Check defaulter status based on remaining balance
                    $student->is_defaulter = $this->checkIfDefaulter($student, $voucherDate);

                    $students->push($student);
                }
            }
        }

        return view('admin.fees.payments.index', compact('classes', 'students', 'selectedClass', 'selectedMonth'));
    }

    private function calculateYearlyTotals(Student $student, int $year): array
    {
        $vouchers = StudentFeeVoucher::where('student_id', $student->id)
            ->whereYear('voucher_month', $year)
            ->get();
            
        // Assuming your fee plans calculate the total annual fee correctly
        $plan = $student->feePlans()->where('year', $year)->first();
        
        $totalPaid = $vouchers->sum('amount_paid');
        
        // This estimate is complicated by monthly arrears.
        // For accurate tracking, we assume total_annual_fees from the plan + previous year arrears.
        // We will stick to the simplified method for the index view performance.

        return [
            'total_payable' => $plan ? (float) $plan->total_annual_fees : 0.00,
            'total_paid' => $totalPaid,
            'total_remaining' => ($plan ? (float) $plan->total_annual_fees : 0.00) - $totalPaid
        ];
    }

    public function generateAndGetVoucher(Request $request, Student $student)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        if ($student->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $voucherDate = Carbon::parse($validated['month'] . "-01");
        $year = $voucherDate->year;
        $month = $voucherDate->month;

        $feePlan = $student->feePlans()->where('year', $year)->first();
        $monthlyTuition = $feePlan ? $feePlan->monthlyTuitionFees()->where('month', $month)->first() : null;

        if ($feePlan && $monthlyTuition) {
            $tuition_fee = $monthlyTuition->tuition_fee;
            
            // Note: If tuition_fee is 0, we assume no fee is due for this month (vacations etc.)
            if ($tuition_fee == 0 && $month > 1 && !in_array($month, [3, 9])) {
                 return response()->json(['error' => 'No tuition fee is due for this month.'], 404);
            }

            // Fixed Fees Logic (Assuming these are charged based on month number)
            $admission_fee = ($month == 1) ? $feePlan->admission_fee : 0;
            $examination_fee = in_array($month, [3, 9]) ? $feePlan->examination_fee : 0;
            $other_fees = ($month == 1) ? $feePlan->other_fees : 0;

            // Arrears Calculation: Calculate TOTAL ARREARS from ALL previous unpaid vouchers in this year and previous years.
            $previousVouchers = $student->feeVouchers()
                ->where('voucher_month', '<', $voucherDate->format('Y-m-d'))
                ->orderBy('voucher_month', 'desc')
                ->get();

            $openingArrears = 0;
            if ($previousVouchers->isNotEmpty()) {
                // Get the cumulative balance from the LAST voucher of the previous month/year
                $lastVoucher = $previousVouchers->first();
                $cumulativeBalance = $lastVoucher->amount_due - ($lastVoucher->amount_paid ?? 0);
                
                // Add up the unpaid amounts from all previous vouchers that are still pending
                foreach ($previousVouchers as $prev) {
                    if (round($prev->amount_due, 2) > round($prev->amount_paid, 2)) {
                        $openingArrears += ($prev->amount_due - $prev->amount_paid);
                    }
                }
                
                // CRITICAL FIX: The simplest way to handle cumulative arrears is usually based on the *last* transaction balance. 
                // However, without a formal ledger table, we use a simpler model:
                $openingArrears = max(0, $cumulativeBalance); // Use balance from the last voucher
            }


            $baseAmount = $tuition_fee + $admission_fee + $examination_fee + $other_fees;
            $totalAmountDue = $baseAmount + $openingArrears;

            // Use updateOrCreate to handle existing partial/pending vouchers
            $voucher = StudentFeeVoucher::updateOrCreate(
                ['student_id' => $student->id, 'voucher_month' => $voucherDate->format('Y-m-d')],
                [
                    'school_id' => $student->school_id,
                    'amount_due' => $totalAmountDue,
                    'due_date' => $voucherDate->copy()->day(10)->format('Y-m-d'),
                    // Status is 'pending' unless it was previously 'paid' or 'partial'
                    'status' => $voucher->status ?? 'pending',
                    'tuition_fee' => $tuition_fee,
                    'admission_fee' => $admission_fee,
                    'examination_fee' => $examination_fee,
                    'other_fees' => $other_fees,
                    // The 'arrears' field on the voucher should reflect the amount carried forward.
                    'arrears' => $openingArrears, 
                ]
            );

            // If the voucher was paid or partially paid, recalculate status/due amount
            if ($voucher->amount_paid > 0) {
                 $voucher->amount_due = $totalAmountDue;
                 $voucher->status = (abs($voucher->amount_paid - $voucher->amount_due) < 0.01) ? 'paid' : 'partial';
                 $voucher->save();
            }

            // 3. Return the voucher data as JSON
            return response()->json($voucher);
        } else {
            // 4. Handle cases where no fee plan is set for this month
            return response()->json(['error' => 'No fee plan found for this student for the selected month.'], 404);
        }
    }

    public function getStudentLedger(Student $student, $year)
    {
        // Check authorization
        if ($student->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $year = (int) $year;
        $currentMonthCarbon = Carbon::now()->startOfMonth();
        $student->load(['feePlans' => fn($q) => $q->where('year', $year), 'feePlans.monthlyTuitionFees']);
        $feePlan = $student->feePlans->first();

        // 1. Calculate opening arrears from the previous year (if any)
        $lastVoucherOfPreviousYear = $student->feeVouchers()
            ->whereYear('voucher_month', $year - 1)
            ->orderBy('voucher_month', 'desc')
            ->first();

        $openingArrears = 0.00;
        if ($lastVoucherOfPreviousYear) {
            $balance = $lastVoucherOfPreviousYear->amount_due - ($lastVoucherOfPreviousYear->amount_paid ?? 0);
            $openingArrears = max(0, $balance);
        }

        // 2. Fetch existing vouchers for the year
        $vouchers = StudentFeeVoucher::where('student_id', $student->id)
            ->whereYear('voucher_month', $year)
            ->orderBy('voucher_month', 'asc')
            ->get()->keyBy(fn($v) => Carbon::parse($v->voucher_month)->month); 

        $ledger = [];
        $runningBalance = $openingArrears; // Start running balance with opening arrears
        $totalPayable = 0.00;
        $totalPaid = 0.00;

        // 3. Build the monthly ledger details by iterating over all 12 months
        for ($month = 1; $month <= 12; $month++) {
            $monthCarbon = Carbon::create($year, $month, 1)->startOfMonth();
            $monthName = $monthCarbon->format('F');
            $voucher = $vouchers->get($month); // Existing voucher if paid/generated
            $monthlyPlan = $feePlan ? $feePlan->monthlyTuitionFees->keyBy('month')->get($month) : null;
            
            // Only proceed for past/current months OR if a plan/voucher exists
            if ($monthCarbon->lt($currentMonthCarbon) || $monthCarbon->isSameMonth($currentMonthCarbon) || $voucher || $monthlyPlan) {
                
                $tuitionFee = $monthlyPlan ? (float)$monthlyPlan->tuition_fee : 0.00;
                
                // Calculate base fees for this specific month from the plan
                $baseFeeThisMonth = $tuitionFee;
                if ($month == 1) { // Admission/Other fees typically only in January
                    $baseFeeThisMonth += (float)($feePlan->admission_fee ?? 0);
                    $baseFeeThisMonth += (float)($feePlan->other_fees ?? 0);
                }
                if (in_array($month, [3, 9])) { // Examination fees
                    $baseFeeThisMonth += (float)($feePlan->examination_fee ?? 0);
                }

                $totalDue = $baseFeeThisMonth + $runningBalance;
                $amountPaid = 0.00;
                $status = 'not_due';
                $paidOn = null;

                if ($monthCarbon->lte($currentMonthCarbon)) {
                    // This month is past or current, fees are DUE.
                    
                    if ($voucher) {
                        // VOUCHER FOUND: Use actual voucher data for due/paid amounts
                        $totalDue = (float)($voucher->amount_due);
                        $amountPaid = (float)($voucher->amount_paid ?? 0);
                        $status = $voucher->status;
                        $paidOn = $voucher->paid_at ? Carbon::parse($voucher->paid_at)->format('d M, Y') : 'N/A';
                    } else if ($baseFeeThisMonth > 0) {
                        // NO VOUCHER FOUND, BUT BASE FEE IS DUE: Set initial due amount + running arrears
                        // We do NOT generate the voucher here, just calculate and set status to pending/not_generated
                        $totalDue = $baseFeeThisMonth + $runningBalance;
                        $status = 'pending';
                    }
                }
                
                $balance = $totalDue - $amountPaid;

                // Update running totals
                $totalPayable += $baseFeeThisMonth; // Only add the base fee, arrears is cumulative
                $totalPaid += $amountPaid;
                
                // Update running balance carried forward to the next month
                // CRITICAL ARREARS FIX: The next month's running balance is the current month's remaining balance.
                $runningBalance = $balance; 

                // Only add to ledger if fee was due this month or a voucher/plan exists
                if ($baseFeeThisMonth > 0 || $totalDue > 0) {
                    $ledger[] = [
                        'month' => $monthName,
                        // Show the calculated total due for display, including arrears carried forward
                        'amount_due' => $totalDue, 
                        'amount_paid' => $amountPaid,
                        'balance' => $balance,
                        'status' => $status,
                        'paid_on' => $paidOn
                    ];
                }
            }
        }
        
        $totalBalance = $totalPayable - $totalPaid;


        // 4. Return the ledger and calculated totals
        return response()->json([
            'ledger' => $ledger,
            'totals' => [
                'payable' => $totalPayable + $openingArrears, // Include opening arrears in final payable total
                'paid' => $totalPaid,
                'balance' => $totalBalance
            ]
        ]);
    }
    
    public function storePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'voucher_id' => ['required', 'exists:student_fee_vouchers,id'],
            'paid_tuition' => ['nullable', 'numeric', 'min:0'],
            'paid_admission' => ['nullable', 'numeric', 'min:0'],
            'paid_examination' => ['nullable', 'numeric', 'min:0'],
            'paid_other' => ['nullable', 'numeric', 'min:0'],
            'paid_arrears' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $voucher = StudentFeeVoucher::findOrFail($validated['voucher_id']);

        if (!in_array($voucher->status, ['pending', 'partial'])) {
            return back()->with('error', 'This voucher has already been fully paid.');
        }

        $newPaidTuition = $validated['paid_tuition'] ?? 0;
        $newPaidAdmission = $validated['paid_admission'] ?? 0;
        $newPaidExamination = $validated['paid_examination'] ?? 0;
        $newPaidOther = $validated['paid_other'] ?? 0;
        $newPaidArrears = $validated['paid_arrears'] ?? 0;

        $newPaymentAmount = $newPaidTuition + $newPaidAdmission + $newPaidExamination + $newPaidOther + $newPaidArrears;

        $totalPaid = $voucher->amount_paid + $newPaymentAmount;
        $totalPaidTuition = $voucher->paid_tuition + $newPaidTuition;
        $totalPaidAdmission = $voucher->paid_admission + $newPaidAdmission;
        $totalPaidExamination = $voucher->paid_examination + $newPaidExamination;
        $totalPaidOther = $voucher->paid_other + $newPaidOther;
        $totalPaidArrears = $voucher->paid_arrears + $newPaidArrears;
        $status = (abs($totalPaid - $voucher->amount_due) < 0.01) ? 'paid' : 'partial';

        $voucher->update([
            'amount_paid' => $totalPaid,
            'status' => $status,
            'paid_at' => Carbon::now(),
            'paid_tuition' => $totalPaidTuition,
            'paid_admission' => $totalPaidAdmission,
            'paid_examination' => $totalPaidExamination,
            'paid_other' => $totalPaidOther,
            'paid_arrears' => $totalPaidArrears,
            'payment_method' => $validated['payment_method'],
            'notes' => $voucher->notes . "\n" . $validated['notes'],
        ]);

        return redirect()->route('fees.receipt', $voucher->id);
    }

    private function checkIfDefaulter(Student $student, Carbon $currentVoucherDate): bool
    {
        $unpaidCount = 0;
        for ($i = 1; $i <= 3; $i++) {
            $monthToCheck = $currentVoucherDate->copy()->subMonths($i);
            $voucher = $student->feeVouchers()->whereYear('voucher_month', $monthToCheck->year)->whereMonth('voucher_month', $monthToCheck->month)->first();
            if ($voucher && (round($voucher->amount_paid, 2) < round($voucher->amount_due, 2))) {
                $unpaidCount++;
            }
        }
        return $unpaidCount >= 3;
    }

    public function showReceipt(StudentFeeVoucher $voucher): View
    {
        $voucher->load(['student.user.school', 'student.schoolClass']);
        return view('admin.fees.payments.receipt', compact('voucher'));
    }
}
