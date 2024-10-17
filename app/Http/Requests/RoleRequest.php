<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize()
 {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|unique:roles,name',
            // 'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم الدور مطلوب.',
            'name.unique' => 'اسم الدور موجود بالفعل.',
            'description.string' => 'الوصف يجب أن يكون نصاً.',
        ];
    }
}
