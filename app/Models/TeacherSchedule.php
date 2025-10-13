<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\SoftDeletes;


class TeacherSchedule extends Model
{
        use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'school_id',
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'day_of_week' => 'array', 
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

        protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}