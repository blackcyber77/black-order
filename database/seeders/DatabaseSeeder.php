<?php

namespace Database\Seeders;

use App\Models\User;

use App\Models\Category;
use App\Models\Tower;
use App\Models\DiningTable;
use App\Models\Setting;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@kantin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);


        // Create Categories
        $categories = [
            ['name' => 'Makanan Berat', 'icon' => '🍚', 'sort_order' => 1],
            ['name' => 'Lauk Pauk', 'icon' => '🍖', 'sort_order' => 2],
            ['name' => 'Minuman', 'icon' => '🥤', 'sort_order' => 3],
            ['name' => 'Snack', 'icon' => '🍿', 'sort_order' => 4],
            ['name' => 'Gorengan', 'icon' => '🍟', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            Category::create($cat + ['is_active' => true]);
        }

        // Create Towers and Dining Tables
        $towers = [
            ['name' => 'Tower A', 'delivery_fee' => 2000],
            ['name' => 'Tower B', 'delivery_fee' => 3000],
            ['name' => 'Tower C', 'delivery_fee' => 4000],
        ];

        foreach ($towers as $t) {
            $tower = Tower::create($t + ['is_active' => true]);
            
            for ($i = 1; $i <= 10; $i++) {
                $table_number = (string) $i;
                $table = DiningTable::create([
                    'tower_id' => $tower->id,
                    'table_number' => $table_number,
                    'is_active' => true,
                ]);
                $table->update(['qr_code' => url('/menu?table=' . $table->table_number)]);
            }
        }

        // Create Settings
        Setting::set('store_name', 'Kantin Industri Batang');
        Setting::set('service_fee', 2000);
        Setting::set('store_address', 'Area Industri Batang, Jawa Tengah');
        Setting::set('store_phone', '0285-123456');

        // Create Sample Menu Items
        $menus = [
            ['name' => 'Nasi Goreng', 'category_id' => 1, 'price' => 15000, 'description' => 'Nasi goreng spesial dengan telur dan ayam'],
            ['name' => 'Nasi Ayam Geprek', 'category_id' => 1, 'price' => 18000, 'description' => 'Ayam geprek pedas dengan nasi hangat'],
            ['name' => 'Mie Goreng', 'category_id' => 1, 'price' => 13000, 'description' => 'Mie goreng dengan sayuran segar'],
            ['name' => 'Ayam Bakar', 'category_id' => 2, 'price' => 12000, 'description' => 'Ayam bakar bumbu kecap'],
            ['name' => 'Telur Dadar', 'category_id' => 2, 'price' => 5000, 'description' => 'Telur dadar tebal'],
            ['name' => 'Tempe Goreng', 'category_id' => 2, 'price' => 3000, 'description' => 'Tempe goreng krispy'],
            ['name' => 'Es Teh Manis', 'category_id' => 3, 'price' => 5000, 'description' => 'Teh manis dingin segar'],
            ['name' => 'Es Jeruk', 'category_id' => 3, 'price' => 7000, 'description' => 'Jeruk segar diperas'],
            ['name' => 'Kopi Hitam', 'category_id' => 3, 'price' => 5000, 'description' => 'Kopi hitam original'],
            ['name' => 'Kerupuk', 'category_id' => 4, 'price' => 2000, 'description' => 'Kerupuk renyah'],
            ['name' => 'Pisang Goreng', 'category_id' => 5, 'price' => 5000, 'description' => '5 pcs pisang goreng'],
            ['name' => 'Tahu Goreng', 'category_id' => 5, 'price' => 5000, 'description' => '5 pcs tahu goreng krispy'],
        ];

        foreach ($menus as $menu) {
            MenuItem::create([
                'category_id' => $menu['category_id'],
                'name' => $menu['name'],
                'description' => $menu['description'],
                'price' => $menu['price'],
                'is_available' => true,
            ]);
        }
    }
}
