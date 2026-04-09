<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string','min:2', 'max:100'],
            'lastname' => ['required', 'string','min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'min:3', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'dui' => ['required', 'string', 'regex:/^\d{8}-\d$/', 'unique:users,dui'],
            'role'     => ['required', 'string', 'in:administrador,organizador,lider,participante']
        ];
    }
}
