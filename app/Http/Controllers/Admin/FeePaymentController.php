<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFeePlan;
use App\Models\StudentFeeVoucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FeePaymentController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::all();
        $students = collect();
        $selectedClass = null;
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::find($request->class_id);
            $studentsInClass = Student::where('school_class_id', $selectedClass->id)
                ->with('user')
                ->get();

            $voucherDate = Carbon::parse($selectedMonth . "-01");
            $year = $voucherDate->year;
            $month = $voucherDate->month;

            foreach ($studentsInClass as $student) {
                // Find the specific fee plan for this student for this month
                $feePlan = StudentFeePlan::where('student_id', $student->id)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->first();

                // Only create a voucher if a fee plan exists for that month
                if ($feePlan) {
                    $baseAmount = $feePlan->amount;

                    // Get previous month’s voucher
                    $previousVoucher = StudentFeeVoucher::where('student_id', $student->id)
                        ->where('voucher_month', $voucherDate->copy()->subMonth()->format('Y-m-d'))
                        ->first();

                    $carryForward = 0;
                    if ($previousVoucher && $previousVoucher->amount_due > ($previousVoucher->amount_paid ?? 0)) {
                        $carryForward = $previousVoucher->amount_due - ($previousVoucher->amount_paid ?? 0);
                    }

                    $voucher = StudentFeeVoucher::firstOrCreate(
                        [
                            'student_id'    => $student->id,
                            'voucher_month' => $voucherDate->format('Y-m-d'),
                        ],
                        [
                            'amount_due' => $baseAmount + $carryForward,
                            'due_date'   => $voucherDate->copy()->day(10)->format('Y-m-d'),
                            'status'     => 'pending',
                        ]
                    );

                    // Optional: if voucher already exists, also update amount_due with carry forward
                    if (!$voucher->wasRecentlyCreated && $carryForward > 0) {
                        $voucher->update([
                            'amount_due' => $baseAmount + $carryForward
                        ]);
                    }

                    $student->voucher = $voucher;
                } else {
                    // If no plan, create a placeholder voucher to show the status
                    $student->voucher = (object)[
                        'status'     => 'no_plan',
                        'amount_due' => 0
                    ];
                }

                $students->push($student);
            }
        }

        return view('admin.fees.payments.index', compact('classes', 'students', 'selectedClass', 'selectedMonth'));
    }



    public function storePayment(Request $request): RedirectResponse
    {
        $request->validate([
            'voucher_id' => ['required', 'exists:student_fee_vouchers,id'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string'],
        ]);

        $voucher = StudentFeeVoucher::find($request->voucher_id);
        
        if($voucher->status === 'paid') {
            return back()->with('error', 'This voucher has already been paid.');
        }

        $voucher->update([
            'amount_paid' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        return redirect()->route('fees.receipt', $voucher->id);
    }

    public function showReceipt(StudentFeeVoucher $voucher): View
    {
        $voucher->load(['student.user', 'student.schoolClass']);
        return view('admin.fees.payments.receipt', compact('voucher'));
    }
}
