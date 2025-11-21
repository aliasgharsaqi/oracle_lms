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
        Schema::table('student_attendances', function (Blueprint $table) {
            // These are the columns your controller is trying to use
            $table->timestamp('check_in')->nullable()->after('status');
            $table->timestamp('check_out')->nullable()->after('check_in');
            $table->string('leave_type')->nullable()->after('remarks');
            $table->string('leave_status')->nullable()->after('leave_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropColumn(['check_in', 'check_out', 'leave_type', 'leave_status']);
        });
    }
};