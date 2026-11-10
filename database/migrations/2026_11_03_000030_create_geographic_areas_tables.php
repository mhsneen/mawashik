<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جداول المناطق الجغرافية (تدرّج هرمي):
 *   countries → cities → areas → neighborhoods
 *
 * تدعم الترجمة عبر جداول *_translations منفصلة (نفس نمط باقي المشروع).
 * الأعمدة lat/lng للتكامل مع خرائط Google.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ===== Countries =====
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso_code', 3)->nullable();
            $table->string('phone_code', 5)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('country_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->unique(['country_id', 'locale']);
        });

        // ===== Cities =====
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('country_id');
        });

        Schema::create('city_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->unique(['city_id', 'locale']);
        });

        // ===== Areas (مناطق داخل مدينة) =====
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('city_id');
        });

        Schema::create('area_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->unique(['area_id', 'locale']);
        });

        // ===== Neighborhoods (أحياء داخل منطقة) =====
        Schema::create('neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('area_id');
        });

        Schema::create('neighborhood_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->unique(['neighborhood_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('neighborhood_translations');
        Schema::dropIfExists('neighborhoods');
        Schema::dropIfExists('area_translations');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('city_translations');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('country_translations');
        Schema::dropIfExists('countries');
    }
};
