<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\SoftDeletes;
class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'father_name',
        'id_card_number',
        'father_phone',
        'address',
        'school_id',
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

    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
