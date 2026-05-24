<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dyno_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('vehicle_make');
            $table->string('vehicle_model');
            $table->string('variant_label')->nullable();
            $table->unsignedSmallInteger('stock_hp');
            $table->unsignedSmallInteger('tuned_hp');
            $table->unsignedSmallInteger('stock_torque')->nullable();
            $table->unsignedSmallInteger('tuned_torque')->nullable();
            $table->string('tune_type')->default('stage_1');
            $table->string('image_url')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['is_approved', 'is_public']);
            $table->index(['vehicle_make', 'vehicle_model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dyno_results');
    }
};
