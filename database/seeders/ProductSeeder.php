<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = Category::pluck('id', 'slug');

        $products = [
            [
                'category' => 'electronics',
                'name' => 'Wireless Earbuds',
                'desc' => 'Premium sound with active noise cancellation and 30-hour battery life.',
                'price' => 899.00,
                'stock' => 25,
                'alert' => 5,
                'image' => 'earbuds.webp',
            ],
            [
                'category' => 'electronics',
                'name' => 'USB-C Hub',
                'desc' => '7-in-1 multiport hub — HDMI, USB 3.0, SD card, and more.',
                'price' => 600.00,
                'stock' => 15,
                'alert' => 3,
                'image' => 'usb-hun.jpg',
            ],
            [
                'category' => 'electronics',
                'name' => 'Laptop Stand',
                'desc' => 'Adjustable aluminium stand for ergonomic working comfort.',
                'price' => 350.00,
                'stock' => 30,
                'alert' => 5,
                'image' => 'laptop-stand.webp',
            ],
            [
                'category' => 'clothing',
                'name' => 'Cotton T-Shirt',
                'desc' => '100% organic cotton, breathable and ultra-soft fabric.',
                'price' => 300.00,
                'stock' => 50,
                'alert' => 10,
                'image' => 't-shirt.jpg',
            ],
            [
                'category' => 'clothing',
                'name' => 'Denim Jacket',
                'desc' => 'Classic slim-fit denim jacket for every wardrobe.',
                'price' => 1200.00,
                'stock' => 8,
                'alert' => 3,
                'image' => 'denim.webp',
            ],
            [
                'category' => 'books',
                'name' => 'Clean Code',
                'desc' => 'A handbook of agile software craftsmanship by Robert C. Martin.',
                'price' => 550.00,
                'stock' => 0,
                'alert' => 5,
                'image' => 'book.jpg',
            ],
            [
                'category' => 'books',
                'name' => 'Laravel: Up & Running',
                'desc' => 'Build modern PHP apps with Laravel — 3rd Edition.',
                'price' => 650.00,
                'stock' => 3,
                'alert' => 3,
                'image' => 'laravel-book.jpg',
            ],
            [
                'category' => 'home-garden',
                'name' => 'Succulent Plant',
                'desc' => 'Low-maintenance indoor succulent — perfect desk companion.',
                'price' => 200.00,
                'stock' => 5,
                'alert' => 5,
                'image' => 'plant.webp',
            ],
        ];

        foreach ($products as $data) {
            $slug = Str::slug($data['name']);

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryMap[$data['category']] ?? null,
                    'name' => $data['name'],
                    'description' => $data['desc'],
                    'base_price' => $data['price'],
                    'image' => $data['image'],
                    'is_active' => true,
                ]
            );

            Inventory::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity' => $data['stock'],
                    'low_stock_alert' => $data['alert'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
