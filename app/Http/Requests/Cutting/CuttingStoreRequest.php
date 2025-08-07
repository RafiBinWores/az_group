<?php

namespace App\Http\Requests\Cutting;

use App\Models\Cutting;
use Illuminate\Foundation\Http\FormRequest;

class CuttingStoreRequest extends FormRequest
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
            'garment_type' => 'required|exists:garment_types,name',
            'date' => 'required|date',
            'cutting' => 'required|array|min:1',
            'cutting.*.color' => 'nullable|string',
            'cutting.*.qty' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'The style no field is required.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // You can access $this->order_id etc.
            $exists = Cutting::where('order_id', $this->order_id)
                ->where('garment_type', $this->garment_type)
                ->where('date', $this->date)
                ->exists();

            if ($exists) {
                $validator->errors()->add('date', 'A report for this style, garment type, and date already exists.');
            }
        });
    }
}
