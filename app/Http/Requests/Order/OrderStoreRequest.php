<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|numeric',
            'buyer_name' => 'required|string',
            'style_no' => 'required|string|unique:orders,style_no',
            'garment_types' => 'required|array|min:1',
            'garment_types.*' => 'exists:garment_types,id',
            'order_quantity' => 'required|numeric',
            'color_qty' => 'required|array|min:1',
            'color_qty.*.color' => 'required|string',
            'color_qty.*.qty' => 'required',
        ];
    }
}
