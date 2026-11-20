<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_pearl_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('family_pearls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('family_pearl_categories')->restrictOnDelete();
            $table->string('title', 160);
            $table->text('text');
            $table->text('context')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('status', 20)->default('published');
            $table->text('moderation_note')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['category_id', 'status']);
        });

        Schema::create('family_pearl_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pearl_id')->constrained('family_pearls')->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('path');
            $table->string('disk', 20)->default('private');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_pearl_media');
        Schema::dropIfExists('family_pearls');
        Schema::dropIfExists('family_pearl_categories');
    }
};
