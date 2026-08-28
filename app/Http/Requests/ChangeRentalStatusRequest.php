<?php

namespace App\Http\Requests;

use App\Models\Rental;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeRentalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    Rental::STATUS_PENDING,
                    Rental::STATUS_CONFIRMED,
                    Rental::STATUS_COMPLETED,
                    Rental::STATUS_CANCELLED,
                ]),
            ],
        ];
    }
}
