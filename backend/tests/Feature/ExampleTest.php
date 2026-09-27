<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_a_delivery_note_can_generate_an_invoice(): void
    {
        $client = Client::create([
            'company_name' => 'SUPER CERAME',
            'ice' => '00000505000042',
            'address' => 'CASA',
        ]);

        $deliveryNote = $this->postJson('/api/delivery-notes', [
            'client_id' => $client->id,
            'number' => '21 263',
            'delivery_date' => '2026-08-10',
            'order_number' => '388 872',
            'transport_mode' => 'R01',
            'carrier' => 'AK RAPID TRANS',
            'permit_number' => '1992/D/40',
            'vehicle' => '16069/A/75',
            'items' => [[
                'article_code' => 'AR101C06C',
                'designation' => 'ARMAN 101 30X60 COMMERCIAL',
                'quantity' => 1,
                'unit' => 'Caisse',
                'unit_price' => 1897.92,
            ]],
        ])->assertCreated();

        $invoice = $this->postJson('/api/invoices/generate', [
            'delivery_note_ids' => [$deliveryNote->json('id')],
            'invoice_date' => '2026-08-10',
            'tax_rate' => 10,
        ])->assertCreated();

        $invoice->assertJsonPath('client', 'SUPER CERAME')
            ->assertJsonPath('subtotal_ht', '1897.92')
            ->assertJsonPath('tax_amount', '189.79')
            ->assertJsonPath('total_ttc', '2087.71');
    }
}
