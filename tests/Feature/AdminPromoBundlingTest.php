<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPromoBundlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_promo_from_promo_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Coffee',
            'icon' => '☕',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $menu = MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Espresso',
            'price' => 20000,
            'is_available' => true,
        ]);

        $token = 'test-token';
        $response = $this->actingAs($admin)
            ->withSession(['_token' => $token])
            ->post(route('admin.menus.promos.store'), [
                '_token' => $token,
                'menu_item_id' => $menu->id,
                'promo_type' => 'promo',
                'promo_title' => 'Diskon Pagi',
                'promo_original_price' => '25.000',
                'promo_sort_order' => 0,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('menu_items', [
            'id' => $menu->id,
            'is_promo' => 1,
            'promo_type' => 'promo',
            'promo_title' => 'Diskon Pagi',
            'promo_sort_order' => 0,
        ]);
    }

    public function test_admin_can_open_promo_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-open@example.com',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Coffee',
            'icon' => '☕',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.menus.promos'));
        $response->assertStatus(200);
    }

    public function test_admin_can_store_bundling(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin2@example.com',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Food',
            'icon' => '🍽️',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $menu = MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Paket Brunch',
            'price' => 35000,
            'is_available' => true,
        ]);

        $token = 'test-token-2';
        $storeResponse = $this->actingAs($admin)
            ->withSession(['_token' => $token])
            ->post(route('admin.menus.promos.store'), [
                '_token' => $token,
                'menu_item_id' => $menu->id,
                'promo_type' => 'bundling',
                'promo_title' => 'Bundle Hemat',
                'promo_sort_order' => 1,
            ]);

        $storeResponse->assertRedirect();
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('menu_items', [
            'id' => $menu->id,
            'is_promo' => 1,
            'promo_type' => 'bundling',
            'promo_title' => 'Bundle Hemat',
        ]);

    }
}
