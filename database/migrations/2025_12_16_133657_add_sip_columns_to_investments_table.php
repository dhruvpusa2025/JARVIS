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
            if (!Schema::hasColumn('investments', 'sip_frequency')) {
                $table->string('sip_frequency')->nullable()->after('is_sip');
            }
            if (!Schema::hasColumn('investments', 'sip_amount')) {
                $table->decimal('sip_amount', 15, 2)->nullable()->after('sip_frequency');
            }
            if (!Schema::hasColumn('investments', 'sip_date')) {
                $table->integer('sip_date')->nullable()->after('sip_amount');
            }
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
