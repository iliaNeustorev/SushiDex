<?php

use App\Enums\Refunds\Status;
use App\Models\Remittance;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Remittance::class)->unique()->constrained();
            $table->string('external_system_status')->nullable();
            $table->string('external_system_id')->nullable()->unique();
            $table->decimal('amount');
            $table->tinyInteger('status')->default(Status::NEW);
            $table->timestamp('refunded_at')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
