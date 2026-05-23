<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('credit_pack_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // purchase | spend | refund | adjust | promo
            $table->integer('credits');     // signed
            $table->integer('balance_after');
            $table->unsignedInteger('amount_pennies')->nullable();
            $table->string('stripe_payment_intent')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }
    public function down(): void { Schema::dropIfExists('credit_transactions'); }
};
