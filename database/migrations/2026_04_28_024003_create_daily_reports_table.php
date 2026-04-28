<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();

            $table->date('report_date')->unique();

            $table->string('cement_mill_status')->nullable();
            $table->text('cement_mill_note')->nullable();

            $table->decimal('feed', 12, 2)->default(0);
            $table->decimal('blaine', 12, 2)->default(0);
            $table->decimal('sieving', 12, 2)->default(0);
            $table->decimal('production_cm', 12, 2)->default(0);
            $table->decimal('running_hours', 12, 2)->default(0);
            $table->decimal('clinker_factor', 12, 2)->default(0);
            $table->decimal('silo_semen', 12, 2)->default(0);

            $table->string('packer1_status')->nullable();
            $table->text('packer1_note')->nullable();

            $table->string('packer2_status')->nullable();
            $table->text('packer2_note')->nullable();

            $table->integer('truck_packer_area')->default(0);
            $table->integer('truck_emplacement_area')->default(0);

            $table->decimal('production_packer', 12, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};