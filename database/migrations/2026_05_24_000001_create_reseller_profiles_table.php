<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reseller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('commission_percent')->default(0);
            $table->unsignedSmallInteger('max_customers')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('reseller_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->index('reseller_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('reseller_id')->nullable()->after('customer_id')->constrained('users')->nullOnDelete();
            $table->index('reseller_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['reseller_id']);
            $table->dropColumn('reseller_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reseller_id']);
            $table->dropColumn('reseller_id');
        });
        Schema::dropIfExists('reseller_profiles');
    }
};
