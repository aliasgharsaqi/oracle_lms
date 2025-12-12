<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'class_id',
        'subject_id',
        'homework_assignment',
        'due_date',
        'status',
        'teacher_notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

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
}