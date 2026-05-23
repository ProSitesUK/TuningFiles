<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('make');
            $table->string('model');
            $table->string('generation')->nullable();
            $table->unsignedSmallInteger('year_start');
            $table->unsignedSmallInteger('year_end')->nullable();
            $table->string('fuel')->nullable();
            $table->string('displacement')->nullable();
            $table->unsignedSmallInteger('stock_hp')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['make', 'model', 'generation', 'year_start']);
            $table->index(['make', 'model']);
        });
    }
    public function down(): void { Schema::dropIfExists('vehicles'); }
};
