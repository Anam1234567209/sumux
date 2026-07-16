<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OngkirController extends Controller
{
    public function index()
    {
        return view('admin.cek-ongkir');
    }

    public function destinations(Request $request)
    {
        $data = $request->validate([
            'search' => 'required|string|min:2|max:80',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $response = $this->rajaongkir()
            ->get($this->rajaongkirUrl() . '/destination/domestic-destination', [
                'search' => $data['search'],
                'limit' => $data['limit'] ?? 20,
                'offset' => 0,
            ]);

        if (! $response->successful()) {
            return response()->json([
                'ok' => false,
                'message' => 'Gagal mengambil daftar kota tujuan.',
                'details' => $response->json(),
            ], $response->status());
        }

        $destinations = collect($response->json('data', []))
            ->map(fn ($item) => [
                'id' => $item['id'] ?? null,
                'label' => $item['label'] ?? '-',
                'province_name' => $item['province_name'] ?? '-',
                'city_name' => $item['city_name'] ?? '-',
                'district_name' => $item['district_name'] ?? '-',
                'subdistrict_name' => $item['subdistrict_name'] ?? '-',
                'zip_code' => $item['zip_code'] ?? '-',
            ])
            ->filter(fn ($item) => filled($item['id']))
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $destinations,
        ]);
    }

    public function hitung(Request $request)
    {
        $data = $request->validate([
            'origin' => 'required|integer|min:1',
            'destination' => 'required|integer|min:1|different:origin',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required|string|in:jne,jnt,sicepat,anteraja,tiki,pos,ninja,wahana,lion,sap',
        ]);

        $response = $this->rajaongkir()
            ->asForm()
            ->post($this->rajaongkirUrl() . '/calculate/domestic-cost', $data);

        if (! $response->successful()) {
            return response()->json([
                'ok' => false,
                'message' => 'Gagal menghitung ongkir. Periksa kembali kota, berat, dan kurir.',
                'details' => $response->json(),
            ], $response->status());
        }

        $payload = $response->json();

        return response()->json([
            'ok' => true,
            'data' => $this->formatOngkirResponse($payload),
            'raw' => $payload,
        ]);
    }

    protected function rajaongkir()
    {
        $apiKey = config('services.rajaongkir.key');

        abort_if(blank($apiKey) || blank($this->rajaongkirUrl()), 500, 'Konfigurasi RajaOngkir belum lengkap.');

        return Http::withHeaders([
            'key' => $apiKey,
        ])->acceptJson()->timeout(15);
    }

    protected function rajaongkirUrl(): string
    {
        return config('services.rajaongkir.url', '');
    }

    protected function formatOngkirResponse(array $payload): array
    {
        if (isset($payload['rajaongkir']['results'][0]['costs'])) {
            $result = $payload['rajaongkir']['results'][0];
            $name = $result['name'] ?? '';

            return array_map(function ($service) use ($name) {
                $cost = $service['cost'][0] ?? [];

                return [
                    'name' => $name,
                    'service' => $service['service'] ?? '',
                    'description' => $service['description'] ?? '',
                    'cost' => isset($cost['value']) ? number_format($cost['value'], 0, ',', '.') : null,
                    'etd' => $cost['etd'] ?? null,
                ];
            }, $result['costs']);
        }

        if (isset($payload['data']) && is_array($payload['data'])) {
            return array_map(function ($item) {
                if (isset($item['cost'])) {
                    $item['cost'] = is_numeric($item['cost']) ? number_format($item['cost'], 0, ',', '.') : $item['cost'];
                }

                if (isset($item['etd']) && is_string($item['etd'])) {
                    $item['etd'] = str_replace(' day', ' hari', $item['etd']);
                }

                return $item;
            }, $payload['data']);
        }

        return [];
    }
}
