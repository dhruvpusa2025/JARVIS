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
        Schema::create('investment_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->enum('type', ['SIP_INSTALLMENT', 'LUMPSUM', 'SELL', 'DIVIDEND'])->default('SIP_INSTALLMENT');
            $table->decimal('amount', 15, 2); // Money In/Out
            $table->decimal('price', 15, 2)->nullable(); // NAV/Price
            $table->decimal('units', 15, 4)->nullable(); // Units bought/sold
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_entries');
    }
};
