<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        // Implemente a lógica de permissão aqui se necessário
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:500',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'O nome da role deve ser um texto.',
            'name.max' => 'O nome da role não pode exceder 255 caracteres.',
            'description.string' => 'A descrição da role deve ser um texto.',
            'description.max' => 'A descrição da role não pode exceder 1000 caracteres.',
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
