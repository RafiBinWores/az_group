<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'name.required' => 'The role name is required.',
    //         'name.unique' => 'This role name already exists.',
    //         'permissions.required' => 'Please select at least one permission.',
    //         'permissions.*.exists' => 'One or more selected permissions are invalid.',
    //     ];
    // }
}
