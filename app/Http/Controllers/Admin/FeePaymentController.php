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

               // ... inside the index method, within the foreach loop ...

                $allVouchersForYear = $student->feeVouchers()
                ->whereYear('voucher_month', $year)
                ->get();

                // 3. --- THIS IS THE FIX ---
                // Correctly calculate year-to-date totals to avoid double-counting arrears.

                // a. Find any outstanding balance from the end of the previous year.
                $lastVoucherOfPreviousYear = $student->feeVouchers()
                    ->whereYear('voucher_month', $year - 1)
                    ->orderBy('voucher_month', 'desc')
                    ->first();
                
                $openingArrears = 0;
                if ($lastVoucherOfPreviousYear) {
                    $balance = $lastVoucherOfPreviousYear->amount_due - ($lastVoucherOfPreviousYear->amount_paid ?? 0);
                    $openingArrears = max(0, $balance); // Carry forward only positive balances
                }

                // b. Sum only the new charges (base fees) for the current year.
                // This avoids summing the 'arrears' column which causes the miscalculation.
                $totalBaseFeesForYear = $allVouchersForYear->sum('tuition_fee') +
                                        $allVouchersForYear->sum('admission_fee') +
                                        $allVouchersForYear->sum('examination_fee') +
                                        $allVouchersForYear->sum('other_fees');

                // c. The total payable is the opening balance plus all new charges for the year.
                $student->total_payable = $openingArrears + $totalBaseFeesForYear;
                $student->total_paid = $allVouchersForYear->sum('amount_paid');
                $student->total_remaining = $student->total_payable - $student->total_paid;



                $student->voucher = $allVouchersForYear->first(function ($voucher) use ($voucherDate) {
                return Carbon::parse($voucher->voucher_month)->isSameDay($voucherDate);
                });

// ... rest of the method ...
   
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

        $feePlan = $student->feePlans()->where('year', '>=', $year)->first();
        
        // --- YEH FIX #1 HAI (Arrears ko theek karne ke liye) ---
        // Pehle, pichle mahine ka voucher update karein taake arrears sahi se aage aayein.
        // Is se ek cascading update hoga.
        if ($feePlan && $voucherDate->gt($student->created_at) && $month > 1) { 
            $this->generateVoucherForMonth($student, $voucherDate->copy()->subMonth());
        }

        // Ab, maujooda mahine ka voucher banayein
        $monthlyTuition = $feePlan ? $feePlan->monthlyTuitionFees()->where('month', $month)->first() : null;

        if ($feePlan && $monthlyTuition) {
            $tuition_fee = $monthlyTuition->tuition_fee;
            $admission_fee = ($month == 1) ? $feePlan->admission_fee : 0;
            $examination_fee = in_array($month, [3, 9]) ? $feePlan->examination_fee : 0;
            $other_fees = ($month == 1) ? $feePlan->other_fees : 0;
            
            // Yeh arrears ab sahi calculate honge kyunki pichla voucher abhi abhi update hua hai.
            $previousVoucher = $student->feeVouchers()->where('voucher_month', $voucherDate->copy()->subMonth()->format('Y-m-d'))->first();
            $arrears = 0;
            if ($previousVoucher) {
                $balance = $previousVoucher->amount_due - ($previousVoucher->amount_paid ?? 0);
                $arrears = max(0, $balance);
            }

            $baseAmount = $tuition_fee + $admission_fee + $examination_fee + $other_fees;

            $voucher = StudentFeeVoucher::firstOrNew(
                ['student_id' => $student->id, 'voucher_month' => $voucherDate->format('Y-m-d')]
            );

            // Sirf naye ya 'pending' voucher ko update karein.
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
        // Pehle, yeh yaqeeni banayein ke saal ke saare vouchers up-to-date hain.
        $todayInSelectedYear = Carbon::create($year)->isSameYear(now()) ? now() : Carbon::create($year)->endOfYear();
        $this->generateVoucherForMonth($student, $todayInSelectedYear);
        
        $vouchers = StudentFeeVoucher::where('student_id', $student->id)
            ->whereYear('voucher_month', $year)
            ->get()->keyBy(fn($v) => Carbon::parse($v->voucher_month)->month);

        // --- YEH FIX #2 HAI (Ledger ke total ko theek karne ke liye) ---
        
        // a. Pichle saal ka bacha hua balance maloom karein.
        $lastVoucherOfPreviousYear = $student->feeVouchers()
            ->whereYear('voucher_month', $year - 1)
            ->orderBy('voucher_month', 'desc')
            ->first();
        
        $openingArrears = 0;
        if ($lastVoucherOfPreviousYear) {
            $balance = $lastVoucherOfPreviousYear->amount_due - ($lastVoucherOfPreviousYear->amount_paid ?? 0);
            $openingArrears = max(0, $balance);
        }

        // b. Sirf is saal ke naye charges (base fees) ko jama karein.
        $totalBaseFeesForYear = $vouchers->sum('tuition_fee') +
                                $vouchers->sum('admission_fee') +
                                $vouchers->sum('examination_fee') +
                                $vouchers->sum('other_fees');

        // c. Sahi total payable = pichla balance + is saal ke naye charges.
        $totalPayable = $openingArrears + $totalBaseFeesForYear;
        $totalPaid = $vouchers->sum('amount_paid');
        $totalBalance = $totalPayable - $totalPaid;


        // Ab mahinay ki ledger list banayein
        $ledger = [];
        for ($month = 1; $month <= 12; $month++) {
            $voucher = $vouchers->get($month);
            if ($voucher) {
                $balance = $voucher->amount_due - ($voucher->amount_paid ?? 0);
                $ledger[] = ['month' => Carbon::create()->month($month)->format('F'), 'amount_due' => $voucher->amount_due, 'amount_paid' => $voucher->amount_paid ?? 0, 'balance' => $balance, 'status' => $voucher->status, 'paid_on' => $voucher->paid_at ? Carbon::parse($voucher->paid_at)->format('d M, Y') : 'N/A'];
            } else {
                if (Carbon::create($year, $month, 1)->isPast()) {
                    $ledger[] = ['month' => Carbon::create()->month($month)->format('F'), 'status' => 'not_generated'];
                }
            }
        }

        // Sahi totals ke saath JSON response bhejein.
        return response()->json([
            'ledger' => $ledger,
            'totals' => ['payable' => $totalPayable, 'paid' => $totalPaid, 'balance' => $totalBalance]
        ]);
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
