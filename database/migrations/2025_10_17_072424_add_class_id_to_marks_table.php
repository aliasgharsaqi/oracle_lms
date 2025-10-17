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
        Schema::table('marks', function (Blueprint $table) {
            // Add the new foreignId column after 'class_id'
            $table->foreignId('school_class_id')
                  ->nullable() // Add this if the value can be optional
                  ->constrained('school_classes') // Explicitly state the table name
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marks', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['school_class_id']);
            // Then drop the column
            $table->dropColumn('school_class_id');
        });
    }
};