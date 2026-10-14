<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom bertipe json yang berisi {"id": "...", "en": "..."} adalah kolom dua bahasa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('slug_en')->unique();
            $table->json('name');
            $table->string('category')->default('edukasi');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->json('duration')->nullable();
            $table->json('itinerary')->nullable();
            $table->json('facilities')->nullable();
            $table->json('target_groups')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('placeholder_label')->nullable();
            $table->unsignedInteger('daily_capacity')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('package_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('duration')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('min_pax')->default(1);
            $table->json('inclusions')->nullable();
            $table->json('badge')->nullable();
            $table->boolean('is_dummy_price')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('attractions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('slug_en')->unique();
            $table->json('name');
            $table->string('category')->default('alam');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('placeholder_label')->nullable();
            $table->unsignedInteger('custom_price')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('attraction_package', function (Blueprint $table) {
            $table->foreignId('attraction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->primary(['attraction_id', 'package_id']);
        });

        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('slug_en')->unique();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('category')->default('alam');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('photoable');
            $table->string('path')->nullable();
            $table->json('caption')->nullable();
            $table->string('placeholder_label')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('slug_en')->unique();
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('placeholder_label')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->json('question');
            $table->json('answer');
            $table->string('category')->default('umum');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('content');
            $table->string('locale', 5)->default('id');
            $table->string('photo_path')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact');
            $table->text('message');
            $table->string('locale', 5)->default('id');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['messages', 'testimonials', 'faqs', 'posts', 'photos', 'albums', 'attraction_package', 'attractions', 'package_tiers', 'packages', 'settings'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
