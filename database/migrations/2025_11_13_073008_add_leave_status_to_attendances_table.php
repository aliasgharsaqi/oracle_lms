<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveStatusToAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // This column will store the MAIN status (present, absent, etc.)
            // We should rename 'status' to 'attendance_status' for clarity
            // But for now, we will add a new column for leave.
            
            $table->string('leave_status')
                  ->nullable()
                  ->after('status')
                  ->comment('pending, approved, rejected');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('leave_status');
        });
    }
}