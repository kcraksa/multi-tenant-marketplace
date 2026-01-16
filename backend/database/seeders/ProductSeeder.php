<?php

namespace Database\Seeders;

use App\Domain\Product\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Premium Wireless Headphones',
                'slug' => 'premium-wireless-headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and premium sound quality.',
                'price' => 299.99,
                'compare_at_price' => 399.99,
                'sku' => 'AUDIO-001',
                'quantity' => 50,
                'status' => 'active',
                'featured' => true,
                'track_inventory' => true,
            ],
            [
                'name' => 'Smart Fitness Watch',
                'slug' => 'smart-fitness-watch',
                'description' => 'Track your fitness goals with this feature-rich smartwatch with heart rate monitoring.',
                'price' => 199.99,
                'compare_at_price' => 249.99,
                'sku' => 'WATCH-001',
                'quantity' => 100,
                'status' => 'active',
                'featured' => true,
                'track_inventory' => true,
            ],
            [
                'name' => 'Portable Bluetooth Speaker',
                'slug' => 'portable-bluetooth-speaker',
                'description' => 'Compact and powerful Bluetooth speaker with 12-hour battery life.',
                'price' => 79.99,
                'sku' => 'AUDIO-002',
                'quantity' => 75,
                'status' => 'active',
                'featured' => false,
                'track_inventory' => true,
            ],
            [
                'name' => 'Wireless Gaming Mouse',
                'slug' => 'wireless-gaming-mouse',
                'description' => 'Professional gaming mouse with customizable buttons and RGB lighting.',
                'price' => 89.99,
                'compare_at_price' => 119.99,
                'sku' => 'GAMING-001',
                'quantity' => 60,
                'status' => 'active',
                'featured' => true,
                'track_inventory' => true,
            ],
            [
                'name' => 'USB-C Fast Charger',
                'slug' => 'usb-c-fast-charger',
                'description' => '65W USB-C fast charger compatible with laptops, phones, and tablets.',
                'price' => 39.99,
                'sku' => 'ACC-001',
                'quantity' => 150,
                'status' => 'active',
                'featured' => false,
                'track_inventory' => true,
            ],
            [
                'name' => '4K Webcam',
                'slug' => '4k-webcam',
                'description' => 'Professional 4K webcam with auto-focus and noise-canceling microphone.',
                'price' => 149.99,
                'sku' => 'CAM-001',
                'quantity' => 40,
                'status' => 'active',
                'featured' => false,
                'track_inventory' => true,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'slug' => 'mechanical-keyboard',
                'description' => 'Premium mechanical keyboard with RGB backlight and custom switches.',
                'price' => 159.99,
                'compare_at_price' => 199.99,
                'sku' => 'GAMING-002',
                'quantity' => 45,
                'status' => 'active',
                'featured' => true,
                'track_inventory' => true,
            ],
            [
                'name' => 'Laptop Stand',
                'slug' => 'laptop-stand',
                'description' => 'Ergonomic aluminum laptop stand for better posture and cooling.',
                'price' => 49.99,
                'sku' => 'ACC-002',
                'quantity' => 80,
                'status' => 'active',
                'featured' => false,
                'track_inventory' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }
    }
}
