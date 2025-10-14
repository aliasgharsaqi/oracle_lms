<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_fee_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->year('year');
            $table->decimal('admission_fee', 10, 2)->default(0);
            $table->decimal('examination_fee', 10, 2)->default(0);
            $table->decimal('other_fees', 10, 2)->default(0);
            $table->decimal('total_annual_fees', 10, 2)->default(0); // Sum of the above
            $table->timestamps();
            
            // Ensure a student has only one plan per year
            $table->unique(['student_id', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_fee_plans');
    }
};
