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
    Schema::create('leave_applications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        $table->date('date');
        $table->enum('leave_type', ['full_day', 'short_leave'])->default('full_day'); // (Req 1.2)
        $table->text('reason')->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->foreignId('approved_by_admin_id')->nullable()->constrained('users'); // Admin who approved
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_applications');
    }
};
