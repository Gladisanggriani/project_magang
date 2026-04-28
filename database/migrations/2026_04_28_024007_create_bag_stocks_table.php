<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bag_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->cascadeOnDelete();
            $table->string('bag_type');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit')->default('lembar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bag_stocks');
    }
};