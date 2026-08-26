@extends('layouts.app')
@section('title','Ganti Password')
@section('page-title','Ganti Password')
@section('breadcrumb')
  <span class="text-gray-400">/</span><span>Ganti Password</span>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
  <div class="card p-6">
    <h2 class="mb-1 text-base font-semibold text-gray-800 dark:text-white">Ubah Password</h2>
    <p class="mb-6 text-sm text-gray-500">Password baru minimal 8 karakter, huruf besar+kecil+angka.</p>

    <form action="{{ route('password.change.update') }}" method="POST" novalidate class="space-y-5">
      @csrf

      <div>
        <label class="form-label">Password Saat Ini <span class="text-error-500">*</span></label>
        <input type="password" name="current_password"
          class="form-input @error('current_password') border-error-300 @enderror"
          placeholder="Masukkan password saat ini" />
        @error('current_password')
        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="form-label">Password Baru <span class="text-error-500">*</span></label>
        <input type="password" name="password"
          class="form-input @error('password') border-error-300 @enderror"
          placeholder="Min. 8 karakter" />
        @error('password')
        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="form-label">Konfirmasi Password <span class="text-error-500">*</span></label>
        <input type="password" name="password_confirmation"
          class="form-input"
          placeholder="Ulangi password baru" />
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan Password</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
