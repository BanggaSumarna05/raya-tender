@extends('layouts.app')
@section('title','Master Klien')
@section('page-title','Master Klien')
@section('breadcrumb')
  <span class="text-gray-400">/</span><span>Master Klien</span>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6">
  <div class="dt-wrapper">

    <div class="dt-header border-b border-gray-100 dark:border-gray-800">
      <h3 class="dt-title">Master Klien
        <span class="ml-2 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $clients->total() }}</span>
      </h3>
      <div class="dt-toolbar">
        <form method="GET" class="dt-show" id="pp-cli">
          @foreach(request()->except('per_page','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <span>Tampilkan</span>
          <select name="per_page" onchange="document.getElementById('pp-cli').submit()">
            @foreach([15,25,50,100] as $n)<option value="{{ $n }}" @selected(request('per_page',15)==$n)>{{ $n }}</option>@endforeach
          </select>
          <span>data</span>
        </form>
        <form method="GET" class="flex flex-wrap items-center gap-2">
          @foreach(request()->except('search','status','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <div class="dt-search w-full sm:w-auto">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/></svg>
            <input type="text" name="search" placeholder="Cari klien..." value="{{ request('search') }}" />
          </div>
          <select name="status" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="active" @selected(request('status')==='active')>Aktif</option>
            <option value="inactive" @selected(request('status')==='inactive')>Tidak Aktif</option>
          </select>
          @if(request()->hasAny(['search','status']))
          <a href="{{ route('clients.index') }}" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Reset</a>
          @endif
        </form>
        @can('create_clients')
        <a href="{{ route('clients.create') }}" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
          <svg class="fill-white" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.25C10.4142 3.25 10.75 3.58579 10.75 4V9.25H16C16.4142 9.25 16.75 9.58579 16.75 10C16.75 10.4142 16.4142 10.75 16 10.75H10.75V16C10.75 16.4142 10.4142 16.75 10 16.75C9.58579 16.75 9.25 16.4142 9.25 16V10.75H4C3.58579 10.75 3.25 10.4142 3.25 10C3.25 9.58579 3.58579 9.25 4 9.25H9.25V4C9.25 3.58579 9.58579 3.25 10 3.25Z" fill=""/></svg>
          Tambah Klien
        </a>
        @endcan
      </div>
    </div>

    <div class="max-w-full overflow-x-auto">
      @if($clients->isEmpty())
      <div class="dt-empty">
        <div class="dt-empty-icon"><svg class="fill-gray-400" width="28" height="28" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12Z" fill=""/></svg></div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada data klien</p>
        @can('create_clients')<a href="{{ route('clients.create') }}" class="mt-3 flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Tambah Klien</a>@endcan
      </div>
      @else
      <table class="dt-table min-w-[700px]">
        <thead>
          <tr>
            <th><div class="th-inner"><p>Kode</p></div></th>
            <x-sort-th column="name" label="Nama Perusahaan" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Industri</p></div></th>
            <x-sort-th column="city" label="Kota" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>PIC</p></div></th>
            <x-sort-th column="tenders_count" label="Tender" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Status</p></div></th>
            <th><div class="th-inner"><p>Aksi</p></div></th>
          </tr>
        </thead>
        <tbody>
          @foreach($clients as $client)
          <tr>
            <td><span class="font-mono text-xs text-gray-400">{{ $client->code }}</span></td>
            <td>
              <a href="{{ route('clients.show', $client) }}" class="td-main hover:text-brand-500 block">{{ $client->name }}</a>
              @if($client->company_type)<span class="td-sub">{{ $client->company_type }}</span>@endif
            </td>
            <td>{{ $client->industry ?? '—' }}</td>
            <td>{{ $client->city ?? '—' }}</td>
            <td>
              @if($client->pic_name)
              <span class="td-main block">{{ $client->pic_name }}</span>
              @if($client->pic_position)<span class="td-sub">{{ $client->pic_position }}</span>@endif
              @else <span class="text-gray-400">—</span> @endif
            </td>
            <td>
              <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $client->tenders_count }}</span>
            </td>
            <td>
              <span class="rounded-full px-2 py-0.5 text-xs font-medium
                {{ $client->status === 'active'
                  ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500'
                  : 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-500' }}">
                {{ $client->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
              </span>
            </td>
            <td>
              <div class="dt-action">
                <a href="{{ route('clients.show', $client) }}" class="dt-btn-view" title="Detail">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619Z" class="fill-current"/></svg>
                </a>
                @can('update_clients')
                <a href="{{ route('clients.edit', $client) }}" class="dt-btn-edit" title="Edit">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M14.1213 2.87868C14.9024 2.09763 16.1684 2.09763 16.9497 2.87868L17.1213 3.05025C17.9024 3.8313 17.9024 5.09763 17.1213 5.87868L8.12132 14.8787C7.89419 15.1058 7.60948 15.2656 7.2981 15.3406L3.52814 16.2394C3.17306 16.3234 2.82898 16.0657 2.87106 15.7047L3.30906 11.921C3.34697 11.5946 3.51174 11.2942 3.76777 11.0782L14.1213 2.87868Z" class="fill-current"/></svg>
                </a>
                @endcan
                @can('delete_clients')
                <button class="dt-btn-delete" title="Hapus"
                  onclick="confirmDelete('{{ route('clients.destroy', $client) }}','Hapus klien &quot;{{ addslashes($client->name) }}&quot;?')">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.75H11C11.4142 3.75 11.75 4.08579 11.75 4.5V5.25H13C13.4142 5.25 13.75 5.58579 13.75 6C13.75 6.41421 13.4142 6.75 13 6.75H7C6.58579 6.75 6.25 6.41421 6.25 6C6.25 5.58579 6.58579 5.25 7 5.25H8.25V4.5C8.25 4.08579 8.58579 3.75 9 3.75ZM6.75 8.25H13.25L12.74 16.25C12.7 16.89 12.17 17.39 11.53 17.39H8.47C7.83 17.39 7.3 16.89 7.26 16.25L6.75 8.25Z" class="fill-current"/></svg>
                </button>
                @endcan
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>

    @if($clients->isNotEmpty())
    <div class="dt-footer">
      <p class="dt-info">Menampilkan <span class="font-medium text-gray-700 dark:text-gray-200">{{ $clients->firstItem() }}</span> sampai <span class="font-medium text-gray-700 dark:text-gray-200">{{ $clients->lastItem() }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-200">{{ $clients->total() }}</span> data</p>
      <div class="dt-pagination">{{ $clients->withQueryString()->links() }}</div>
    </div>
    @endif

  </div>
</div>
@endsection
