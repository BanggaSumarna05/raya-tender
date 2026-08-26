@extends('layouts.app')
@section('title','Log Aktivitas')
@section('page-title','Log Aktivitas')
@section('breadcrumb')
  <span class="text-gray-400">/</span><span>Log Aktivitas</span>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6">
  <div class="dt-wrapper">

    <div class="dt-header border-b border-gray-100 dark:border-gray-800">
      <h3 class="dt-title">Log Aktivitas
        <span class="ml-2 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $logs->total() }}</span>
      </h3>
      <div class="dt-toolbar">
        <form method="GET" class="dt-show" id="pp-log">
          @foreach(request()->except('per_page','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <span>Tampilkan</span>
          <select name="per_page" onchange="document.getElementById('pp-log').submit()">
            @foreach([20,50,100] as $n)<option value="{{ $n }}" @selected(request('per_page',20)==$n)>{{ $n }}</option>@endforeach
          </select>
          <span>data</span>
        </form>
        <form method="GET" class="flex flex-wrap items-center gap-2">
          @foreach(request()->except('search','user_id','module','action','date_from','date_to','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <div class="dt-search">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/></svg>
            <input type="text" name="search" placeholder="Cari log..." value="{{ request('search') }}" />
          </div>
          <select name="user_id" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Pengguna</option>
            @foreach($users as $u)<option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>@endforeach
          </select>
          <select name="module" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Modul</option>
            @foreach($modules as $m)<option value="{{ $m }}" @selected(request('module')===$m)>{{ $m }}</option>@endforeach
          </select>
          <select name="action" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Aksi</option>
            @foreach($actions as $a)<option value="{{ $a }}" @selected(request('action')===$a)>{{ $a }}</option>@endforeach
          </select>
          <input type="date" name="date_from" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" value="{{ request('date_from') }}" onchange="this.form.submit()" />
          <input type="date" name="date_to" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" value="{{ request('date_to') }}" onchange="this.form.submit()" />
          @if(request()->hasAny(['search','user_id','module','action','date_from','date_to']))
          <a href="{{ route('settings.activity-logs.index') }}" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Reset</a>
          @endif
        </form>
      </div>
    </div>

    <div class="max-w-full overflow-x-auto">
      @if($logs->isEmpty())
      <div class="dt-empty">
        <div class="dt-empty-icon"><svg class="fill-gray-400" width="28" height="28" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 5.5C3.25 4.25736 4.25736 3.25 5.5 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V18.5C20.75 19.7426 19.7426 20.75 18.5 20.75H5.5C4.25736 20.75 3.25 19.7426 3.25 18.5V5.5ZM5.5 4.75C5.08579 4.75 4.75 5.08579 4.75 5.5V8.58325L19.25 8.58325V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H5.5Z" fill=""/></svg></div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada log aktivitas</p>
      </div>
      @else
      <table class="dt-table min-w-[700px]">
        <thead>
          <tr>
            <x-sort-th column="created_at" label="Waktu" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Pengguna</p></div></th>
            <x-sort-th column="action" label="Aksi" :current="$sortField" :direction="$sortDir" />
            <x-sort-th column="module" label="Modul" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Deskripsi</p></div></th>
            <th><div class="th-inner"><p>IP Address</p></div></th>
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
          @php
            $actionClass = match(true) {
              str_contains($log->action, 'DELETE')   => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
              str_contains($log->action, 'CREATE')   => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
              str_contains($log->action, 'UPDATE')   => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
              str_contains($log->action, 'LOGIN')    => 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400',
              str_contains($log->action, 'DOWNLOAD') => 'bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/10 dark:text-blue-light-400',
              default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
            };
          @endphp
          <tr>
            <td>
              <span class="block font-medium text-gray-700 dark:text-gray-300">{{ $log->created_at->format('d M Y') }}</span>
              <span class="block text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</span>
            </td>
            <td>
              <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                  {{ strtoupper(substr($log->user?->name ?? 'S', 0, 2)) }}
                </div>
                <span class="td-main">{{ $log->user?->name ?? 'System' }}</span>
              </div>
            </td>
            <td>
              <span class="rounded-full px-2 py-0.5 font-mono text-xs font-medium {{ $actionClass }}">{{ $log->action }}</span>
            </td>
            <td>
              <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $log->module }}</span>
            </td>
            <td><span class="line-clamp-1 max-w-xs text-gray-500 dark:text-gray-400">{{ $log->description ?? '-' }}</span></td>
            <td><span class="font-mono text-xs text-gray-400">{{ $log->ip_address ?? '-' }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>

    @if($logs->isNotEmpty())
    <div class="dt-footer">
      <p class="dt-info">Menampilkan <span class="font-medium text-gray-700 dark:text-gray-200">{{ $logs->firstItem() }}</span> sampai <span class="font-medium text-gray-700 dark:text-gray-200">{{ $logs->lastItem() }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-200">{{ $logs->total() }}</span> log</p>
      <div class="dt-pagination">{{ $logs->withQueryString()->links() }}</div>
    </div>
    @endif

  </div>
</div>
@endsection
