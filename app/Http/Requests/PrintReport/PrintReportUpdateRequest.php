<?php

namespace App\Http\Requests\PrintReport;

use App\Models\PrintReport;
use Illuminate\Foundation\Http\FormRequest;

class PrintReportUpdateRequest extends FormRequest
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
            'print_data' => 'required|array|min:1',
            'print_data.*.color' => 'required|string',
            'print_data.*.factory' => 'nullable|string',
            'print_data.*.send' => 'nullable',
            'print_data.*.received' => 'nullable',
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
            $id = $this->route('print') ?? $this->id;

            $exists = PrintReport::where('order_id', $this->order_id)
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
