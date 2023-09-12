<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_plan_id')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->text('activity')->nullable();
            $table->text('details')->nullable();
            $table->text('observation')->nullable();
            $table->text('remarks')->nullable();
            $table->text('monitor_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
