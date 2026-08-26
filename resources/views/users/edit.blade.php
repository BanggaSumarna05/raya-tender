@extends('layouts.app')
@section('title','Edit User')
@section('page-title','Edit User')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('settings.users.index') }}" class="hover:text-brand-500">Kelola User</a>
  <span class="text-gray-400">/</span><span>{{ $user->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
<form action="{{ route('settings.users.update', $user) }}" method="POST" novalidate>
@csrf @method('PUT')
<div class="space-y-5">
  <div class="card p-5">
    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Edit Pengguna</h3>
    <div class="grid grid-cols-2 gap-4">
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Nama Lengkap <span class="text-error-500">*</span></label>
        <input type="text" name="name" class="form-input @error('name') border-error-300 @enderror" value="{{ old('name',$user->name) }}" />
        @error('name')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-input @error('username') border-error-300 @enderror" value="{{ old('username',$user->username) }}" />
        @error('username')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Email <span class="text-error-500">*</span></label>
        <input type="email" name="email" class="form-input @error('email') border-error-300 @enderror" value="{{ old('email',$user->email) }}" />
        @error('email')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Telepon</label>
        <input type="text" name="phone" class="form-input" value="{{ old('phone',$user->phone) }}" />
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Role <span class="text-error-500">*</span></label>
        <select name="role" class="form-select @error('role') border-error-300 @enderror">
          @foreach($roles as $r)<option value="{{ $r->name }}" @selected(old('role',$user->getRoleNames()->first())===$r->name)>{{ $r->name }}</option>@endforeach
        </select>
        @error('role')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="active" @selected(old('status',$user->status->value)==='active')>Aktif</option>
          <option value="inactive" @selected(old('status',$user->status->value)==='inactive')>Tidak Aktif</option>
        </select>
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Password Baru</label>
        <input type="password" name="password" class="form-input @error('password') border-error-300 @enderror" autocomplete="new-password" />
        <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ingin mengubah password</p>
        @error('password')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
      <div class="col-span-2 sm:col-span-1">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password" />
      </div>
    </div>
  </div>
  <div class="flex gap-3">
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">Batal</a>
  </div>
</div>
</form>
</div>
@endsection
