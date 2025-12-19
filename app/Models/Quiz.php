<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'subject', 
        'status', 
        'school_id', // Assuming this exists in the DB
        'questions', 
        'duration', 
        'due_date',
        'class_id', 
    ];

    protected $casts = [
        'due_date' => 'date',
    ];
    
    /**
     * Define the relationship to the class.
     */
    public function class()
    {
        // FIX: Point the relationship to SchoolClass model
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}