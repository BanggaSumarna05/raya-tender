<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_clients');
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:200'],
            'company_type' => ['nullable', 'string', 'max:100'],
            'address'      => ['nullable', 'string', 'max:1000'],
            'city'         => ['nullable', 'string', 'max:100'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'email'        => ['nullable', 'email', 'max:150'],
            'website'      => ['nullable', 'url', 'max:255'],
            'industry'     => ['nullable', 'string', 'max:100'],
            'pic_name'     => ['nullable', 'string', 'max:150'],
            'pic_position' => ['nullable', 'string', 'max:100'],
            'pic_phone'    => ['nullable', 'string', 'max:30'],
            'pic_email'    => ['nullable', 'email', 'max:150'],
            'status'       => ['required', 'in:active,inactive'],
            'notes'        => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama perusahaan wajib diisi.',
            'email.email'   => 'Format email tidak valid.',
            'website.url'   => 'Format website tidak valid.',
        ];
    }
}
