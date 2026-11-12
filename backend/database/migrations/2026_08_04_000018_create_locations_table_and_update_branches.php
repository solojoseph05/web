<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla locations si no existe
        if (!Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->id();
                $table->string('branch_name')->nullable();
                $table->string('name')->nullable();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('schedule')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->integer('map_zoom')->default(16);
                $table->string('google_maps_url', 600)->nullable();
                $table->string('map_link', 600)->nullable();
                $table->string('image_url', 600)->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_open')->default(true);
                $table->boolean('is_coming_soon')->default(false);
                $table->boolean('is_new')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Asegurar que la tabla landing_branches cuente con todas las columnas
        if (Schema::hasTable('landing_branches')) {
            Schema::table('landing_branches', function (Blueprint $table) {
                if (!Schema::hasColumn('landing_branches', 'branch_name')) {
                    $table->string('branch_name')->nullable()->after('name');
                }
                if (!Schema::hasColumn('landing_branches', 'latitude')) {
                    $table->decimal('latitude', 10, 8)->nullable()->after('schedule');
                }
                if (!Schema::hasColumn('landing_branches', 'longitude')) {
                    $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
                }
                if (!Schema::hasColumn('landing_branches', 'map_zoom')) {
                    $table->integer('map_zoom')->default(16)->after('longitude');
                }
                if (!Schema::hasColumn('landing_branches', 'google_maps_url')) {
                    $table->string('google_maps_url', 600)->nullable()->after('map_zoom');
                }
                if (!Schema::hasColumn('landing_branches', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('is_open');
                }
                if (!Schema::hasColumn('landing_branches', 'is_coming_soon')) {
                    $table->boolean('is_coming_soon')->default(false)->after('is_active');
                }
                if (!Schema::hasColumn('landing_branches', 'is_new')) {
                    $table->boolean('is_new')->default(false)->after('is_coming_soon');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
