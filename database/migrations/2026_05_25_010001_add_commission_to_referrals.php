<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->unsignedBigInteger('referred_total_spend_pennies')->default(0)->after('credited_at');
            $table->unsignedBigInteger('commission_earned_pennies')->default(0)->after('referred_total_spend_pennies');
            $table->unsignedSmallInteger('current_tier')->default(0)->after('commission_earned_pennies');
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn(['referred_total_spend_pennies', 'commission_earned_pennies', 'current_tier']);
        });
    }
};
