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
        Schema::table('investments', function (Blueprint $table) {
            $table->string('sip_frequency')->nullable()->after('is_sip');
            $table->decimal('sip_amount', 15, 2)->nullable()->after('sip_frequency');
            $table->integer('sip_date')->nullable()->after('sip_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['sip_frequency', 'sip_amount', 'sip_date']);
        });
    }
};
