<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rakps', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->string('material_name')->default('Semen');
            $table->decimal('value', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['year', 'month', 'material_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rakps');
    }
};