<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'The selected product does not exist.',
            'quantity.min'      => 'Quantity must be at least 1.',
            'quantity.max'      => 'Cannot add more than 100 units at once.',
        ];
    }
}
