<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_student_leave_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('leave_type', ['full_day', 'short_leave'])->default('full_day');
            $table->text('reason');
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('action_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('action_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_leave_requests');
    }
};