<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_users');
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:100', 'unique:users,username', 'alpha_dash'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'status'   => ['required', 'in:active,inactive'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah digunakan.',
            'username.unique'   => 'Username sudah digunakan.',
            'role.required'     => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
        ];
    }
}
