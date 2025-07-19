<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'event_image', 
        'date',
        'time',
        'expected_guest',
        'heading',
        'address',
        'entry',
        'desc',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
