<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //TODO logica de roles
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'required|string|max:255',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);

        throw new HttpResponseException($response);
    }

    public function messages()
    {
        return [
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'O e-mail já está em uso por outro usuário.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
        ];
    }
}
