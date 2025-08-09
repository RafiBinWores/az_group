<?php

namespace App\Http\Requests\Wash;

use App\Models\Wash;
use Illuminate\Foundation\Http\FormRequest;

class WashUpdateRequest extends FormRequest
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
            'wash_data' => 'required|array|min:1',
            'wash_data.*.color' => 'required|string',
            'wash_data.*.factory' => 'nullable|string',
            'wash_data.*.send' => 'nullable',
            'wash_data.*.received' => 'nullable',
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
            $id = $this->route('wash') ?? $this->id;

            $exists = Wash::where('order_id', $this->order_id)
                ->where('garment_type', $this->garment_type)
                ->where('date', $this->date)
                // Ignore current record when editing
                ->when($id, function ($q) use ($id) {
                    $q->where('id', '!=', $id);
                })
                ->exists();

            if ($exists) {
                $validator->errors()->add('date', 'A report for this style, garment type, and date already exists.');
            }
        });
    }
}
