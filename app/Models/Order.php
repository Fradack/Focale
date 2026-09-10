<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = [
        'pending' => 'En attente de paiement',
        'paid' => 'Payée',
        'shipped' => 'Expédiée',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
    ];

    protected $fillable = [
        'order_number', 'user_id', 'customer_name', 'customer_email',
        'shipping_option_id', 'shipping_label', 'shipping_price_cents',
        'payment_method_id', 'payment_method_label', 'payment_instructions',
        'status', 'items_total_cents', 'total_cents',
        'customer_note', 'admin_note', 'paid_at', 'shipped_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingOption(): BelongsTo
    {
        return $this->belongsTo(ShippingOption::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function totalFormatted(): string
    {
        return number_format($this->total_cents / 100, 2, ',', ' ').' €';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public static function generateOrderNumber(): string
    {
        return 'FOC-'.now()->format('Y').'-'.strtoupper(Str::random(6));
    }
}
