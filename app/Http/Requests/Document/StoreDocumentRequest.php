<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('upload_documents');
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'tender_id'   => ['nullable', 'exists:tenders,id'],
            'proposal_id' => ['nullable', 'exists:proposals,id'],
            'category_id' => ['nullable', 'exists:document_categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'file'        => [
                'required',
                'file',
                'max:20480', // 20 MB
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Nama dokumen wajib diisi.',
            'file.required'   => 'File wajib diunggah.',
            'file.max'        => 'Ukuran file maksimal 20 MB.',
            'file.mimes'      => 'Format file tidak didukung. Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, ZIP.',
        ];
    }
}
