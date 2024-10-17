<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updatePermissionRequest extends FormRequest
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
            'name' => 'nullable|unique:roles,name',
            // 'description' => 'nullable',
            // 'permission_id' => 'required|array', 
            // 'permission_id.*' => 'exists:permissions,id' 



        ];
    }
}
