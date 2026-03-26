<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics',   'slug' => 'electronics'],
            ['name' => 'Clothing',      'slug' => 'clothing'],
            ['name' => 'Books',         'slug' => 'books'],
            ['name' => 'Home & Garden', 'slug' => 'home-garden'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
