<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month'); // 1 for Jan, 2 for Feb, etc.
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->unique(['student_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_plans');
    }
};
