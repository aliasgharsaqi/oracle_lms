<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\SchoolScope; 

class StudentDiary extends Model
{
    use HasFactory;
    
    protected $table = 'student_diaries'; 

    protected $fillable = [
        'student_id',
        'school_id',
        'subject',
        'date',
        'homework',
        'teacher_notes',
        'status', 
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }
}