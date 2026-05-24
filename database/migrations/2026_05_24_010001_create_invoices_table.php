<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('credit_pack_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('amount_pennies');
            $table->unsignedInteger('credits');
            $table->string('status')->default('draft'); // draft|sent|paid|overdue|cancelled
            $table->string('payment_terms')->default('net_30');
            $table->string('reference')->unique();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }
    public function down(): void { Schema::dropIfExists('invoices'); }
};
