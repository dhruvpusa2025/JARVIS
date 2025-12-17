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
        Schema::create('recurring_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Salary, Rent, Business
            $table->decimal('amount', 15, 2);
            $table->string('type')->default('SALARY'); // SALARY, BUSINESS, RENT, OTHER
            $table->integer('day_of_month')->default(1); // 1-31
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete(); // Account where income is credited
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_incomes');
    }
};
