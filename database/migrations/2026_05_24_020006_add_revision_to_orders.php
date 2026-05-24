<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('revision_window_ends_at')->nullable();
            $table->unsignedSmallInteger('revision_count')->default(0);
            $table->unsignedSmallInteger('max_revisions')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['revision_window_ends_at', 'revision_count', 'max_revisions']);
        });
    }
};
