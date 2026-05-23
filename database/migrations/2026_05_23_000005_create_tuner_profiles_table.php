<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tuner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status')->default('off'); // live | busy | away | off
            $table->unsignedTinyInteger('workload')->default(0);
            $table->unsignedTinyInteger('capacity')->default(4);
            $table->unsignedTinyInteger('active_count')->default(0);
            $table->string('idle')->nullable();
            $table->json('specialties')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }
    public function down(): void { Schema::dropIfExists('tuner_profiles'); }
};
