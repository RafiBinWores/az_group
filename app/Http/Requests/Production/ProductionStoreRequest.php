<?php

namespace App\Http\Requests\Production;

use App\Models\Production;
use Illuminate\Foundation\Http\FormRequest;

class ProductionStoreRequest extends FormRequest
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
            'production_data' => 'required|array|min:1',
            'production_data.*.color' => 'required|string',
            'production_data.*.order_qty' => 'required',
            'production_data.*.cutting_qty' => 'nullable',
            'production_data.*.factory' => 'nullable|string',
            'production_data.*.line' => 'nullable|string',
            'production_data.*.input' => 'nullable',
            'production_data.*.total_input' => 'nullable',
            'production_data.*.output' => 'nullable',
            'production_data.*.total_output' => 'nullable',
            'date' => 'required|date',
        ];
    }

    public function messages():array
    {
        return [
            'order_id.required' => 'The style no field is required.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $exists = Production::where('order_id', $this->order_id)
                ->where('garment_type', $this->garment_type)
                ->where('date', $this->date)
                ->exists();

            if ($exists) {
                $validator->errors()->add('date', 'A report for this style, garment type, and date already exists.');
            }
        });
    }
}
