<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject_code',
        'school_id',
        'created_by',
        'active',
        'school_class_id', 
        'type',            
    ];

    /**
     * A subject belongs to a class.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    // Remove the old classes() relationship
}