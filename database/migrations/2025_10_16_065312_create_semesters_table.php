<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_semesters_table.php

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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Fall 2024"
            $table->year('year');
            $table->enum('season', ['Spring', 'Fall', 'Summer', 'Winter']);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active'); // e.g., "active" or "inactive"
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('semesters');
    }
};