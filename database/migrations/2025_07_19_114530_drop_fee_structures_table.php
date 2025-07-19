<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the foreign key and the table if it exists
        if (Schema::hasTable('fee_structures')) {
            Schema::dropIfExists('fee_structures');
        }
    }

    public function down(): void
    {
        // Optional: You can recreate the table here if you need to roll back
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('fee_type')->default('monthly_tuition');
            $table->timestamps();
        });
    }
};
