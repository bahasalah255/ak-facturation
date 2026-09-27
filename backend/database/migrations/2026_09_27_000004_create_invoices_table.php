<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('invoice_date');
            $table->string('client');
            $table->string('client_ice')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(10);
            $table->decimal('subtotal_ht', 12, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->decimal('total_ttc', 12, 2);
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_note_id')->nullable()->constrained()->nullOnDelete();
            $table->string('delivery_note_number');
            $table->date('delivery_date');
            $table->string('designation');
            $table->string('vehicle')->nullable();
            $table->decimal('amount_ht', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};