<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para crear la tabla persistente de reseñas (`reviews`) en MySQL / SQLite.
 */
return new class extends Migration
{
    /**
     * Ejecuta la creación de la tabla `reviews` con índices para moderación y sucursales.
     */
    public function up(): void
    {
        if (Schema::hasTable('reviews')) {
            return;
        }

        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('customer_name', 120);
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('comment');
            $table->string('branch', 160)->nullable();
            $table->string('favorite_dish', 160)->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->string('source', 60)->default('Web');
            $table->boolean('is_google_review')->default(false);
            $table->string('status', 30)->default('approved');
            $table->unsignedInteger('likes_count')->default(0);
            $table->json('admin_reply')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('rating');
        });
    }

    /**
     * Revierte la migración eliminando la tabla `reviews`.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
