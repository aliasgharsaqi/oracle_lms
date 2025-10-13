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
        // USERS
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // STUDENTS
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // TEACHERS
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // SCHOOL CLASSES
        Schema::table('school_classes', function (Blueprint $table) {
            if (!Schema::hasColumn('school_classes', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // SUBJECTS
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // teacher_schedules
        Schema::table('teacher_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_schedules', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            }
        });
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
