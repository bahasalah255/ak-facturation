<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\DeliveryNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(DeliveryNote::query()->with(['items', 'clientRecord'])->latest('delivery_date')->latest('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $deliveryNote = $this->persist($data);
        return response()->json($deliveryNote, 201);
    }

    public function update(Request $request, DeliveryNote $deliveryNote): JsonResponse
    {
        $data = $this->validated($request);
        $deliveryNote->items()->delete();
        $deliveryNote = $this->persist($data, $deliveryNote);
        return response()->json($deliveryNote);
    }

    public function destroy(DeliveryNote $deliveryNote): JsonResponse
    {
        $deliveryNote->delete();
        return response()->json(null, 204);
    }

    private function persist(array $data, ?DeliveryNote $deliveryNote = null): DeliveryNote
    {
        return DB::transaction(function () use ($data, $deliveryNote) {
            $client = Client::findOrFail($data['client_id']);
            $items = collect($data['items'])->map(fn (array $item) => [
                ...$item,
                'amount_ht' => round((float) $item['quantity'] * (float) $item['unit_price'], 2),
            ]);
            $attributes = [...collect($data)->except('items')->all(), 'client' => $client->company_name, 'client_ice' => $client->ice, 'total_ht' => $items->sum('amount_ht')];
            $deliveryNote ??= new DeliveryNote();
            $deliveryNote->fill($attributes);
            $deliveryNote->save();
            $deliveryNote->items()->createMany($items->all());
            return $deliveryNote->load(['items', 'clientRecord']);
        });
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'number' => ['required', 'string', 'max:100'],
            'delivery_date' => ['required', 'date'],
            'order_number' => ['nullable', 'string', 'max:100'],
            'transport_mode' => ['nullable', 'string', 'max:100'],
            'carrier' => ['nullable', 'string', 'max:255'],
            'permit_number' => ['nullable', 'string', 'max:100'],
            'vehicle' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.article_code' => ['nullable', 'string', 'max:100'],
            'items.*.designation' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
            'items.*.unit' => ['nullable', 'string', 'max:30'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
