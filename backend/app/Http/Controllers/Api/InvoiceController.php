<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Invoice::query()->with('items')->latest('invoice_date')->latest('id')->get());
    }

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'delivery_note_ids' => ['required', 'array', 'min:1'],
            'delivery_note_ids.*' => ['integer', 'distinct', 'exists:delivery_notes,id'],
            'invoice_date' => ['required', 'date'],
            'number' => ['nullable', 'string', 'max:100', 'unique:invoices,number'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $invoice = DB::transaction(function () use ($data) {
            $deliveryNotes = DeliveryNote::query()->whereKey($data['delivery_note_ids'])->get();
            abort_if($deliveryNotes->pluck('client')->unique()->count() > 1, 422, 'Les bons de livraison doivent avoir le même client.');
            $subtotal = round($deliveryNotes->sum('total_ht'), 2);
            $tax = round($subtotal * ((float) $data['tax_rate'] / 100), 2);
            $number = $data['number'] ?? sprintf('%d/%s', Invoice::query()->count() + 1, date('Y', strtotime($data['invoice_date'])));
            $invoice = Invoice::create([
                'number' => $number, 'invoice_date' => $data['invoice_date'], 'client' => $deliveryNotes->first()->client,
                'client_ice' => $deliveryNotes->first()->client_ice, 'tax_rate' => $data['tax_rate'],
                'subtotal_ht' => $subtotal, 'tax_amount' => $tax, 'total_ttc' => $subtotal + $tax,
            ]);
            $invoice->items()->createMany($deliveryNotes->map(fn (DeliveryNote $note) => [
                'delivery_note_id' => $note->id, 'delivery_note_number' => $note->number, 'delivery_date' => $note->delivery_date,
                'designation' => $note->items->pluck('designation')->join(', '), 'vehicle' => $note->vehicle, 'amount_ht' => $note->total_ht,
            ])->all());
            return $invoice->load('items');
        });

        return response()->json($invoice, 201);
    }
}