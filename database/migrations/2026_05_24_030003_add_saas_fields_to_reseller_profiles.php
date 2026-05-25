<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reseller_profiles', function (Blueprint $table) {
            $table->string('subscription_status')->nullable();
            $table->string('custom_domain')->nullable();
            $table->boolean('domain_verified')->default(false);
            $table->string('brand_color')->nullable()->default('#e65100');
            $table->timestamp('trial_ends_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reseller_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_status',
                'custom_domain',
                'domain_verified',
                'brand_color',
                'trial_ends_at',
            ]);
        });
    }
};
