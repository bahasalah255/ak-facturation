<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invoice_id', 'delivery_note_id', 'delivery_note_number', 'delivery_date', 'designation', 'vehicle', 'amount_ht'])]
class InvoiceItem extends Model
{
    protected function casts(): array
    {
        return ['delivery_date' => 'date', 'amount_ht' => 'decimal:2'];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}