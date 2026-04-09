<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateCompetitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string|array<mixed>|string>
     */
    public function rules(): array
    {
        $competition = $this->route('competition');

        $startDate = $this->input('start_date', $competition->start_date);
        $endDate = $this->input('end_date', $competition->end_date);
        $maxTeams = $this->input('max_teams', $competition->max_teams);

        $enrolledTeams = $competition->teams->count();

        return [
            'name'=>['sometimes', 'string', 'max:255'],
            'description'=>['sometimes', 'string', 'min:1', 'max:500'],
            'prize_information'=>['sometimes', 'string', 'max:500', 'min:1'],
            'tools_information'=>['sometimes', 'string', 'max:500', 'min:1'],
            'total_teams'=>[
                'sometimes', 'integer', 'gte:0',
                function ($attribute, $value, $fail) use ($maxTeams) {
                    if ($value > $maxTeams) {
                        $fail('Has excedido el máximo de equipos.');
                    }
                },
            ],
            'start_date'=>[
                'sometimes', Rule::date()->format('Y-m-d'),
                function ($attribute, $value, $fail) use ($endDate) {
                    if ($value >= $endDate) {
                        $fail('No puede comenzar luego de la fecha de cierre');
                    }
                }
            ],
            'end_date'=>[
                'sometimes', Rule::date()->format('Y-m-d'),
                function ($attribute, $value, $fail) use($startDate) {
                    if ($value <= $startDate) {
                        $fail('No puede terminar antes de comenzar');
                    }
                }
            ],
            'category_id'=>['sometimes', 'exists:categories,id'],
            'max_teams'=>['sometimes', 'integer', 'gt:0', function($attribute, $value, $fail) use($enrolledTeams) {
                if ($value < $enrolledTeams) {
                    $fail('No se puede actualizar el máximo. Actualmente cuentas con más equipos inscritos');
                }
            }],
            'is_finished'=>['sometimes', 'boolean']
        ];
    }
}
