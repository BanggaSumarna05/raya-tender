@extends('layouts.app')
@section('title','Edit Klien')
@section('page-title','Edit Klien')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('clients.index') }}" class="hover:text-brand-500">Master Klien</a>
  <span class="text-gray-400">/</span><a href="{{ route('clients.show', $client) }}" class="hover:text-brand-500">{{ $client->name }}</a>
  <span class="text-gray-400">/</span><span>Edit</span>
@endsection

@section('content')
<form action="{{ route('clients.update', $client) }}" method="POST" novalidate>
@csrf @method('PUT')
<div class="grid grid-cols-12 gap-5">
  <div class="col-span-12 xl:col-span-8 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Perusahaan</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Nama Perusahaan <span class="text-error-500">*</span></label>
          <input type="text" name="name" class="form-input @error('name') border-error-300 @enderror" value="{{ old('name', $client->name) }}" />
          @error('name')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Jenis Perusahaan</label><input type="text" name="company_type" class="form-input" value="{{ old('company_type', $client->company_type) }}" /></div>
          <div><label class="form-label">Industri</label><input type="text" name="industry" class="form-input" value="{{ old('industry', $client->industry) }}" /></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Kota</label><input type="text" name="city" class="form-input" value="{{ old('city', $client->city) }}" /></div>
          <div><label class="form-label">Telepon</label><input type="text" name="phone" class="form-input" value="{{ old('phone', $client->phone) }}" /></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Email</label><input type="email" name="email" class="form-input" value="{{ old('email', $client->email) }}" /></div>
          <div><label class="form-label">Website</label><input type="url" name="website" class="form-input" value="{{ old('website', $client->website) }}" /></div>
        </div>
        <div><label class="form-label">Alamat</label><textarea name="address" class="form-textarea" rows="2">{{ old('address', $client->address) }}</textarea></div>
        <div><label class="form-label">Catatan</label><textarea name="notes" class="form-textarea" rows="2">{{ old('notes', $client->notes) }}</textarea></div>
      </div>
    </div>
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi PIC</h3>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="form-label">Nama PIC</label><input type="text" name="pic_name" class="form-input" value="{{ old('pic_name', $client->pic_name) }}" /></div>
        <div><label class="form-label">Jabatan</label><input type="text" name="pic_position" class="form-input" value="{{ old('pic_position', $client->pic_position) }}" /></div>
        <div><label class="form-label">Telepon PIC</label><input type="text" name="pic_phone" class="form-input" value="{{ old('pic_phone', $client->pic_phone) }}" /></div>
        <div><label class="form-label">Email PIC</label><input type="email" name="pic_email" class="form-input" value="{{ old('pic_email', $client->pic_email) }}" /></div>
      </div>
    </div>
  </div>
  <div class="col-span-12 xl:col-span-4 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Status</h3>
      <select name="status" class="form-select">
        <option value="active" @selected(old('status', $client->status)==='active')>Aktif</option>
        <option value="inactive" @selected(old('status', $client->status)==='inactive')>Tidak Aktif</option>
      </select>
    </div>
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary flex-1">Simpan</button>
      <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Batal</a>
    </div>
  </div>
</div>
</form>
@endsection
