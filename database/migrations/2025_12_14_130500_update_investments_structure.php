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
        Schema::create('investment_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "My Zerodha"
            $table->string('broker'); // e.g., "ZERODHA", "COIN"
            $table->string('account_number')->nullable();
            $table->timestamps();
        });

        Schema::table('investments', function (Blueprint $table) {
            $table->foreignId('investment_account_id')->nullable()->constrained('investment_accounts')->onDelete('cascade');
            $table->string('isin')->nullable()->index(); // Unique ID for stocks/MFs
            $table->string('symbol')->nullable()->after('name');
            $table->string('sector')->nullable();

            // Extended fields for Zerodha support
            $table->decimal('previous_close_price', 15, 2)->nullable();
            $table->decimal('unrealized_pnl', 15, 2)->nullable();
            $table->decimal('unrealized_pnl_pct', 8, 2)->nullable();
            $table->decimal('quantity_discrepant', 15, 4)->default(0);
            $table->decimal('quantity_long_term', 15, 4)->default(0);
            $table->decimal('quantity_pledged_margin', 15, 4)->default(0);
            $table->decimal('quantity_pledged_loan', 15, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropForeign(['investment_account_id']);
            $table->dropColumn([
                'investment_account_id',
                'isin',
                'symbol',
                'sector',
                'previous_close_price',
                'unrealized_pnl',
                'unrealized_pnl_pct',
                'quantity_discrepant',
                'quantity_long_term',
                'quantity_pledged_margin',
                'quantity_pledged_loan'
            ]);
        });

        Schema::dropIfExists('investment_accounts');
    }
};
