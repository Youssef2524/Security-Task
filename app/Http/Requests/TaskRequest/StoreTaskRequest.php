<?php

namespace App\Http\Requests\TaskRequest;
use App\Http\Controllers\Controller;
use App\Rules\IsDeveloper;
use App\Rules\UserExistsInProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
{
   

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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Bug,Feature,Improvement',
            'status' => 'required|in:Open,In Progress,Completed,Blocked',
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'required|date',
            'assigned_to' => 'required|exists:users,id',
            'dependencies' => 'nullable',
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
