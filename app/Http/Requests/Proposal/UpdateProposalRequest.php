<?php

namespace App\Http\Requests\Proposal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update_proposals');
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'pic_id'        => ['required', 'exists:users,id'],
            'backup_pic_id' => ['nullable', 'exists:users,id', 'different:pic_id'],
            'status'        => ['required', 'in:draft,internal_review,final,submitted,revision,won,lost,cancelled'],
            'bid_value'     => ['nullable', 'numeric', 'min:0'],
            'deadline'      => ['nullable', 'date'],
            'valid_until'   => ['nullable', 'date', 'after_or_equal:deadline'],
            'description'   => ['nullable', 'string', 'max:5000'],
            'notes'         => ['nullable', 'string', 'max:5000'],
            'new_version'   => ['nullable', 'boolean'],
            'version_notes' => ['nullable', 'required_if:new_version,true', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'             => 'Judul proposal wajib diisi.',
            'pic_id.required'            => 'PIC wajib dipilih.',
            'valid_until.after_or_equal' => 'Berlaku hingga harus setelah atau sama dengan deadline.',
            'version_notes.required_if'  => 'Catatan revisi wajib diisi saat membuat versi baru.',
        ];
    }
}
