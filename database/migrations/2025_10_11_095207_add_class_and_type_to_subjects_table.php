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
        Schema::table('subjects', function (Blueprint $table) {
            // Add a nullable foreign key for the class.
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->onDelete('set null');

            // Add a column to define if a subject is 'core' or 'optional'
            $table->enum('type', ['core', 'optional'])->default('core');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['school_class_id']);
            $table->dropColumn('school_class_id');
            $table->dropColumn('type');
        });
    }
};