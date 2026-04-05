<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'total_price',
        'payment_method',
        'payment_status',
        'gateway_reference',
        'gateway_response',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'gateway_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get human-readable payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'tunai' => 'Tunai',
            default => strtoupper($this->payment_method),
        };
    }
}
