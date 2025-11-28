<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->text('homework_assignment')->nullable(); // The task itself
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'verified'])->default('pending');
            $table->text('teacher_notes')->nullable(); // Notes on completion
            $table->timestamps();

            // Ensures a teacher can only have one specific assignment per class/subject combination
            $table->unique(['teacher_id', 'class_id', 'subject_id', 'due_date'], 'unique_teacher_assignment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_assignments');
    }
};