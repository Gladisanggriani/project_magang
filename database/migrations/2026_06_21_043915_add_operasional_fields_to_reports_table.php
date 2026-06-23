<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->decimal('stock_awal_silo',15,2)->default(0);

        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {

            $table->dropColumn([
                'start_time',
                'end_time',
                'stock_awal_silo'
            ]);

        });
    }
};
