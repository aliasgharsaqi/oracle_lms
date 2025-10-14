<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_fee_vouchers', function (Blueprint $table) {
            // Add school_id for multi-tenancy and better querying
            if (!Schema::hasColumn('student_fee_vouchers', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade')->after('student_id');
            }

            // Add columns to store the fee breakdown for detailed receipts
            $table->decimal('tuition_fee', 10, 2)->default(0)->after('amount_due');
            $table->decimal('admission_fee', 10, 2)->default(0)->after('tuition_fee');
            $table->decimal('examination_fee', 10, 2)->default(0)->after('admission_fee');
            $table->decimal('other_fees', 10, 2)->default(0)->after('examination_fee');
            $table->decimal('arrears', 10, 2)->default(0)->after('other_fees');
        });
    }

    public function down()
    {
        Schema::table('student_fee_vouchers', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn(['school_id', 'tuition_fee', 'admission_fee', 'examination_fee', 'other_fees', 'arrears']);
        });
    }
};
