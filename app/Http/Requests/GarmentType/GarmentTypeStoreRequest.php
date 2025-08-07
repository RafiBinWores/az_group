<?php

namespace App\Http\Requests\GarmentType;

use Illuminate\Foundation\Http\FormRequest;

class GarmentTypeStoreRequest extends FormRequest
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
            'garment_type' => 'required|string|unique:garment_types,name',
            'garmentTypeStatus' => 'required|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'garment_type.required' => 'The garment type filed is required.',
            'garment_type.string' => 'Garment type must be a string.',
            'garment_type.unique' => 'Garment type must be unique.',
            'garmentTypeStatus.required' => 'The status field is required.',
            'garmentTypeStatus.boolean' => 'Status must be true or false.',
        ];
    }
}
