<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'license_plate' => $this->license_plate,
            'price_per_day' => (float) $this->price_per_day,
            'is_available' => (bool) $this->is_available,
            'location' => new LocationResource($this->whenLoaded('location')),
        ];
    }
}
