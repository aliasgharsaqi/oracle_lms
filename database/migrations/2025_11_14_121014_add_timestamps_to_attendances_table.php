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
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('check_in')->nullable()->after('status');
            $table->time('check_out')->nullable()->after('check_in');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // *** FIX: Added dropColumn logic ***
            $table->dropColumn('check_in');
            $table->dropColumn('check_out');
        });
    }
};