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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
        });
        Schema::table('school_classes', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
        });
        // Add this to any other tables that need to be scoped by school
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
