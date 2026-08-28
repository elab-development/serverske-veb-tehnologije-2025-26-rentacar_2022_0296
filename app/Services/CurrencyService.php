<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    public function getExchangeRates(): array
    {
        return Cache::remember('exchange_rates_rsd', 43200, function () {
            try {
                $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/RSD');

                if ($response->successful()) {
                    return $response->json()['rates'] ?? [];
                }
            } catch (\Exception $e) {

            }

            return [
                'EUR' => 0.0085,
                'USD' => 0.0093,
            ];
        });
    }

    public function convertFromRsd(float $amountRsd, string $targetCurrency = 'EUR'): float
    {
        $rates = $this->getExchangeRates();
        $rate = $rates[strtoupper($targetCurrency)] ?? 1;

        return round($amountRsd * $rate, 2);
    }
}
