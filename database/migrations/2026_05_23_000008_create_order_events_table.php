<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('stage');  // 'file received','validated','assigned','tuning in progress','review','delivery'
            $table->string('state')->default('pending'); // done | active | pending
            $table->text('note')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('happened_at')->useCurrent();
            $table->timestamps();
            $table->index(['order_id', 'happened_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('order_events'); }
};
