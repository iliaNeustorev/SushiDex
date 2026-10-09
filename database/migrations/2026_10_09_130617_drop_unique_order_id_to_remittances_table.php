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
        if (Schema::hasIndex('remittances', ['order_id'], 'unique')) {
            Schema::table('remittances', function (Blueprint $table) {
                $table->dropUnique(['order_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remittances', function (Blueprint $table) {
            $table->unique('order_id');
        });
    }
};
