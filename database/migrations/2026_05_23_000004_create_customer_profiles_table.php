<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('plan')->default('Pro'); // Pro | Trade | VIP
            $table->unsignedInteger('credit_balance')->default(0);
            $table->unsignedInteger('total_spent_pennies')->default(0);
            $table->string('country', 2)->nullable();
            $table->string('phone')->nullable();
            $table->string('workshop')->nullable();
            $table->timestamp('since_at')->nullable();
            $table->timestamps();
            $table->index('plan');
        });
    }
    public function down(): void { Schema::dropIfExists('customer_profiles'); }
};
