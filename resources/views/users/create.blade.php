@extends('layouts.app')
@section('title','Tambah User')
@section('page-title','Tambah User')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('settings.users.index') }}" class="hover:text-brand-500">Kelola User</a>
  <span class="text-gray-400">/</span><span>Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
<form action="{{ route('settings.users.store') }}" method="POST" novalidate>
@csrf
<div class="space-y-5">
  <div class="card p-5">
    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Pengguna</h3>
    <div class="grid grid-cols-2 gap-4">
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Nama Lengkap <span class="text-error-500">*</span></label>
        <input type="text" name="name" class="form-input @error('name') border-error-300 @enderror" value="{{ old('name') }}" />
        @error('name')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-input @error('username') border-error-300 @enderror" value="{{ old('username') }}" placeholder="opsional" />
        @error('username')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Email <span class="text-error-500">*</span></label>
        <input type="email" name="email" class="form-input @error('email') border-error-300 @enderror" value="{{ old('email') }}" />
        @error('email')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Telepon</label>
        <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" />
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Role <span class="text-error-500">*</span></label>
        <select name="role" class="form-select @error('role') border-error-300 @enderror">
          <option value="">— Pilih Role —</option>
          @foreach($roles as $r)<option value="{{ $r->name }}" @selected(old('role')===$r->name)>{{ $r->name }}</option>@endforeach
        </select>
        @error('role')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="active" @selected(old('status','active')==='active')>Aktif</option>
          <option value="inactive" @selected(old('status')==='inactive')>Tidak Aktif</option>
        </select>
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Password <span class="text-error-500">*</span></label>
        <input type="password" name="password" class="form-input @error('password') border-error-300 @enderror" autocomplete="new-password" placeholder="Min. 8 karakter" />
        <p class="mt-1 text-xs text-gray-400">Huruf besar + kecil + angka</p>
        @error('password')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Konfirmasi Password <span class="text-error-500">*</span></label>
        <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password" />
      </div>
    </div>
  </div>
  <div class="flex gap-3">
    <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
    <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">Batal</a>
  </div>
</div>
</form>
</div>
@endsection
