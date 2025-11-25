<?php

namespace App\Http\Requests\Task;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class MoveTaskRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'listId' => 'required|integer|exists:lists,id',
            'position' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'listId.required' => 'O campo listId é obrigatório.',
            'listId.integer' => 'O campo listId deve ser um número inteiro.',
            'listId.exists' => 'A lista especificada não existe.',
            'position.required' => 'O campo position é obrigatório.',
            'position.integer' => 'O campo position deve ser um número inteiro.',
            'position.min' => 'O campo position deve ser no mínimo 1.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Erro de validação' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
