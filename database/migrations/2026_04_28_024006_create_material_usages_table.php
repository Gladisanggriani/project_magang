<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->cascadeOnDelete();
            $table->string('material_name');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit')->default('ton');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_usages');
    }
};