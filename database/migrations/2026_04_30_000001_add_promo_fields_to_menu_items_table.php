<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->boolean('is_promo')->default(false)->after('is_available');
            $table->string('promo_type')->nullable()->after('is_promo'); // promo | bundling
            $table->string('promo_title')->nullable()->after('promo_type');
            $table->decimal('promo_original_price', 12, 2)->nullable()->after('promo_title');
            $table->unsignedInteger('promo_sort_order')->default(0)->after('promo_original_price');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn([
                'is_promo',
                'promo_type',
                'promo_title',
                'promo_original_price',
                'promo_sort_order',
            ]);
        });
    }
};
