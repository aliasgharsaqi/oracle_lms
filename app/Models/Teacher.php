<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'id_card_number',
        'date_of_birth',
        'education',
        'school_id',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all attendance records for the teacher.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceRecord()
    {
        return $this->hasOne(Attendance::class);
    }
    /**
     * Get the attendance record for today.
     */
    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)->where('date', today());
    }

    // --- NEW RELATIONSHIP FOR DIARY/TASKS ---
    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }
    // --- END NEW RELATIONSHIP ---

    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}