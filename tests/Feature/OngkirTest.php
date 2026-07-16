<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OngkirTest extends TestCase
{
    public function test_destinations_can_be_searched(): void
    {
        config([
            'services.rajaongkir.key' => 'test-key',
            'services.rajaongkir.url' => 'https://rajaongkir.test/api/v1',
        ]);

        Http::fake([
            'rajaongkir.test/api/v1/destination/domestic-destination*' => Http::response([
                'meta' => [
                    'message' => 'Success Get Domestic Destinations',
                    'code' => 200,
                    'status' => 'success',
                ],
                'data' => [
                    [
                        'id' => 67854,
                        'label' => 'GEDANGAN, REMBANG, REMBANG, JAWA TENGAH, 59219',
                        'province_name' => 'JAWA TENGAH',
                        'city_name' => 'REMBANG',
                        'district_name' => 'REMBANG',
                        'subdistrict_name' => 'GEDANGAN',
                        'zip_code' => '59219',
                    ],
                ],
            ]),
        ]);

        $response = $this->getJson(route('api.ongkir.destinations', [
            'search' => 'Rembang',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.0.id', 67854)
            ->assertJsonPath('data.0.city_name', 'REMBANG');
    }

    public function test_shipping_cost_can_be_calculated(): void
    {
        config([
            'services.rajaongkir.key' => 'test-key',
            'services.rajaongkir.url' => 'https://rajaongkir.test/api/v1',
        ]);

        Http::fake([
            'rajaongkir.test/api/v1/calculate/domestic-cost' => Http::response([
                'meta' => [
                    'message' => 'Success Calculate Domestic Shipping cost',
                    'code' => 200,
                    'status' => 'success',
                ],
                'data' => [
                    [
                        'name' => 'Jalur Nugraha Ekakurir (JNE)',
                        'code' => 'jne',
                        'service' => 'REG',
                        'description' => 'Layanan Reguler',
                        'cost' => 20000,
                        'etd' => '1 day',
                    ],
                ],
            ]),
        ]);

        $response = $this->postJson(route('api.cek-ongkir'), [
            'origin' => 67854,
            'destination' => 17547,
            'weight' => 1000,
            'courier' => 'jne',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.0.service', 'REG')
            ->assertJsonPath('data.0.cost', '20.000')
            ->assertJsonPath('data.0.etd', '1 hari');
    }
}
