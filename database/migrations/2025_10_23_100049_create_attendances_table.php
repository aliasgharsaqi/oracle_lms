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
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->date('date');

            // Stores the final status for the day
            $table->enum('status', [
                'present',
                'absent',
                'leave',
                'short_leave',
                'late_arrival',
            ]);

            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->text('notes')->nullable();                                // For leave reasons
            $table->foreignId('marked_by')->nullable()->constrained('users'); // Admin who marked it

            $table->timestamps();

            // Ensures only one record per teacher per day
            $table->unique(['teacher_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
