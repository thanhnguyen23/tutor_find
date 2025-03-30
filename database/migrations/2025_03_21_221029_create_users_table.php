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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('about_you')->nullable();
            $table->integer('student_number')->nullable();
            $table->integer('lesson_number')->nullable();
            $table->integer('rating_number')->default(0);
            $table->integer('price')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('role')->default(0)->comment('0: student, 1: tutor');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
