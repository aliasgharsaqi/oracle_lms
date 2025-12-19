<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('student_diaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('subject')->index(); 
            $table->date('date')->index();
            $table->text('homework')->nullable();
            $table->text('teacher_notes')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->unique(['student_id', 'subject', 'date']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_diaries');
    }
};