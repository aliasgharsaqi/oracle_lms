<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Student::count();
        $staff =Teacher::count();
        $course = SchoolClass::count();
        return view('admin.dashboard',compact('user','staff','course'));
    }
}
