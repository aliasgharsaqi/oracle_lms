<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_tuition_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_fee_plan_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->decimal('tuition_fee', 10, 2)->default(0);
            $table->timestamps();

            // Ensure only one entry per month for each plan
            $table->unique(['student_fee_plan_id', 'month']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_tuition_fees');
    }
};
