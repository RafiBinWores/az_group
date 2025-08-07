<?php

namespace App\Http\Requests\GarmentType;

use Illuminate\Foundation\Http\FormRequest;

class GarmentTypeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get the current GarmentType id (adjust key if needed)
        $garmentType = $this->route('garment_type');
        $id = $garmentType?->id ?? null;

        return [
            'garment_type' => 'required|string|unique:garment_types,name,' . $id,
            'garmentTypeStatus' => 'required|boolean',
        ];
    }
}
