<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up(): void
{
    DB::statement("ALTER TABLE student_fee_vouchers MODIFY COLUMN status ENUM('pending','paid','partial','overdue') DEFAULT 'pending'");
}

public function down(): void
{
    DB::statement("ALTER TABLE student_fee_vouchers MODIFY COLUMN status ENUM('pending','paid','overdue') DEFAULT 'pending'");
}

};
