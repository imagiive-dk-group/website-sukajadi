<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mitra bisnis (wisata sekitar, penginapan, kafe) yang dijual dengan komisi,
 * serta pencatatan prospek (leads) dan klik WhatsApp untuk monitoring.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('slug_en')->unique();
            $table->string('name');
            $table->string('category')->default('wisata');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->json('highlights')->nullable();
            $table->json('opening_hours')->nullable();
            $table->string('address')->nullable();
            $table->string('maps_url', 1000)->nullable();
            $table->string('cover_path')->nullable();
            $table->string('placeholder_label')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10);
            // Data internal (tidak tampil di website)
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('agreement_status')->default('draft');
            $table->text('internal_notes')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('partner_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            $table->unsignedInteger('price');
            $table->string('unit')->default('per_pax');
            $table->unsignedInteger('min_qty')->default(1);
            $table->boolean('is_dummy_price')->default(false);
            $table->string('image_path')->nullable();
            $table->string('placeholder_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('partner_id')->nullable()->after('package_tier_id')->constrained()->nullOnDelete();
            $table->foreignId('partner_offer_id')->nullable()->after('partner_id')->constrained()->nullOnDelete();
            $table->unsignedInteger('nights')->nullable()->after('pax');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('total');
            $table->unsignedBigInteger('commission_amount')->default(0)->after('commission_rate');
            // none | pending | invoiced | paid
            $table->string('commission_status')->default('none')->after('commission_amount');
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            // booking | contact | whatsapp | manual
            $table->string('source')->default('manual');
            // new | contacted | negotiating | won | lost
            $table->string('status')->default('new');
            $table->string('interest')->nullable();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('message_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('estimated_value')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('follow_up_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('locale', 5)->default('id');
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('wa_clicks', function (Blueprint $table) {
            $table->id();
            // general | package | attraction | umkm | product | partner | offer | booking
            $table->string('context_type')->default('general');
            $table->unsignedBigInteger('context_id')->nullable();
            $table->string('page')->nullable();
            $table->string('locale', 5)->default('id');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['context_type', 'context_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_clicks');
        Schema::dropIfExists('leads');
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('partner_offer_id');
            $table->dropConstrainedForeignId('partner_id');
            $table->dropColumn(['nights', 'commission_rate', 'commission_amount', 'commission_status']);
        });
        Schema::dropIfExists('partner_offers');
        Schema::dropIfExists('partners');
    }
};
