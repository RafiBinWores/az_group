<?php

namespace App\Http\Requests\Line;

use Illuminate\Foundation\Http\FormRequest;

class LineStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
       public function rules(): array
    {
        return [
            'name' => 'required|string|unique:lines,name',
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
