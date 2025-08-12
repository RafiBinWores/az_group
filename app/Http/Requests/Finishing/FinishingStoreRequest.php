<?php

namespace App\Http\Requests\Finishing;

use Illuminate\Foundation\Http\FormRequest;

class FinishingStoreRequest extends FormRequest
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
            'order_id' => 'required|numeric',
            'thread_cutting' => 'required|numeric',
            'qc_check' => 'nullable|numeric',
            'button_rivet_attach' => 'nullable|numeric',
            'iron' => 'nullable|numeric',
            'hangtag' => 'nullable|numeric',
            'poly' => 'nullable|numeric',
            'carton' => 'nullable|numeric',
            'today_finishing' => 'nullable|numeric',
            'total_finishing' => 'nullable|numeric',
            'plan_to_complete' => 'nullable|numeric',
            'dpi_inline' => 'nullable|numeric',
            'fri_final' => 'nullable|numeric',
            'date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'The style no field is required.',
        ];
    }
}
