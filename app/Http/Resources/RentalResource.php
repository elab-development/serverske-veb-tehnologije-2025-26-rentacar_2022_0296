<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_price' => (float) $this->total_price,
            'status' => $this->status,
            'notes' => $this->notes,
            'user' => new UserResource($this->whenLoaded('user')),
            'car' => new CarResource($this->whenLoaded('car')),
        ];
    }
}
