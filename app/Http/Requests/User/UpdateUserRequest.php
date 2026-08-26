<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update_users');
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', "unique:users,email,{$userId}"],
            'username' => ['nullable', 'string', 'max:100', "unique:users,username,{$userId}", 'alpha_dash'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'status'   => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Nama wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.unique'    => 'Email sudah digunakan.',
            'username.unique' => 'Username sudah digunakan.',
            'role.required'   => 'Role wajib dipilih.',
        ];
    }
}
