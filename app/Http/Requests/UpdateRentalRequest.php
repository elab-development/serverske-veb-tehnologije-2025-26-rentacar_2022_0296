<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|exists:users,id',
            'car_id' => 'sometimes|required|exists:cars,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'total_price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ];
    }
}
