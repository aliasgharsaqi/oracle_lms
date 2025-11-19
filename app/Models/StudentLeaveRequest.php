<?php

// app/Models/StudentLeaveRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_id',
        'start_date',
        'end_date',
        'leave_type',
        'reason',
        'status',
        'action_by_user_id',
        'action_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'action_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by_user_id');
    }
}