<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'tower_id',
        'table_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'payment_method',
        'payment_status',
        'payment_proof',
        'payment_gateway',
        'payment_gateway_ref',
        'payment_gateway_url',
        'payment_gateway_token',
        'payment_expiry',
        'paid_at',
        'subtotal',
        'service_fee',
        'delivery_fee',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_expiry' => 'datetime',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Payment methods (cashless via iPaymu gateway)
     */
    const PAYMENT_QRIS = 'qris';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';

    /**
     * Payment methods available for QR ordering
     */
    public static function customerPaymentMethods(): array
    {
        return [self::PAYMENT_QRIS];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }



    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function getFullLocationAttribute(): string
    {
        if ($this->table_number) {
            return 'Meja ' . $this->table_number;
        }

        return '-';
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->total, 0, ',', '.');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            default => strtoupper($this->payment_method),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'delivering' => 'Diantar',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar',
            'verified' => 'Terverifikasi',
            'expired' => 'Kadaluarsa',
            'failed' => 'Gagal',
            default => $this->payment_status,
        };
    }

    /**
     * Check if payment has expired (for gateway-generated payments)
     */
    public function isPaymentExpired(): bool
    {
        if (!$this->payment_expiry) {
            return false;
        }
        return now()->isAfter($this->payment_expiry);
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(?string $gatewayRef = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_gateway_ref' => $gatewayRef ?? $this->payment_gateway_ref,
        ]);

        if ($this->transaction) {
            $this->transaction->update([
                'payment_status' => 'paid',
                'gateway_reference' => $gatewayRef ?? $this->payment_gateway_ref,
            ]);
        }
    }

    /**
     * Check if this is a cashless payment (from QR ordering)
     */
    public function isCashless(): bool
    {
        return in_array($this->payment_method, [self::PAYMENT_QRIS, self::PAYMENT_BANK_TRANSFER]);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeAwaitingPayment($query)
    {
        return $query->where('payment_status', 'pending')
                     ->whereIn('payment_method', [self::PAYMENT_QRIS, self::PAYMENT_BANK_TRANSFER]);
    }


}
