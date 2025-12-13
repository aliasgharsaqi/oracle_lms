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
        $month = $voucherDate->month; // Get the month number

        // 1. **CRITICAL FIX: Check if class_id is valid before proceeding**
        if ($request->filled('class_id')) {
            // Attempt to find the class
            $selectedClass = SchoolClass::where('id', $request->class_id)
                                        ->where('school_id', $school_id) // Ensure class belongs to the school
                                        ->first(); 
            
            // If the class is found, proceed with fetching students
            if ($selectedClass) {
                $studentsInClass = Student::where('school_class_id', $selectedClass->id) // SAFE now
                    ->where('school_id', $school_id)
                    ->with([
                        // ... rest of the relations ...
                        'user',
                        'feeVouchers',
                        'feePlans' => function ($query) use ($year) {
                            $query->where('year', $year);
                        },
                        'feePlans.monthlyTuitionFees' => function ($query) use ($month) {
                            $query->where('month', $month);
                        }
                    ])
                    ->get();

                foreach ($studentsInClass as $student) {
                    // ... rest of the logic ...
                    $vouchersThisYear = $student->feeVouchers->filter(
                        fn($v) => Carbon::parse($v->voucher_month)->year == $year
                    );
                    $currentAnnualPlan = $student->feePlans->first();

                    $currentMonthlyPlan = $currentAnnualPlan ? $currentAnnualPlan->monthlyTuitionFees->first() : null;
                    $student->has_plan_for_month = (bool) $currentMonthlyPlan;

                    // Ensure feePlans has been loaded before accessing total_annual_fees
                    $student->total_payable = $currentAnnualPlan ? (float) $currentAnnualPlan->total_annual_fees : 0.00;

                    $student->total_paid = $vouchersThisYear->sum('amount_paid');
                    $student->total_remaining = $student->total_payable - $student->total_paid;

                    $student->voucher = $vouchersThisYear->first(function ($voucher) use ($voucherDate) {
                        // NOTE: $voucher->voucher_month is a Carbon instance only if casting is set on FeeVoucher model
                        return $voucher->voucher_month && Carbon::parse($voucher->voucher_month)->isSameDay($voucherDate);
                    });
                    $student->is_defaulter = $this->checkIfDefaulter($student, $voucherDate);

                    $students->push($student);
                }
            } 
            // If $selectedClass is null (not found), $students remains empty, which is correct behavior.
        }

        return view('admin.fees.payments.index', compact('classes', 'students', 'selectedClass', 'selectedMonth'));
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
            $admission_fee = ($month == 1) ? $feePlan->admission_fee : 0;
            $examination_fee = in_array($month, [3, 9]) ? $feePlan->examination_fee : 0;
            $other_fees = ($month == 1) ? $feePlan->other_fees : 0;

            // Calculate arrears from the single previous month
            $previousVoucher = $student->feeVouchers()
                ->where('voucher_month', $voucherDate->copy()->subMonthNoOverflow()->format('Y-m-d'))
                ->first();
            $arrears = 0;
            if ($previousVoucher) {
                $balance = $previousVoucher->amount_due - ($previousVoucher->amount_paid ?? 0);
                $arrears = max(0, $balance);
            }

            $baseAmount = $tuition_fee + $admission_fee + $examination_fee + $other_fees;
            // Use firstOrNew to create or find the voucher
            $voucher = StudentFeeVoucher::firstOrNew(
                ['student_id' => $student->id, 'voucher_month' => $voucherDate->format('Y-m-d')]
            );

            // Only fill/update if it's new or still pending
            if (!$voucher->exists || $voucher->status == 'pending') {
                $voucher->fill([
                    'school_id' => $student->school_id,
                    'amount_due' => $baseAmount + $arrears,
                    'due_date' => $voucherDate->copy()->day(10)->format('Y-m-d'),
                    'status' => 'pending',
                    'tuition_fee' => $tuition_fee,
                    'admission_fee' => $admission_fee,
                    'examination_fee' => $examination_fee,
                    'other_fees' => $other_fees,
                    'arrears' => $arrears,
                ])->save();
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
        // --- VOUCHER GENERATION REMOVED ---
        // $todayInSelectedYear = Carbon::create($year)->isSameYear(now()) ? now() : Carbon::create($year)->endOfYear();
        // $this->generateVoucherForMonth($student, $todayInSelectedYear); // <-- DELETED THIS LINE

        // Check authorization (ensure student belongs to the admin's school)
        if ($student->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Fetch existing vouchers for the year
        $vouchers = StudentFeeVoucher::where('student_id', $student->id)
            ->whereYear('voucher_month', $year)
            ->orderBy('voucher_month', 'asc') // Order by month for correct display
            ->get()->keyBy(fn($v) => Carbon::parse($v->voucher_month)->month); // Key by month number

        // Calculate opening arrears from the previous year
        $lastVoucherOfPreviousYear = $student->feeVouchers()
            ->whereYear('voucher_month', $year - 1)
            ->orderBy('voucher_month', 'desc')
            ->first();

        $openingArrears = 0.00;
        if ($lastVoucherOfPreviousYear) {
            $balance = $lastVoucherOfPreviousYear->amount_due - ($lastVoucherOfPreviousYear->amount_paid ?? 0);
            $openingArrears = max(0, $balance); // Ensure it's not negative
        }

        // Calculate totals based *only* on the base fees charged *this year* + opening balance
        $totalBaseFeesForYear = $vouchers->sum('tuition_fee') +
            $vouchers->sum('admission_fee') +
            $vouchers->sum('examination_fee') +
            $vouchers->sum('other_fees');

        $totalPayable = $openingArrears + $totalBaseFeesForYear;
        $totalPaid = $vouchers->sum('amount_paid');
        $totalBalance = $totalPayable - $totalPaid;

        // Build the monthly ledger details
        $ledger = [];
        $currentMonthCarbon = Carbon::now()->startOfMonth(); // Get start of current month

        for ($month = 1; $month <= 12; $month++) {
            $voucher = $vouchers->get($month); // Get voucher using month number key
            $monthCarbon = Carbon::create($year, $month, 1)->startOfMonth();

            if ($voucher) {
                // Ensure amounts are floats for calculation
                $amountDue = (float)($voucher->amount_due ?? 0);
                $amountPaid = (float)($voucher->amount_paid ?? 0);
                $balance = $amountDue - $amountPaid;

                // Format paid_on date or show 'N/A'
                $paidOn = $voucher->paid_at ? Carbon::parse($voucher->paid_at)->format('d M, Y') : 'N/A';

                $ledger[] = [
                    'month' => $monthCarbon->format('F'),
                    'amount_due' => $amountDue,
                    'amount_paid' => $amountPaid,
                    'balance' => $balance,
                    'status' => $voucher->status,
                    'paid_on' => $paidOn
                ];
            } else {
                // Only show 'not_generated' for past months where no voucher exists
                // Don't show for future months
                if ($monthCarbon->lt($currentMonthCarbon)) {
                    $ledger[] = [
                        'month' => $monthCarbon->format('F'),
                        'status' => 'not_generated'
                        // Other fields can be null or omitted
                    ];
                }
                // Optional: You could add placeholder rows for future months if desired
                // else {
                //    $ledger[] = ['month' => $monthCarbon->format('F'), 'status' => 'future'];
                // }
            }
        }

        // Return the ledger and calculated totals
        return response()->json([
            'ledger' => $ledger,
            'totals' => [
                'payable' => $totalPayable,
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
