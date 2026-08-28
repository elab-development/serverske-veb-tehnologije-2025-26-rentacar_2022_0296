<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $carId = $this->route('id') ?? $this->route('car');

        return [
            'brand' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'price_per_day' => 'sometimes|required|numeric|min:0',
            'location_id' => 'sometimes|required|exists:locations,id',
            'license_plate' => 'nullable|string|unique:cars,license_plate,' . $carId,
            'is_available' => 'boolean',
        ];
    }
}
