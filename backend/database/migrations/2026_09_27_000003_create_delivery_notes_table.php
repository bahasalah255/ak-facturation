<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->date('delivery_date');
            $table->string('client');
            $table->string('client_ice')->nullable();
            $table->string('order_number')->nullable();
            $table->string('transport_mode')->nullable();
            $table->string('carrier')->nullable();
            $table->string('permit_number')->nullable();
            $table->string('vehicle')->nullable();
            $table->decimal('total_ht', 12, 2);
            $table->timestamps();
            $table->unique(['number', 'delivery_date']);
        });

        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained()->cascadeOnDelete();
            $table->string('article_code')->nullable();
            $table->string('designation');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount_ht', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_note_items');
        Schema::dropIfExists('delivery_notes');
    }
};