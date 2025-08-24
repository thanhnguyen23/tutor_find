<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('level_id')->constrained('education_levels')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('number_of_lessons');
            $table->integer('duration'); // in minutes
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('user_package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_package_id')->constrained()->onDelete('cascade');
            $table->string('feature');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_package_features');
        Schema::dropIfExists('user_packages');
    }
};
