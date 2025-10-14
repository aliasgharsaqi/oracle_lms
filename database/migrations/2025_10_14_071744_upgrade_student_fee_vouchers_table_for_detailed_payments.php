<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_fee_vouchers', function (Blueprint $table) {
              
            // Add columns to store the breakdown of the `amount_paid`
            $table->decimal('paid_tuition', 10, 2)->nullable()->after('amount_paid');
            $table->decimal('paid_admission', 10, 2)->nullable()->after('paid_tuition');
            $table->decimal('paid_examination', 10, 2)->nullable()->after('paid_admission');
            $table->decimal('paid_other', 10, 2)->nullable()->after('paid_examination');
            $table->decimal('paid_arrears', 10, 2)->nullable()->after('paid_other');

            // Add a notes column
            $table->text('notes')->nullable()->after('paid_at');
        });
    }

    public function down()
    {
        Schema::table('student_fee_vouchers', function (Blueprint $table) {
            $table->dropColumn([
                'paid_tuition', 'paid_admission', 'paid_examination', 'paid_other', 'paid_arrears', 'notes'
            ]);
        });
    }
};
