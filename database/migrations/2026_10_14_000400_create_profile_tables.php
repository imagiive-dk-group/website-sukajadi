<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_stats', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('identitas');
            $table->json('label');
            $table->json('value');
            $table->string('source')->nullable();
            $table->string('source_year')->nullable();
            $table->boolean('needs_verification')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('profile_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('title');
            $table->json('body')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('position');
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officials');
        Schema::dropIfExists('profile_sections');
        Schema::dropIfExists('profile_stats');
    }
};
