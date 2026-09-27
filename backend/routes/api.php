<?php

use App\Http\Controllers\Api\DeliveryNoteController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/delivery-notes', [DeliveryNoteController::class, 'index']);
Route::post('/delivery-notes', [DeliveryNoteController::class, 'store']);
Route::put('/delivery-notes/{deliveryNote}', [DeliveryNoteController::class, 'update']);
Route::delete('/delivery-notes/{deliveryNote}', [DeliveryNoteController::class, 'destroy']);
Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'update', 'destroy']);
Route::get('/invoices', [InvoiceController::class, 'index']);
Route::post('/invoices/generate', [InvoiceController::class, 'generate']);