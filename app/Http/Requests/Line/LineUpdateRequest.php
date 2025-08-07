<?php

namespace App\Http\Requests\Line;

use Illuminate\Foundation\Http\FormRequest;

class LineUpdateRequest extends FormRequest
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
        $line = $this->route('line');
        $id = $line?->id ?? null;

        return [
            'name' => 'required|string|unique:lines,name,' . $id,
            'lineStatus' => 'required|boolean',
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
            'lineStatus.required' => 'The status field is required.',
            'lineStatus.boolean' => 'Status must be true or false.',
        ];
    }
}
