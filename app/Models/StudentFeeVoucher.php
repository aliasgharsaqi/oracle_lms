<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'voucher_month',
        'amount_due',
        'late_fee_fine',
        'due_date',
        'status',
        'paid_at',
        'amount_paid',
        'payment_method',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
