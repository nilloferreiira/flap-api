<?php

namespace App\Http\Requests\Clients;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'companyName'     => 'required|string|max:255',
            'cnpj'            => 'required|string|max:20|unique:clients,cnpj',
            'address'         => 'required|string|max:255',
            'primaryContact'  => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'email'           => 'required|email|max:255|unique:clients,email',
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
            'companyName.required'    => 'O nome da empresa é obrigatório.',
            'companyName.string'      => 'O nome da empresa deve ser um texto.',
            'companyName.max'         => 'O nome da empresa deve ter no máximo 255 caracteres.',
            'cnpj.required'           => 'O CNPJ é obrigatório.',
            'cnpj.string'             => 'O CNPJ deve ser um texto.',
            'cnpj.max'                => 'O CNPJ deve ter no máximo 20 caracteres.',
            'cnpj.unique'             => 'Este CNPJ já está cadastrado.',
            'address.required'        => 'O endereço é obrigatório.',
            'address.string'          => 'O endereço deve ser um texto.',
            'address.max'             => 'O endereço deve ter no máximo 255 caracteres.',
            'primaryContact.required' => 'O contato principal é obrigatório.',
            'primaryContact.string'   => 'O contato principal deve ser um texto.',
            'primaryContact.max'      => 'O contato principal deve ter no máximo 255 caracteres.',
            'phone.required'          => 'O telefone é obrigatório.',
            'phone.string'            => 'O telefone deve ser um texto.',
            'phone.max'               => 'O telefone deve ter no máximo 20 caracteres.',
            'email.required'          => 'O e-mail é obrigatório.',
            'email.email'             => 'O e-mail deve ser válido.',
            'email.max'               => 'O e-mail deve ter no máximo 255 caracteres.',
            'email.unique'            => 'Este e-mail já está cadastrado.',
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
                'message' => 'Erro de validação. ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
