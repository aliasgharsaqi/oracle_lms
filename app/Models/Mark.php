<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\SchoolScope; // <-- Import the scope

class Mark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'subject_id',
        'semester_id',
        'school_id',
        'class_id',         // <-- Added class_id
        'school_class_id',  // <-- This was already here
        'total_marks',
        'obtained_marks',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Apply the global scope to automatically filter by school
        static::addGlobalScope(new SchoolScope);
    }

    // --- EXISTING RELATIONSHIPS ---

    /**
     * Get the school that the mark belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the student associated with the mark.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject associated with the mark.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the semester associated with the mark.
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the school class that the mark belongs to.
     * This links the 'school_class_id' column to the 'school_classes' table.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
