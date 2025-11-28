<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'decription',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'subscription_plan',
        'status',
        'start_time', 
        'end_time'
    ];

    /**
     * Get the users associated with the school.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the students associated with the school.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the teachers associated with the school.
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * Get the classes associated with the school.
     */
    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * Get the subjects associated with the school.
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
    
}
