<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('family_pearls', 'person_id')) {
            Schema::table('family_pearls', function (Blueprint $table) {
                $table->dropForeign(['person_id']);
            });
        }

        Schema::table('family_pearls', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['person_id', 'event_date'],
                fn (string $column) => Schema::hasColumn('family_pearls', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('family_people');
    }

    public function down(): void
    {
        Schema::create('family_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('family_pearls', function (Blueprint $table) {
            $table->foreignId('person_id')->nullable()->after('category_id')->constrained('family_people')->nullOnDelete();
            $table->date('event_date')->nullable()->after('context');
        });
    }
};
