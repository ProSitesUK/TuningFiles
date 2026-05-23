<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ecus', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('family');
            $table->string('variant')->nullable();
            $table->string('identifier')->unique();
            $table->json('supported_tunes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['vendor', 'family']);
        });

        Schema::create('vehicle_ecu', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ecu_id')->constrained()->cascadeOnDelete();
            $table->primary(['vehicle_id', 'ecu_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('vehicle_ecu');
        Schema::dropIfExists('ecus');
    }
};
