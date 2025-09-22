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
        // Add the 'deleted_at' column to the students table
        Schema::table('students', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add the 'deleted_at' column to the teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

