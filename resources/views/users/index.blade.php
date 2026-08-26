@extends('layouts.app')
@section('title','Kelola User')
@section('page-title','Kelola User')
@section('breadcrumb')
  <span class="text-gray-400">/</span><span>Kelola User</span>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6">
  <div class="dt-wrapper">

    <div class="dt-header border-b border-gray-100 dark:border-gray-800">
      <h3 class="dt-title">Kelola User
        <span class="ml-2 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $users->total() }}</span>
      </h3>
      <div class="dt-toolbar">
        <form method="GET" class="dt-show" id="pp-usr">
          @foreach(request()->except('per_page','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <span>Tampilkan</span>
          <select name="per_page" onchange="document.getElementById('pp-usr').submit()">
            @foreach([15,25,50,100] as $n)<option value="{{ $n }}" @selected(request('per_page',15)==$n)>{{ $n }}</option>@endforeach
          </select>
          <span>data</span>
        </form>
        <form method="GET" class="flex flex-wrap items-center gap-2">
          @foreach(request()->except('search','role','status','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <div class="dt-search w-full sm:w-auto">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/></svg>
            <input type="text" name="search" placeholder="Cari pengguna..." value="{{ request('search') }}" />
          </div>
          <select name="role" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            @foreach($roles as $r)<option value="{{ $r->name }}" @selected(request('role')===$r->name)>{{ $r->name }}</option>@endforeach
          </select>
          <select name="status" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="active" @selected(request('status')==='active')>Aktif</option>
            <option value="inactive" @selected(request('status')==='inactive')>Tidak Aktif</option>
          </select>
          @if(request()->hasAny(['search','role','status']))
          <a href="{{ route('settings.users.index') }}" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Reset</a>
          @endif
        </form>
        @can('create_users')
        <a href="{{ route('settings.users.create') }}" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
          <svg class="fill-white" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.25C10.4142 3.25 10.75 3.58579 10.75 4V9.25H16C16.4142 9.25 16.75 9.58579 16.75 10C16.75 10.4142 16.4142 10.75 16 10.75H10.75V16C10.75 16.4142 10.4142 16.75 10 16.75C9.58579 16.75 9.25 16.4142 9.25 16V10.75H4C3.58579 10.75 3.25 10.4142 3.25 10C3.25 9.58579 3.58579 9.25 4 9.25H9.25V4C9.25 3.58579 9.58579 3.25 10 3.25Z" fill=""/></svg>
          Tambah User
        </a>
        @endcan
      </div>
    </div>

    <div class="max-w-full overflow-x-auto">
      @if($users->isEmpty())
      <div class="dt-empty">
        <div class="dt-empty-icon"><svg class="fill-gray-400" width="28" height="28" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12Z" fill=""/></svg></div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada data pengguna</p>
      </div>
      @else
      <table class="dt-table min-w-[650px]">
        <thead>
          <tr>
            <x-sort-th column="name" label="Pengguna" :current="$sortField" :direction="$sortDir" />
            <x-sort-th column="email" label="Email" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Role</p></div></th>
            <th><div class="th-inner"><p>Status</p></div></th>
            <x-sort-th column="last_login_at" label="Login Terakhir" :current="$sortField" :direction="$sortDir" />
            <x-sort-th column="created_at" label="Bergabung" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Aksi</p></div></th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <td>
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                  {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                  <span class="td-main block">{{ $user->name }}</span>
                  @if($user->username)<span class="td-sub">@{{ $user->username }}</span>@endif
                </div>
              </div>
            </td>
            <td>{{ $user->email }}</td>
            <td>
              @foreach($user->roles as $role)
              <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $role->name }}</span>
              @endforeach
            </td>
            <td>
              <span class="rounded-full px-2 py-0.5 text-xs font-medium
                {{ $user->status->value === 'active'
                  ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500'
                  : 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-500' }}">
                {{ $user->status->value === 'active' ? 'Aktif' : 'Tidak Aktif' }}
              </span>
            </td>
            <td>{{ $user->last_login_at?->format('d M Y, H:i') ?? 'Belum pernah' }}</td>
            <td>{{ $user->created_at->format('d M Y') }}</td>
            <td>
              <div class="dt-action">
                <a href="{{ route('settings.users.show', $user) }}" class="dt-btn-view" title="Detail">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619Z" class="fill-current"/></svg>
                </a>
                @can('update_users')
                <a href="{{ route('settings.users.edit', $user) }}" class="dt-btn-edit" title="Edit">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M14.1213 2.87868C14.9024 2.09763 16.1684 2.09763 16.9497 2.87868L17.1213 3.05025C17.9024 3.8313 17.9024 5.09763 17.1213 5.87868L8.12132 14.8787C7.89419 15.1058 7.60948 15.2656 7.2981 15.3406L3.52814 16.2394C3.17306 16.3234 2.82898 16.0657 2.87106 15.7047L3.30906 11.921C3.34697 11.5946 3.51174 11.2942 3.76777 11.0782L14.1213 2.87868Z" class="fill-current"/></svg>
                </a>
                {{-- Toggle Status --}}
                <form action="{{ route('settings.users.toggle-status', $user) }}" method="POST">
                  @csrf
                  <button type="submit"
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700
                    {{ $user->status->value === 'active' ? 'text-warning-500 hover:text-warning-600' : 'text-success-500 hover:text-success-600' }}"
                    title="{{ $user->status->value === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                    @if($user->status->value === 'active')
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3C6.134 3 3 6.134 3 10C3 13.866 6.134 17 10 17C13.866 17 17 13.866 17 10C17 6.134 13.866 3 10 3ZM10 5C10.552 5 11 5.448 11 6V11C11 11.552 10.552 12 10 12C9.448 12 9 11.552 9 11V6C9 5.448 9.448 5 10 5ZM10 13C10.552 13 11 13.448 11 14C11 14.552 10.552 15 10 15C9.448 15 9 14.552 9 14C9 13.448 9.448 13 10 13Z" class="fill-current"/></svg>
                    @else
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3C6.134 3 3 6.134 3 10C3 13.866 6.134 17 10 17C13.866 17 17 13.866 17 10C17 6.134 13.866 3 10 3ZM13.707 8.207C14.098 7.816 14.098 7.184 13.707 6.793C13.317 6.402 12.684 6.402 12.293 6.793L9 10.086L7.707 8.793C7.317 8.402 6.684 8.402 6.293 8.793C5.902 9.184 5.902 9.817 6.293 10.207L8.293 12.207C8.684 12.598 9.317 12.598 9.707 12.207L13.707 8.207Z" class="fill-current"/></svg>
                    @endif
                  </button>
                </form>
                @endcan
                @can('delete_users')
                @if(auth()->id() !== $user->id && !$user->hasRole('Super Admin'))
                <button class="dt-btn-delete" title="Hapus"
                  onclick="confirmDelete('{{ route('settings.users.destroy', $user) }}','Hapus pengguna &quot;{{ addslashes($user->name) }}&quot;?')">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.75H11C11.4142 3.75 11.75 4.08579 11.75 4.5V5.25H13C13.4142 5.25 13.75 5.58579 13.75 6C13.75 6.41421 13.4142 6.75 13 6.75H7C6.58579 6.75 6.25 6.41421 6.25 6C6.25 5.58579 6.58579 5.25 7 5.25H8.25V4.5C8.25 4.08579 8.58579 3.75 9 3.75ZM6.75 8.25H13.25L12.74 16.25C12.7 16.89 12.17 17.39 11.53 17.39H8.47C7.83 17.39 7.3 16.89 7.26 16.25L6.75 8.25Z" class="fill-current"/></svg>
                </button>
                @endif
                @endcan
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>

    @if($users->isNotEmpty())
    <div class="dt-footer">
      <p class="dt-info">Menampilkan <span class="font-medium text-gray-700 dark:text-gray-200">{{ $users->firstItem() }}</span> sampai <span class="font-medium text-gray-700 dark:text-gray-200">{{ $users->lastItem() }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-200">{{ $users->total() }}</span> data</p>
      <div class="dt-pagination">{{ $users->withQueryString()->links() }}</div>
    </div>
    @endif

  </div>
</div>
@endsection
