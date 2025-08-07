<?php

namespace App\Http\Requests\Embroidery;

use App\Models\Embroidery;
use Illuminate\Foundation\Http\FormRequest;

class EmbroideryStoreRequest extends FormRequest
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
            'order_id' => 'required|numeric',
            'garment_type' => 'required|string',
            'embroidery_data' => 'required|array|min:1',
            'embroidery_data.*.color' => 'required|string',
            'embroidery_data.*.factory' => 'nullable|string',
            'embroidery_data.*.send' => 'nullable',
            'embroidery_data.*.received' => 'nullable',
            'date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'The style no field is required.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $exists = Embroidery::where('order_id', $this->order_id)
                ->where('garment_type', $this->garment_type)
                ->where('date', $this->date)
                ->exists();

            if ($exists) {
                $validator->errors()->add('date', 'A report for this style, garment type, and date already exists.');
            }
        });
    }
}
