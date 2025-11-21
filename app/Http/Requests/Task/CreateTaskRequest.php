<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'list_id' => 'required|exists:lists,id',
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
            'position' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'list_id.required' => 'Uma lista é obrigatória para criar uma tarefa.',
            'list_id.exists' => 'A lista informada não existe.',
            'client_id.required' => 'O cliente é obrigatório.',
            'client_id.exists' => 'O cliente informado não existe.',
            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser um texto.',
            'title.max' => 'O título deve ter no máximo 255 caracteres.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
            'end_date.date' => 'A data de término deve ser uma data válida.',
            'end_date.after' => 'A data de término deve ser posterior à data de início.',
            'description.string' => 'A descrição deve ser um texto.',
            'position.integer' => 'A posição deve ser um número inteiro.',
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
