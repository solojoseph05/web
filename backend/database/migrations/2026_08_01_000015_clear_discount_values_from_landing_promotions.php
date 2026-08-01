<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('landing_promotions')->update(['discount' => null]);
    }

    public function down(): void
    {
        // No se restaura información histórica al revertir esta limpieza.
    }
};
