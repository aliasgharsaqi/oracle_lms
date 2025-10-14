<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_id',
        'voucher_month',
        'due_date',
        'status',
        // Fee Breakdown (What is due)
        'amount_due',
        'tuition_fee',
        'admission_fee',
        'examination_fee',
        'other_fees',
        'arrears',
        // Payment Breakdown (What was paid)
        'amount_paid',
        'paid_tuition',
        'paid_admission',
        'paid_examination',
        'paid_other',
        'paid_arrears',
        // Payment Meta
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'voucher_month' => 'date',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

