<?php

namespace App\Http\Requests\Proposal;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_proposals');
    }

    public function rules(): array
    {
        return [
            'tender_id'   => ['required', 'exists:tenders,id'],
            'title'       => ['required', 'string', 'max:255'],
            'pic_id'      => ['required', 'exists:users,id'],
            'backup_pic_id' => ['nullable', 'exists:users,id', 'different:pic_id'],
            'status'      => ['required', 'in:draft,internal_review,final,submitted,revision,won,lost,cancelled'],
            'bid_value'   => ['nullable', 'numeric', 'min:0'],
            'deadline'    => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:deadline'],
            'description' => ['nullable', 'string', 'max:5000'],
            'notes'       => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tender_id.required' => 'Tender wajib dipilih.',
            'tender_id.exists'   => 'Tender tidak ditemukan.',
            'title.required'     => 'Judul proposal wajib diisi.',
            'pic_id.required'    => 'PIC wajib dipilih.',
        ];
    }
}
