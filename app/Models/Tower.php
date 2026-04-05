<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tower extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'delivery_fee',
        'is_active',
    ];

    protected $casts = [
        'delivery_fee' => 'integer',
        'is_active' => 'boolean',
    ];

    public function diningTables(): HasMany
    {
        return $this->hasMany(DiningTable::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
