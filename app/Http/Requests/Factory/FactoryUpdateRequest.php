<?php

namespace App\Http\Requests\Factory;

use Illuminate\Foundation\Http\FormRequest;

class FactoryUpdateRequest extends FormRequest
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
        // Get the current Factory id
        $factory = $this->route('factory');
        $id = $factory?->id ?? null;

        return [
            'name' => 'required|string|unique:factories,name,' . $id,
            'factoryStatus' => 'required|boolean',
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
            'factoryStatus.required' => 'The status field is required.',
            'factoryStatus.boolean' => 'Status must be true or false.',
        ];
    }
}
