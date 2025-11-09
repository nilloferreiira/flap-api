<?php

namespace App\Http\Requests\Clients;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClientRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'companyName'     => 'sometimes|string|max:255',
            'cnpj'            => 'sometimes|string|max:20',
            'address'         => 'sometimes|string|max:255',
            'primaryContact'  => 'sometimes|string|max:255',
            'phone'           => 'sometimes|string|max:20',
            'email'           => 'sometimes|email|max:255',
            'avatarUrl'       => 'nullable|string|max:255',
            'agentUrl'        => 'nullable|string|max:255',
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'companyName.string'      => 'O nome da empresa deve ser um texto.',
            'companyName.max'         => 'O nome da empresa deve ter no máximo 255 caracteres.',
            'cnpj.string'             => 'O CNPJ deve ser um texto.',
            'cnpj.max'                => 'O CNPJ deve ter no máximo 20 caracteres.',
            'address.string'          => 'O endereço deve ser um texto.',
            'address.max'             => 'O endereço deve ter no máximo 255 caracteres.',
            'primaryContact.string'   => 'O contato principal deve ser um texto.',
            'primaryContact.max'      => 'O contato principal deve ter no máximo 255 caracteres.',
            'phone.string'            => 'O telefone deve ser um texto.',
            'phone.max'               => 'O telefone deve ter no máximo 20 caracteres.',
            'email.email'             => 'O e-mail deve ser válido.',
            'email.max'               => 'O e-mail deve ter no máximo 255 caracteres.',
            'avatarUrl.string'        => 'A URL do avatar deve ser um texto.',
            'avatarUrl.max'           => 'A URL do avatar deve ter no máximo 255 caracteres.',
            'agentUrl.string'         => 'A URL do agente deve ser um texto.',
            'agentUrl.max'            => 'A URL do agente deve ter no máximo 255 caracteres.',
        ];
    }

    /**
     * Manipula falhas de validação.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
