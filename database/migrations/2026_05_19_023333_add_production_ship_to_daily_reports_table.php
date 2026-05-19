<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('daily_reports', 'production_ship')) {
            Schema::table('daily_reports', function (Blueprint $table) {
                $table->decimal('production_ship', 15, 2)->nullable()->after('production_cm');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('daily_reports', 'production_ship')) {
            Schema::table('daily_reports', function (Blueprint $table) {
                $table->dropColumn('production_ship');
            });
        }
    }
};