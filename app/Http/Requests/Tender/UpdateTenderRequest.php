<?php

namespace App\Http\Requests\Tender;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update_tenders');
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'client_id'           => ['required', 'exists:clients,id'],
            'category_id'         => ['required', 'exists:tender_categories,id'],
            'pic_id'              => ['required', 'exists:users,id'],
            'backup_pic_id'       => ['nullable', 'exists:users,id', 'different:pic_id'],
            'source'              => ['nullable', 'string', 'max:100'],
            'source_reference'    => ['nullable', 'string', 'max:255'],
            'location'            => ['nullable', 'string', 'max:255'],
            'estimated_value'     => ['nullable', 'numeric', 'min:0'],
            'received_date'       => ['nullable', 'date'],
            'submission_deadline' => ['nullable', 'date'],
            'project_start_date'  => ['nullable', 'date'],
            'project_end_date'    => ['nullable', 'date', 'after_or_equal:project_start_date'],
            'priority'            => ['required', 'in:low,medium,high,urgent'],
            'status'              => ['required', 'in:draft,identified,qualification,preparation,submitted,evaluation,clarification,negotiation,won,lost,completed,cancelled'],
            'description'         => ['nullable', 'string', 'max:5000'],
            'requirements'        => ['nullable', 'string', 'max:5000'],
            'notes'               => ['nullable', 'string', 'max:5000'],
            'no_bid_reason'       => ['nullable', 'string', 'max:2000'],
            'lost_reason'         => ['required_if:status,lost', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'            => 'Nama tender wajib diisi.',
            'client_id.required'        => 'Klien wajib dipilih.',
            'category_id.required'      => 'Kategori wajib dipilih.',
            'pic_id.required'           => 'PIC wajib dipilih.',
            'backup_pic_id.different'   => 'Backup PIC tidak boleh sama dengan PIC.',
            'no_bid_reason.required_if' => 'Alasan No Bid wajib diisi jika status adalah No Bid.',
            'lost_reason.required_if'   => 'Alasan kalah wajib diisi jika status adalah Lost.',
        ];
    }
}
