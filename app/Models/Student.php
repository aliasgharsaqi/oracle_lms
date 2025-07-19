<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'father_name',
        'id_card_number',
        'father_phone',
        'address',
        'school_class_id',
        'section',
        'previous_school_docs',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function feeVouchers()
    {
        return $this->hasMany(StudentFeeVoucher::class);
    }
    public function feePlans()
    {
        return $this->hasMany(StudentFeePlan::class);
    }
}
