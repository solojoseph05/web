<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_promotions', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'landing_promotions_active_sort_idx');
            $table->index('valid_until', 'landing_promotions_valid_until_idx');
        });

        Schema::table('landing_branches', function (Blueprint $table) {
            $table->index(['is_open', 'sort_order'], 'landing_branches_open_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::table('landing_promotions', function (Blueprint $table) {
            $table->dropIndex('landing_promotions_active_sort_idx');
            $table->dropIndex('landing_promotions_valid_until_idx');
        });

        Schema::table('landing_branches', function (Blueprint $table) {
            $table->dropIndex('landing_branches_open_sort_idx');
        });
    }
};

