<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Make existing fields nullable
            $table->decimal('emi_amount', 15, 2)->nullable()->change();
            $table->integer('emi_date')->nullable()->change();
            $table->decimal('interest_rate', 5, 2)->nullable()->change();

            // Add new fields
            $table->string('loan_type')->default('BANK')->after('lender'); // BANK, PERSONAL
            $table->string('interest_payment_frequency')->nullable()->after('interest_rate'); // MONTHLY, WEEKLY, NONE
            $table->integer('interest_payment_date')->nullable()->after('interest_payment_frequency'); // Day of month or Day of week (1-7)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('emi_amount', 15, 2)->nullable(false)->change();
            $table->integer('emi_date')->nullable(false)->change();
            $table->decimal('interest_rate', 5, 2)->nullable(false)->change();

            $table->dropColumn(['loan_type', 'interest_payment_frequency', 'interest_payment_date']);
        });
    }
};
