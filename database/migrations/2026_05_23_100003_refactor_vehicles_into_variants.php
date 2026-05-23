<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add nullable model_id so we can backfill before enforcing
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('model_id')->nullable()->after('id')->constrained('vehicle_models')->nullOnDelete();
        });

        // 2. Backfill from existing make/model string columns (if any rows exist)
        if (Schema::hasColumn('vehicles', 'make') && Schema::hasColumn('vehicles', 'model')) {
            $rows = DB::table('vehicles')->select('id', 'make', 'model')->get();
            foreach ($rows as $row) {
                $makeId = DB::table('vehicle_makes')->where('name', $row->make)->value('id')
                    ?? DB::table('vehicle_makes')->insertGetId([
                        'name'       => $row->make,
                        'slug'       => Str::slug($row->make),
                        'is_active'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                $modelId = DB::table('vehicle_models')
                    ->where('make_id', $makeId)
                    ->where('name', $row->model)
                    ->value('id')
                    ?? DB::table('vehicle_models')->insertGetId([
                        'make_id'    => $makeId,
                        'name'       => $row->model,
                        'slug'       => Str::slug($row->model),
                        'is_active'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                DB::table('vehicles')->where('id', $row->id)->update(['model_id' => $modelId]);
            }
        }

        // 3. Drop old unique index, then the redundant string columns
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique(['make', 'model', 'generation', 'year_start']);
            $table->dropIndex(['make', 'model']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['make', 'model']);
        });

        // 4. Enforce non-null and add the new uniqueness rule
        Schema::table('vehicles', function (Blueprint $table) {
            // SQLite needs a separate change call; doctrine/dbal not required for FK alter on Laravel 11+
            $table->unique(['model_id', 'generation', 'year_start']);
            $table->index('model_id');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('make')->nullable()->after('id');
            $table->string('model')->nullable()->after('make');
        });

        $rows = DB::table('vehicles')
            ->join('vehicle_models', 'vehicles.model_id', '=', 'vehicle_models.id')
            ->join('vehicle_makes', 'vehicle_models.make_id', '=', 'vehicle_makes.id')
            ->select('vehicles.id', 'vehicle_makes.name as make', 'vehicle_models.name as model')
            ->get();

        foreach ($rows as $row) {
            DB::table('vehicles')->where('id', $row->id)->update([
                'make'  => $row->make,
                'model' => $row->model,
            ]);
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique(['model_id', 'generation', 'year_start']);
            $table->dropIndex(['model_id']);
            $table->dropForeign(['model_id']);
            $table->dropColumn('model_id');
            $table->unique(['make', 'model', 'generation', 'year_start']);
            $table->index(['make', 'model']);
        });
    }
};
