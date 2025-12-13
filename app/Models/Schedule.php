<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'teacher_id',
        'class_id',
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];
    
    /**
     * The table associated with the model.
     * * @var string
     */
    protected $table = 'schedules'; 
    
    // Relationships (As required by TeacherDiaryController.php -> getTeacherRecord)

    /**
     * Get the school class associated with the schedule.
     * Controller uses: $lecture->schoolClass->name
     */
    public function schoolClass(): BelongsTo
    {
        // Assuming your classes table is 'school_classes' and uses 'id'
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
    
    /**
     * Get the subject associated with the schedule.
     * Controller uses: $lecture->subject->name
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the teacher associated with the schedule.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
    
    /**
     * Get the school associated with the schedule.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}