<?php

// app/Models/Semester.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'year', 'season', 'start_date', 'end_date','status','school_id'];
}
