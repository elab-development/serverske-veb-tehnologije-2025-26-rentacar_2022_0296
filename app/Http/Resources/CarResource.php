<?php

namespace App\Http\Resources;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currencyService = app(CurrencyService::class);
        $priceEur = $currencyService->convertFromRsd((float) $this->price_per_day, 'EUR');

        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'license_plate' => $this->license_plate,
            'price_per_day' => (float) $this->price_per_day,
            'price_per_day_eur' => $priceEur,
            'currency' => 'RSD',
            'is_available' => (bool) $this->is_available,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'location' => new LocationResource($this->whenLoaded('location')),
        ];
    }
}
