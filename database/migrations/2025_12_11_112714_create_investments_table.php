<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // mutual_fund, stock, fd, real_estate
            $table->decimal('units', 15, 4)->nullable();
            $table->decimal('buy_price', 15, 2)->nullable();
            $table->decimal('current_price', 15, 2)->nullable();
            $table->decimal('invested_amount', 15, 2);
            $table->decimal('current_value', 15, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->date('maturity_date')->nullable();
            $table->boolean('is_sip')->default(false);
            $table->decimal('sip_amount', 15, 2)->nullable();
            $table->integer('sip_date')->nullable(); // Day of month
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
