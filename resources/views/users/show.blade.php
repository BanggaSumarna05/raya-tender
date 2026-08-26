@extends('layouts.app')
@section('title', $user->name)
@section('page-title','Detail User')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('settings.users.index') }}" class="hover:text-brand-500">Kelola User</a>
  <span class="text-gray-400">/</span><span>{{ $user->name }}</span>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-5">
  <div class="col-span-12 lg:col-span-4 xl:col-span-3 space-y-5">
    <div class="card p-5 text-center">
      <div class="mb-4 flex h-20 w-20 mx-auto items-center justify-center rounded-full bg-brand-500 text-2xl font-bold text-white">
        {{ strtoupper(substr($user->name, 0, 2)) }}
      </div>
      <h2 class="text-base font-semibold text-gray-800 dark:text-white">{{ $user->name }}</h2>
      <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>
      <div class="mt-3 flex flex-wrap justify-center gap-1">
        @foreach($user->roles as $role)<span class="badge badge-brand">{{ $role->name }}</span>@endforeach
        <span class="badge {{ $user->status->value === 'active' ? 'badge-success' : 'badge-error' }}">
          {{ $user->status->value === 'active' ? 'Aktif' : 'Tidak Aktif' }}
        </span>
      </div>
    </div>

    <div class="card p-5 space-y-3">
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Username</p>
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $user->username ?? '—' }}</p>
      </div>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Telepon</p>
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $user->phone ?? '—' }}</p>
      </div>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Login Terakhir</p>
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $user->last_login_at?->format('d M Y, H:i') ?? 'Belum pernah' }}</p>
      </div>
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Bergabung</p>
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $user->created_at->format('d M Y') }}</p>
      </div>
    </div>

    <div class="flex flex-col gap-2">
      @can('update_users')
      <a href="{{ route('settings.users.edit', $user) }}" class="btn btn-primary w-full">Edit Pengguna</a>
      @endcan
      <a href="{{ route('settings.users.index') }}" class="btn btn-secondary w-full">Kembali</a>
    </div>
  </div>

  <div class="col-span-12 lg:col-span-8 xl:col-span-9">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5 mb-5">
      @foreach([
        ['Total Tender', $stats['total_tenders'], 'text-gray-800'],
        ['Aktif', $stats['active_tenders'], 'text-blue-light-600'],
        ['Menang', $stats['won_tenders'], 'text-success-600'],
        ['Kalah', $stats['lost_tenders'], 'text-error-600'],
        ['Proposal', $stats['total_proposals'], 'text-warning-600'],
      ] as [$lbl, $val, $color])
      <div class="card p-4 text-center">
        <p class="text-xl font-bold {{ $color }} dark:text-white">{{ $val }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ $lbl }}</p>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
