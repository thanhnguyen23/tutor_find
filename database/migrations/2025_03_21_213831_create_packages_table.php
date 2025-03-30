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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_id');
            $table->string('name');
            $table->integer('price')->nullable();
            $table->integer('discount_percent')->nullable();
            $table->integer('max_contacts')->nullable();
            $table->integer('months')->nullable();
            $table->integer('years')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->string('icon');
            $table->json('features');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
