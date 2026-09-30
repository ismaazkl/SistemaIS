<?php

namespace App\Http\Requests;

use App\Models\Curso;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCursoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'curso' => [
                'required',
                'string',
                'min:3',
                'max:120',
                Rule::unique(Curso::class, 'curso')
                    ->where('team_id', $this->teamId())
                    ->ignore($this->route('curso')),
            ],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'curso.min' => 'El nombre del curso debe tener al menos 3 caracteres.',
            'curso.unique' => 'Este curso ya está registrado.',
        ];
    }

    /**
     * Get the id of the team the course belongs to.
     */
    protected function teamId(): int
    {
        return (int) $this->user()->current_team_id;
    }
}
