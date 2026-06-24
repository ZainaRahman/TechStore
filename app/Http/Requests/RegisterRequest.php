<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone'     => ['nullable', 'string', 'max:15'],
            'address'   => ['nullable', 'string', 'max:255'],
            'city'      => ['nullable', 'string', 'max:50'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}