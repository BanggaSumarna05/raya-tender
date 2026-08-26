@extends('layouts.app')
@section('title','Upload Dokumen')
@section('page-title','Upload Dokumen')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('documents.index') }}" class="hover:text-brand-500">Dokumen</a>
  <span class="text-gray-400">/</span><span>Upload</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
  <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" novalidate>
  @csrf
  <div class="space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Dokumen</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Nama Dokumen <span class="text-error-500">*</span></label>
          <input type="text" name="name" class="form-input @error('name') border-error-300 @enderror"
            value="{{ old('name') }}" placeholder="Nama deskriptif dokumen" />
          @error('name')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>

        {{-- File Drop Zone --}}
        <div x-data="{ dragging: false, fileName: '' }">
          <label class="form-label">File <span class="text-error-500">*</span></label>
          <label
            @dragover.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="dragging = false; fileName = $event.dataTransfer.files[0]?.name; $refs.fileInput.files = $event.dataTransfer.files"
            :class="dragging ? 'border-brand-400 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-300 bg-white dark:bg-gray-900 hover:border-brand-300'"
            class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed px-6 py-8 cursor-pointer transition-colors">
            <svg class="fill-gray-400" width="36" height="36" viewBox="0 0 48 48"><path fill-rule="evenodd" clip-rule="evenodd" d="M24 4C12.954 4 4 12.954 4 24C4 35.046 12.954 44 24 44C35.046 44 44 35.046 44 24C44 12.954 35.046 4 24 4ZM25.5 16C25.5 15.1716 24.8284 14.5 24 14.5C23.1716 14.5 22.5 15.1716 22.5 16V22.5H16C15.1716 22.5 14.5 23.1716 14.5 24C14.5 24.8284 15.1716 25.5 16 25.5H22.5V32C22.5 32.8284 23.1716 33.5 24 33.5C24.8284 33.5 25.5 32.8284 25.5 32V25.5H32C32.8284 25.5 33.5 24.8284 33.5 24C33.5 23.1716 32.8284 22.5 32 22.5H25.5V16Z" fill=""/></svg>
            <div class="text-center">
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="fileName || 'Klik atau drag & drop file di sini'"></p>
              <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, ZIP, RAR · Maks 20 MB</p>
            </div>
            <input x-ref="fileInput" type="file" name="file"
              @change="fileName = $event.target.files[0]?.name"
              class="hidden @error('file') border-error-300 @enderror"
              accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar" />
          </label>
          @error('file')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">Tender</label>
            <select name="tender_id" class="form-select">
              <option value="">— Tidak terkait —</option>
              @foreach($tenders as $t)<option value="{{ $t->id }}" @selected(old('tender_id',$selectedTenderId)==$t->id)>{{ $t->code }} — {{ Str::limit($t->title,25) }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Proposal</label>
            <select name="proposal_id" class="form-select">
              <option value="">— Tidak terkait —</option>
              @foreach($proposals as $p)<option value="{{ $p->id }}" @selected(old('proposal_id',$selectedProposalId)==$p->id)>{{ $p->code }}</option>@endforeach
            </select>
          </div>
        </div>

        <div>
          <label class="form-label">Kategori Dokumen</label>
          <select name="category_id" class="form-select">
            <option value="">— Pilih Kategori —</option>
            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id')==$c->id)>{{ $c->name }}</option>@endforeach
          </select>
        </div>

        <div>
          <label class="form-label">Keterangan</label>
          <textarea name="description" class="form-textarea" rows="2" placeholder="Keterangan dokumen...">{{ old('description') }}</textarea>
        </div>
      </div>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">
        <svg class="fill-white" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.25C10.4142 2.25 10.75 2.58579 10.75 3V8.25H16C16.4142 8.25 16.75 8.58579 16.75 9C16.75 9.41421 16.4142 9.75 16 9.75H10.75V15C10.75 15.4142 10.4142 15.75 10 15.75C9.58579 15.75 9.25 15.4142 9.25 15V9.75H4C3.58579 9.75 3.25 9.41421 3.25 9C3.25 8.58579 3.58579 9.25 4 8.25H9.25V3C9.25 2.58579 9.58579 2.25 10 2.25Z" fill=""/></svg>
        Upload Dokumen
      </button>
      <a href="{{ route('documents.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </div>
  </form>
</div>
@endsection
