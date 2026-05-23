<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // human ref like 4471
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ecu_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_tuner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status')->default('queued');
            // queued | in_progress | review | ready | delivered | refunded | dispute | failed

            $table->string('origin')->default('customer upload'); // 'customer upload' | 'trade portal' | 'VIP' | 'API'
            $table->string('vehicle_label')->nullable();          // denormalized for display ("Golf R MK7")
            $table->unsignedSmallInteger('vehicle_year')->nullable();
            $table->string('ecu_label')->nullable();              // denormalized ("Bosch MED17.1.62")
            $table->string('options_label')->nullable();          // denormalized ("Stage 1 + EGR off")
            $table->json('options')->nullable();                  // ['stage_1','egr_off']

            $table->unsignedSmallInteger('credits_cost')->default(0);
            $table->string('file_size')->nullable();              // pretty "1.24 MB"
            $table->string('md5_status')->default('ok');
            $table->string('sla')->nullable();                    // "30m", "60m", "4h"
            $table->float('progress')->default(0);
            $table->boolean('breach')->default(false);

            $table->timestamp('queued_at')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('review_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('sla_due_at')->nullable();

            $table->text('customer_note')->nullable();
            $table->text('tuner_note')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index(['assigned_tuner_id', 'status']);
            $table->index('sla_due_at');
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
