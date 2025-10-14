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

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::find($request->class_id);
            $studentsInClass = Student::where('school_class_id', $selectedClass->id)
                ->where('school_id', $school_id)->with('user')->get();

            $voucherDate = Carbon::parse($selectedMonth . "-01");
            $year = $voucherDate->year;

            foreach ($studentsInClass as $student) {
                // 1. Ensure the voucher for the selected month exists or is created first.
                $this->generateVoucherForMonth($student, $voucherDate);

                // 2. Now that we're sure all vouchers are present, fetch a FRESH collection for the year.
                $allVouchersForYear = $student->feeVouchers()
                    ->whereYear('voucher_month', $year)
                    ->get();

                // 3. Calculate year-to-date totals based on the fresh, up-to-date data.
                $student->total_payable = $allVouchersForYear->sum('amount_due');
                $student->total_paid = $allVouchersForYear->sum('amount_paid');
                $student->total_remaining = $student->total_payable - $student->total_paid;

                // 4. Get the current month's voucher for the action buttons.

                // --- THIS IS THE FIX ---
                // Compare the Carbon object from the voucher with the Carbon object of the selected date.
                $student->voucher = $allVouchersForYear->first(function ($voucher) use ($voucherDate) {
                    // Use isSameDay() to compare just the date part, ignoring time.
                    return Carbon::parse($voucher->voucher_month)->isSameDay($voucherDate);
                });
   
                if (!$student->voucher) {
                    $student->voucher = (object)['status' => 'no_plan'];
                }

                $student->is_defaulter = $this->checkIfDefaulter($student, $voucherDate);
                $students->push($student);
            }
        }
        return view('admin.fees.payments.index', compact('classes', 'students', 'selectedClass', 'selectedMonth'));
    }

    private function generateVoucherForMonth(Student $student, Carbon $voucherDate)
    {
        $year = $voucherDate->year;
        $month = $voucherDate->month;

        $feePlan = $student->feePlans()->where('year', $year)->first();
        // This is the crucial check: we need both the annual plan and the specific monthly fee record.
        $monthlyTuition = $feePlan ? $feePlan->monthlyTuitionFees()->where('month', $month)->first() : null;

        if ($feePlan && $monthlyTuition) {
            $tuition_fee = $monthlyTuition->tuition_fee;
            $admission_fee = ($month == 1) ? $feePlan->admission_fee : 0;
            $examination_fee = in_array($month, [3, 9]) ? $feePlan->examination_fee : 0;
            $other_fees = ($month == 1) ? $feePlan->other_fees : 0;

            $previousVoucher = $student->feeVouchers()->where('voucher_month', $voucherDate->copy()->subMonth()->format('Y-m-d'))->first();
            $arrears = $previousVoucher ? ($previousVoucher->amount_due - ($previousVoucher->amount_paid ?? 0)) : 0;
            $arrears = max(0, $arrears);

            $baseAmount = $tuition_fee + $admission_fee + $examination_fee + $other_fees;

            $voucher = StudentFeeVoucher::firstOrNew(
                ['student_id' => $student->id, 'voucher_month' => $voucherDate->format('Y-m-d')]
            );

            // Only create/update if the voucher is new or still pending.
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
        }
    }

    public function getStudentLedger(Student $student, $year)
    {
        $vouchers = StudentFeeVoucher::where('student_id', $student->id)
            ->whereYear('voucher_month', $year)
            ->get()->keyBy(fn($v) => Carbon::parse($v->voucher_month)->month);

        $ledger = [];
        $totalPayable = 0;
        $totalPaid = 0;

        for ($month = 1; $month <= 12; $month++) {
            $voucher = $vouchers->get($month);
            if ($voucher) {
                $balance = $voucher->amount_due - ($voucher->amount_paid ?? 0);
                $ledger[] = ['month' => Carbon::create()->month($month)->format('F'), 'amount_due' => $voucher->amount_due, 'amount_paid' => $voucher->amount_paid ?? 0, 'balance' => $balance, 'status' => $voucher->status, 'paid_on' => $voucher->paid_at ? Carbon::parse($voucher->paid_at)->format('d M, Y') : 'N/A'];
                $totalPayable += $voucher->amount_due;
                $totalPaid += $voucher->amount_paid ?? 0;
            } else {
                $ledger[] = ['month' => Carbon::create()->month($month)->format('F'), 'status' => 'not_generated'];
            }
        }

        return response()->json(['ledger' => $ledger, 'totals' => ['payable' => $totalPayable, 'paid' => $totalPaid, 'balance' => $totalPayable - $totalPaid]]);
    }

    public function storePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate(['voucher_id' => ['required', 'exists:student_fee_vouchers,id'], 'paid_tuition' => ['nullable', 'numeric', 'min:0'], 'paid_admission' => ['nullable', 'numeric', 'min:0'], 'paid_examination' => ['nullable', 'numeric', 'min:0'], 'paid_other' => ['nullable', 'numeric', 'min:0'], 'paid_arrears' => ['nullable', 'numeric', 'min:0'], 'payment_method' => ['required', 'string'], 'notes' => ['nullable', 'string'],]);
        $voucher = StudentFeeVoucher::findOrFail($validated['voucher_id']);
        if ($voucher->status !== 'pending') {
            return back()->with('error', 'This voucher has already been processed.');
        }
        $totalPaid = collect($validated)->only(['paid_tuition', 'paid_admission', 'paid_examination', 'paid_other', 'paid_arrears'])->sum();
        $status = (abs($totalPaid - $voucher->amount_due) < 0.01) ? 'paid' : 'partial';
        $voucher->update(['amount_paid' => $totalPaid, 'status' => $status, 'paid_at' => Carbon::now(), 'paid_tuition' => $validated['paid_tuition'] ?? 0, 'paid_admission' => $validated['paid_admission'] ?? 0, 'paid_examination' => $validated['paid_examination'] ?? 0, 'paid_other' => $validated['paid_other'] ?? 0, 'paid_arrears' => $validated['paid_arrears'] ?? 0, 'payment_method' => $validated['payment_method'], 'notes' => $validated['notes'],]);
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
