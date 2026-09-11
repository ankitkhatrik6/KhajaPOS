<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'sale_id',
        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'payment_method',
        'payment_status',
        'cashier_name',
        'customer_name',
        'restaurant_name',
        'restaurant_address',
        'restaurant_phone',
        'restaurant_pan',
        'restaurant_vat',
        'invoice_footer',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
