<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Client::query()->orderBy('company_name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(Client::create($this->validated($request)), 201);
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $client->update($this->validated($request));
        return response()->json($client->refresh());
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();
        return response()->json(null, 204);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'ice' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}