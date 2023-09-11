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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable();
            $table->string('exam_type')->nullable();
            $table->date('examination_date')->nullable();
            $table->string('temperature')->nullable();
            $table->string('crt')->nullable();
            $table->text('exam_result')->nullable();
            $table->text('image_result')->nullable();
            $table->text('diagnosis')->nullable();
            $table->integer('price')->nullable();
            // $table->integer('ammount_paid')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
