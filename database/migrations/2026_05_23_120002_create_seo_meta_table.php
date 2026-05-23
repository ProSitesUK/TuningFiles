<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type', 32); // 'route' | 'make' | 'model' | 'page'
            $table->string('subject_key', 191); // route name or model PK
            $table->string('title')->nullable();
            $table->string('description', 320)->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical')->nullable();
            $table->string('robots', 64)->nullable(); // e.g. 'index,follow' | 'noindex,nofollow'
            $table->json('structured_data')->nullable();
            $table->timestamps();

            $table->unique(['subject_type', 'subject_key']);
            $table->index('subject_type');
        });
    }

    public function down(): void { Schema::dropIfExists('seo_meta'); }
};
