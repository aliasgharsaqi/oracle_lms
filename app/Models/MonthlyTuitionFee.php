<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyTuitionFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_plan_id',
        'month',
        'tuition_fee',
    ];

    public function studentFeePlan()
    {
        return $this->belongsTo(StudentFeePlan::class);
    }
}
