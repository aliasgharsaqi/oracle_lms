<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\SchoolScope; 

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'created_by',
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

    /**
     * Get the school that owns the class.
     */
    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
    
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'school_class_id');
    }
}

