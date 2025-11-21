<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_checkin_checkout_to_student_attendances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $table->timestamp('check_in')->nullable()->after('status');
            $table->timestamp('check_out')->nullable()->after('check_in');
            // Add leave status columns to match the teacher attendance
            $table->string('leave_type')->nullable()->after('check_in');
            $table->string('leave_status')->nullable()->after('leave_type');
        });
    }

    public function down()
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropColumn(['check_in', 'check_out', 'leave_type', 'leave_status']);
        });
    }
};