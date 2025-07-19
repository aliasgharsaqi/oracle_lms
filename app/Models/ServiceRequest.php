<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'club',
        'location',
        'services',
    ];

    protected $casts = [
        'services' => 'array',
    ];
}