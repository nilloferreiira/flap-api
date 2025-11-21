<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize()
    {
        // Implemente a lógica de permissão aqui se necessário
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome da role é obrigatório.',
            'name.string' => 'O nome da role deve ser um texto.',
            'permissions.required' => 'É obrigatório informar as permissões.',
            'permissions.array' => 'As permissões devem ser um array de IDs.',
            'permissions.*.integer' => 'Cada permissão deve ser um ID inteiro.',
            'permissions.*.exists' => 'Permissão informada não existe.',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'message' => 'Erro de validação. ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
