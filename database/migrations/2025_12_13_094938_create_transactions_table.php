<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Optional: Link to a user/admin
            $table->enum('type', ['Income', 'Expense']);
            $table->decimal('amount', 10, 2);
            $table->string('recipient_source'); // E.g., 'John Doe', 'Vendor X', 'Salary'
            $table->text('description')->nullable();
            $table->enum('status', ['Completed', 'Pending', 'Failed'])->default('Completed');
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};