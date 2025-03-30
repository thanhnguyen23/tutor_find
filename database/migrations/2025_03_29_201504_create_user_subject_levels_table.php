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
        Schema::create('user_subject_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_subject_id');
            $table->unsignedBigInteger('education_level_id');
            $table->timestamps();

            $table->foreign('user_subject_id')->references('id')->on('user_subjects');
            $table->foreign('education_level_id')->references('id')->on('education_levels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subject_levels');
    }
};
