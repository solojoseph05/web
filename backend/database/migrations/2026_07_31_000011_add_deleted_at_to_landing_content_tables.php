<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_jobs', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('landing_menu_items', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('landing_jobs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('landing_menu_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
