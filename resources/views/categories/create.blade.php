@extends('layouts.app')
@section('title','Tambah Kategori')
@section('page-title','Tambah Kategori')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('settings.categories.index') }}" class="hover:text-brand-500">Kategori</a>
  <span class="text-gray-400">/</span><span>Tambah</span>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
<form action="{{ route('settings.categories.store') }}" method="POST" novalidate>
@csrf
<div class="space-y-5">
  <div class="card p-5">
    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Kategori Baru</h3>
    <div class="space-y-4">
      <div>
        <label class="form-label">Kode <span class="text-error-500">*</span></label>
        <input type="text" name="code" class="form-input @error('code') border-error-300 @enderror"
          value="{{ old('code') }}" placeholder="e.g. KON, INF" style="text-transform:uppercase" />
        <p class="mt-1 text-xs text-gray-400">Huruf, angka, tanda hubung. Harus unik.</p>
        @error('code')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="form-label">Nama Kategori <span class="text-error-500">*</span></label>
        <input type="text" name="name" class="form-input @error('name') border-error-300 @enderror" value="{{ old('name') }}" placeholder="Nama kategori" />
        @error('name')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-textarea" rows="3">{{ old('description') }}</textarea>
      </div>
      <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="active">Aktif</option>
          <option value="inactive">Tidak Aktif</option>
        </select>
      </div>
    </div>
  </div>
  <div class="flex gap-3">
    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
    <a href="{{ route('settings.categories.index') }}" class="btn btn-secondary">Batal</a>
  </div>
</div>
</form>
</div>
@endsection
