<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'phone' => '08123456789',
                'address' => '123 Admin Street',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12345',
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // Create sample customers
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'phone' => '08111111111',
                'address' => '456 Customer Street',
                'city' => 'Surabaya',
                'state' => 'East Java',
                'country' => 'Indonesia',
                'postal_code' => '60123',
                'is_active' => true,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'phone' => '08222222222',
                'address' => '789 Buyer Avenue',
                'city' => 'Bandung',
                'state' => 'West Java',
                'country' => 'Indonesia',
                'postal_code' => '40123',
                'is_active' => true,
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
                'phone' => '08333333333',
                'address' => '321 Shopper Lane',
                'city' => 'Medan',
                'state' => 'North Sumatra',
                'country' => 'Indonesia',
                'postal_code' => '20123',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customerData) {
            $customer = User::updateOrCreate(
                ['email' => $customerData['email']],
                $customerData
            );
            $customer->assignRole('customer');
        }

        // Create sample products
        $products = [
            [
                'name' => 'Laptop Pro 15',
                'description' => 'High-performance laptop with Intel i9 processor, 32GB RAM, and 1TB SSD. Perfect for professionals and developers.',
                'price' => 1500.00,
                'stock' => 25,
                'sku' => 'LAPTOP-PRO-15-001',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'https://via.placeholder.com/500x500?text=Laptop+Pro+15',
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with precision tracking and long battery life. Compatible with all devices.',
                'price' => 29.99,
                'stock' => 150,
                'sku' => 'MOUSE-WIRELESS-001',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'https://via.placeholder.com/500x500?text=Wireless+Mouse',
            ],
            [
                'name' => 'Mechanical Keyboard RGB',
                'description' => 'Premium mechanical keyboard with RGB lighting, 100+ customizable keys, and Cherry MX switches.',
                'price' => 149.99,
                'stock' => 60,
                'sku' => 'KEYBOARD-RGB-001',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'https://via.placeholder.com/500x500?text=Mechanical+Keyboard',
            ],
            [
                'name' => 'USB-C Hub 7-in-1',
                'description' => 'Multi-port USB-C hub with HDMI, USB 3.0, SD card reader, and power delivery up to 100W.',
                'price' => 59.99,
                'stock' => 80,
                'sku' => 'HUB-USB-7IN1-001',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'https://via.placeholder.com/500x500?text=USB-C+Hub',
            ],
            [
                'name' => 'Monitor 4K UltraWide',
                'description' => 'Stunning 4K UltraWide display with 3440x1440 resolution, perfect for content creators and gamers.',
                'price' => 799.99,
                'stock' => 15,
                'sku' => 'MONITOR-4K-ULTRA-001',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'https://via.placeholder.com/500x500?text=4K+Monitor',
            ],
            [
                'name' => 'Wireless Headphones Pro',
                'description' => 'Noise-canceling wireless headphones with 30-hour battery life and premium sound quality.',
                'price' => 299.99,
                'stock' => 40,
                'sku' => 'HEADPHONES-PRO-001',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'https://via.placeholder.com/500x500?text=Wireless+Headphones',
            ],
            [
                'name' => 'Webcam 1080P HD',
                'description' => 'Professional 1080P HD webcam with auto-focus, built-in microphone, and wide-angle lens.',
                'price' => 79.99,
                'stock' => 70,
                'sku' => 'WEBCAM-1080P-001',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'https://via.placeholder.com/500x500?text=Webcam+1080P',
            ],
            [
                'name' => 'Portable SSD 2TB',
                'description' => 'Ultra-fast portable SSD with 2TB capacity, USB 3.2, and durable design for professionals on the go.',
                'price' => 249.99,
                'stock' => 50,
                'sku' => 'SSD-PORTABLE-2TB-001',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'https://via.placeholder.com/500x500?text=Portable+SSD',
            ],
            [
                'name' => 'Desk Lamp LED',
                'description' => 'Smart LED desk lamp with adjustable brightness, color temperature, and USB charging port.',
                'price' => 49.99,
                'stock' => 100,
                'sku' => 'LAMP-LED-DESK-001',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'https://via.placeholder.com/500x500?text=Desk+Lamp+LED',
            ],
            [
                'name' => 'Phone Stand Premium',
                'description' => 'Aluminum phone stand with adjustable height and angle, compatible with all smartphones and tablets.',
                'price' => 39.99,
                'stock' => 120,
                'sku' => 'STAND-PHONE-PREMIUM-001',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'https://via.placeholder.com/500x500?text=Phone+Stand',
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }
    }
}
