<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Menambahkan 2 kolom baru tepat setelah kolom production_packer
            $table->decimal('production_packer1', 15, 2)->default(0)->after('production_packer');
            $table->decimal('production_packer2', 15, 2)->default(0)->after('production_packer1');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn(['production_packer1', 'production_packer2']);
        });
    }
};
