<?php

namespace Database\Seeders;

use App\Models\FamilyPearlCategory;
use Illuminate\Database\Seeder;

class FamilyPearlSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Citāts', 'citats', 10],
            ['Notikums', 'notikums', 20],
            ['Smieklīga situācija', 'situacija', 30],
            ['Atmiņa', 'atmina', 40],
            ['Cits', 'cits', 50],
        ];

        foreach ($categories as [$name, $slug, $sortOrder]) {
            FamilyPearlCategory::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'is_active' => true, 'sort_order' => $sortOrder],
            );
        }
    }
}
