<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['number', 'invoice_date', 'client', 'client_ice', 'tax_rate', 'subtotal_ht', 'tax_amount', 'total_ttc'])]
class Invoice extends Model
{
    protected function casts(): array
    {
        return ['invoice_date' => 'date', 'tax_rate' => 'decimal:2', 'subtotal_ht' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total_ttc' => 'decimal:2'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}