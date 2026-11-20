<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('family_pearls')
            ->where('status', 'pending')
            ->update([
                'status' => 'published',
                'published_at' => DB::raw('COALESCE(published_at, created_at)'),
            ]);

        Schema::table('family_pearls', function (Blueprint $table) {
            $table->string('status', 20)->default('published')->change();
        });
    }

    public function down(): void
    {
        Schema::table('family_pearls', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });
    }
};
