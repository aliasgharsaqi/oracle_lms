<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFeePlan;
use App\Models\MonthlyTuitionFee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class StudentFeePlanController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->input('year', date('Y')); 

        $studentsQuery = Student::with(['user', 'schoolClass']);

        if ($request->filled('class_id')) { $studentsQuery->where('school_class_id', $request->class_id); }
        if ($request->filled('student_name')) { $studentsQuery->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->student_name . '%')); }
        if ($request->filled('status')) {
            if ($request->status == 'defined') { $studentsQuery->whereHas('feePlans', fn($q) => $q->where('year', $year)); } 
            elseif ($request->status == 'pending') { $studentsQuery->whereDoesntHave('feePlans', fn($q) => $q->where('year', $year)); }
        }
        
        $classOrder = "CASE WHEN school_classes.name LIKE 'Play Group%' THEN 1 WHEN school_classes.name LIKE 'Nursery%' THEN 2 WHEN school_classes.name LIKE 'Prep%' THEN 3 WHEN school_classes.name LIKE 'KG%' THEN 4 WHEN school_classes.name LIKE 'Class 1%' THEN 5 WHEN school_classes.name LIKE 'Class 2%' THEN 6 WHEN school_classes.name LIKE 'Class 3%' THEN 7 WHEN school_classes.name LIKE 'Class 4%' THEN 8 WHEN school_classes.name LIKE 'Class 5%' THEN 9 WHEN school_classes.name LIKE 'Class 6%' THEN 10 WHEN school_classes.name LIKE 'Class 7%' THEN 11 WHEN school_classes.name LIKE 'Class 8%' THEN 12 WHEN school_classes.name LIKE 'Class 9%' THEN 13 WHEN school_classes.name LIKE 'Class 10%' THEN 14 ELSE 99 END, school_classes.name ASC";
        
        $students = $studentsQuery->join('school_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderByRaw($classOrder)
            ->orderBy('users.name', 'asc')
            ->select('students.*')
            ->get();
            
        // --- THIS IS THE FIX ---
        // After getting the sorted student list, load the count for the specific year onto the collection.
        $students->loadCount(['feePlans' => fn($q) => $q->where('year', $year)]);
        
        $allSchoolStudents = Student::query();
        $totalStudents = $allSchoolStudents->count();
        $studentsWithPlan = Student::whereHas('feePlans', fn($q) => $q->where('year', $year))->count();
        $studentsWithoutPlan = $totalStudents - $studentsWithPlan;
        
        $classes = \App\Models\SchoolClass::query()->select('id', 'name')->orderByRaw(str_replace('school_classes.', '', $classOrder))->get();
        
        return view('admin.fees.plans.index', compact('students', 'classes', 'totalStudents', 'studentsWithPlan', 'studentsWithoutPlan', 'year'));
    }

    public function create(Student $student): View
    {
        $year = request('year', date('Y'));
        
        $plan = $student->feePlans()->with('monthlyTuitionFees')->where('year', $year)->first();
        
        $monthly_plans = $plan ? $plan->monthlyTuitionFees->keyBy('month') : collect();

        return view('admin.fees.plans.create', compact('student', 'year', 'plan', 'monthly_plans'));
    }

    public function store(Request $request, Student $student): RedirectResponse
    {
        $request->validate([
            'year' => ['required', 'integer', 'min:2020'],
            'tuition_fee' => ['required', 'array', 'size:12'],
            'tuition_fee.*' => ['nullable', 'numeric', 'min:0'],
            'admission_fee' => ['nullable', 'numeric', 'min:0'],
            'examination_fee' => ['nullable', 'numeric', 'min:0'],
            'other_fees' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $totalTuition = collect($request->input('tuition_fee'))->sum();
            $annualFeesTotal = $totalTuition + $request->input('admission_fee', 0) + ($request->input('examination_fee', 0) * 2) + $request->input('other_fees', 0);

            $feePlan = StudentFeePlan::updateOrCreate(
                [ 'student_id' => $student->id, 'school_id' => auth()->user()->school_id, 'year' => $request->year, ],
                [
                    'admission_fee' => $request->input('admission_fee', 0),
                    'examination_fee' => $request->input('examination_fee', 0),
                    'other_fees' => $request->input('other_fees', 0),
                    'total_annual_fees' => $annualFeesTotal,
                ]
            );

            for ($month = 1; $month <= 12; $month++) {
                if($request->input("tuition_fee.$month", 0) > 0 )   {
                    MonthlyTuitionFee::updateOrCreate(
                        [ 'student_fee_plan_id' => $feePlan->id, 'month' => $month, ],
                        [ 'tuition_fee' => $request->input("tuition_fee.$month", 0), ]
                    );
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save fee plan. Error: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('fees.plans.index')->with('success', "Fee plan for {$student->user->name} has been saved.");
    }
}

