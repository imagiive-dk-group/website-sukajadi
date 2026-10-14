<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('description')->nullable();
            $table->unsignedInteger('price');
            $table->string('unit')->default('per_pax');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('package_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->index('date');
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type')->default('package');
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_tier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('package_name');
            $table->date('visit_date');
            $table->unsignedInteger('pax');
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('organization')->nullable();
            $table->string('group_type')->default('family');
            $table->text('notes')->nullable();
            $table->string('locale', 5)->default('id');
            $table->unsignedInteger('price_per_pax')->default(0);
            $table->json('addons')->nullable();
            $table->json('custom_items')->nullable();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('addons_total')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->index(['visit_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('blocked_dates');
        Schema::dropIfExists('addons');
    }
};
