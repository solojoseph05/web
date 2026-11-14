<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Amplía las tablas de promociones y menú con vigencia programada, términos,
 * porcentajes/precios promocionales y control de platillos agotados.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('landing_promotions')) {
            Schema::table('landing_promotions', function (Blueprint $table) {
                if (!Schema::hasColumn('landing_promotions', 'subtitle')) {
                    $table->string('subtitle', 255)->nullable()->after('title');
                }
                if (!Schema::hasColumn('landing_promotions', 'valid_from')) {
                    $table->date('valid_from')->nullable()->after('image_url');
                }
                if (!Schema::hasColumn('landing_promotions', 'terms_conditions')) {
                    $table->text('terms_conditions')->nullable()->after('code');
                }
                if (!Schema::hasColumn('landing_promotions', 'discount_percentage')) {
                    $table->decimal('discount_percentage', 5, 2)->nullable()->after('badge');
                }
                if (!Schema::hasColumn('landing_promotions', 'promo_price')) {
                    $table->decimal('promo_price', 10, 2)->nullable()->after('discount_percentage');
                }
            });
        }

        if (Schema::hasTable('landing_menu_items')) {
            Schema::table('landing_menu_items', function (Blueprint $table) {
                if (!Schema::hasColumn('landing_menu_items', 'is_sold_out')) {
                    $table->boolean('is_sold_out')->default(false)->after('is_featured');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('landing_promotions')) {
            Schema::table('landing_promotions', function (Blueprint $table) {
                foreach (['subtitle', 'valid_from', 'terms_conditions', 'discount_percentage', 'promo_price'] as $col) {
                    if (Schema::hasColumn('landing_promotions', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('landing_menu_items') && Schema::hasColumn('landing_menu_items', 'is_sold_out')) {
            Schema::table('landing_menu_items', function (Blueprint $table) {
                $table->dropColumn('is_sold_out');
            });
        }
    }
};
