<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_pearl_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pearl_id')->constrained('family_pearls')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('emoji', 16);
            $table->timestamps();

            $table->unique(['pearl_id', 'user_id']);
            $table->index(['pearl_id', 'emoji']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_pearl_reactions');
    }
};
