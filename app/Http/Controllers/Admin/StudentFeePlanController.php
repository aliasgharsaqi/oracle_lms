<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFeePlan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class StudentFeePlanController extends Controller
{
    public function index(): View
    {
        $students = Student::with('user')->withCount('feePlans')->latest()->get();
        return view('admin.fees.plans.index', compact('students'));
    }

    public function create(Student $student): View
    {
        $year = date('Y');
        $plans = $student->feePlans()->where('year', $year)->get()->keyBy('month');
        return view('admin.fees.plans.create', compact('student', 'year', 'plans'));
    }

    public function store(Request $request, Student $student): RedirectResponse
    {
        $request->validate([
            'year' => ['required', 'integer', 'min:2020'],
            'fees' => ['required', 'array', 'size:12'],
            'fees.*' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->fees as $month => $amount) {
                StudentFeePlan::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'year' => $request->year,
                        'month' => $month,
                    ],
                    [
                        'amount' => $amount,
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save fee plan. Please try again.');
        }

        return redirect()->route('fees.plans.index')->with('success', "Fee plan for {$student->user->name} has been saved.");
    }
}
