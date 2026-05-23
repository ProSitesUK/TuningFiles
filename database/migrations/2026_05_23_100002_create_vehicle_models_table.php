<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('make_id')->constrained('vehicle_makes')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('body_type')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['make_id', 'slug']);
            $table->index('make_id');
        });
    }

    public function down(): void { Schema::dropIfExists('vehicle_models'); }
};
