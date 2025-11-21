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
        });
    }

    public function down()
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropColumn(['check_in', 'check_out']);
        });
    }
};