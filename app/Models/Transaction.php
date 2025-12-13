<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'recipient_source',
        'description',
        'status',
        'transaction_date',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'float',
    ];

    // Optional: Define relationship if needed
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}