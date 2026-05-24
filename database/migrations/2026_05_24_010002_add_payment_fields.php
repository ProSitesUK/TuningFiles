<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('stripe_payment_intent'); // stripe|bank|invoice
            $table->string('payment_status')->nullable()->after('payment_method');        // completed|pending|failed
        });

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->boolean('can_invoice')->default(false)->after('workshop');
        });
    }

    public function down(): void
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status']);
        });
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropColumn('can_invoice');
        });
    }
};
