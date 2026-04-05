<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getServiceFee(): int
    {
        return (int) static::get('service_fee', 2000);
    }

    public static function getQrisImage(): ?string
    {
        return static::get('qris_image');
    }

    public static function getStoreName(): string
    {
        return static::get('store_name', 'Kantin Industri Batang');
    }
}
