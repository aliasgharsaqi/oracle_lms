<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
protected $fillable = [
    'student_id',
    'school_id',
    'school_class_id',
    'attendance_date',
    'status',
    'check_in',     // <-- ADD
    'check_out',    // <-- ADD
    'remarks',
    'leave_type',   // <-- ADD
    'leave_status', // <-- ADD
];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attendance_date' => 'date',
    ];

    /**
     * Apply the school scope by default.
     */
    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

    /**
     * Get the student that owns the attendance record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the school class that owns the attendance record.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Get the school that owns the attendance record.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}