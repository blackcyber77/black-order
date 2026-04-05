<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'tower_id',
        'table_number',
        'qr_code',
        'is_active',
        'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class);
    }

    public function getFullLocationAttribute(): string
    {
        return 'Meja ' . $this->table_number;
    }
}
