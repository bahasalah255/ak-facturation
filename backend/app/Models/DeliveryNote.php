<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['client_id', 'number', 'delivery_date', 'client', 'client_ice', 'order_number', 'transport_mode', 'carrier', 'permit_number', 'vehicle', 'total_ht'])]
class DeliveryNote extends Model
{
    protected function casts(): array
    {
        return ['delivery_date' => 'date', 'total_ht' => 'decimal:2'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    public function clientRecord(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}