<?php

namespace App\Http\Requests\TaskRequest;

use App\Rules\IsDeveloper;
use App\Rules\UserExistsInProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
{
    
    // stop validation in the first failure
    // protected $stopOnFirstFailure = false;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:Bug,Feature,Improvement',
            'priority' => 'nullable|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
           
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException($this->errorResponse($errors, 'Validation error', 422));
    }

    public function errorResponse($data, $message, $code)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code,
        ], $code);
    }

}
