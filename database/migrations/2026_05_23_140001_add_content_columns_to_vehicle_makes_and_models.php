<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_makes', function (Blueprint $table) {
            $table->string('seo_description', 320)->nullable()->after('image_url');
            $table->text('intro')->nullable()->after('seo_description');
        });

        Schema::table('vehicle_models', function (Blueprint $table) {
            $table->string('seo_description', 320)->nullable()->after('image_url');
            $table->text('intro')->nullable()->after('seo_description');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_models', function (Blueprint $table) {
            $table->dropColumn(['intro', 'seo_description']);
        });
        Schema::table('vehicle_makes', function (Blueprint $table) {
            $table->dropColumn(['intro', 'seo_description']);
        });
    }
};
