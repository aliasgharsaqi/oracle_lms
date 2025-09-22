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

    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
