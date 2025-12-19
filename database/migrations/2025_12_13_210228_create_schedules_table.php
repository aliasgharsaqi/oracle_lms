<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade'); // Assuming classes table is 'school_classes'
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');

            // Schedule Details
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            
            $table->timestamps();

            // Unique constraint to prevent the same teacher teaching the same subject/class at the exact same time/day
            $table->unique(['teacher_id', 'day_of_week', 'start_time', 'end_time']); 
            
            // Optional: Index for quick lookups by day/class
            $table->index(['day_of_week', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};