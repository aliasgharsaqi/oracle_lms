<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'id_card_number',
        'date_of_birth',
        'education',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
