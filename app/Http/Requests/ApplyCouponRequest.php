<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coupon_code' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_code.required' => 'Please enter a coupon code.',
        ];
    }
}
