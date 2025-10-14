<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_id',
        'year',
        'admission_fee',
        'examination_fee',
        'other_fees',
        'total_annual_fees',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * A fee plan has 12 monthly tuition fee records.
     * THIS IS THE FIX.
     */
    public function monthlyTuitionFees()
    {
        return $this->hasMany(MonthlyTuitionFee::class);
    }
}

