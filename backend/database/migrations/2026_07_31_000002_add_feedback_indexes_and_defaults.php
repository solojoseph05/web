<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->index('email');
            $table->index('created_at');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE feedbacks ALTER COLUMN rating SET DEFAULT '5 estrellas'");
            DB::statement("ALTER TABLE feedbacks ALTER COLUMN type SET DEFAULT 'Contacto'");
        }
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['created_at']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE feedbacks ALTER COLUMN rating DROP DEFAULT');
            DB::statement('ALTER TABLE feedbacks ALTER COLUMN type DROP DEFAULT');
        }
    }
};
