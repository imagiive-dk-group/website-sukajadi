<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('slug_en')->unique();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('category')->default('makanan');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('placeholder_label')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('address')->nullable();
            $table->string('maps_url', 1000)->nullable();
            $table->json('opening_hours')->nullable();
            $table->boolean('accepts_orders')->default(true);
            $table->boolean('accepts_visits')->default(false);
            $table->boolean('show_contact')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('umkm_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            $table->unsignedInteger('price_min')->nullable();
            $table->unsignedInteger('price_max')->nullable();
            $table->json('unit')->nullable();
            $table->string('image_path')->nullable();
            $table->string('placeholder_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('package_umkm', function (Blueprint $table) {
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('umkm_id')->constrained()->cascadeOnDelete();
            $table->primary(['package_id', 'umkm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_umkm');
        Schema::dropIfExists('umkm_products');
        Schema::dropIfExists('umkms');
    }
};
