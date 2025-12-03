<?php

namespace App\Http\Requests\Task\Elements;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCheckListRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'title.string' => 'O título da checklist deve ser uma string.',
            'items.array' => 'Os itens da checklist devem ser um array.',
            'items.*.description.required' => 'A descrição do item da checklist é obrigatória.',
            'items.*.description.string' => 'A descrição do item da checklist deve ser uma string.',
        ];
    }

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
