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
        Schema::table('remittances', function (Blueprint $table) {
            $table->timestamp('payed_at')->nullable();
            $table->decimal('payment_system_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remittances', function (Blueprint $table) {
            $table->dropColumn(['payed_at', 'payment_system_amount']);
        });
    }
};
