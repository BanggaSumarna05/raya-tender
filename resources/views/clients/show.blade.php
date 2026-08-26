@extends('layouts.app')
@section('title', $client->name)
@section('page-title','Detail Klien')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('clients.index') }}" class="hover:text-brand-500">Master Klien</a>
  <span class="text-gray-400">/</span><span>{{ $client->code }}</span>
@endsection

@section('content')
<div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
  <div>
    <div class="mb-2 flex items-center gap-2">
      <span class="font-mono text-xs text-gray-400">{{ $client->code }}</span>
      <span class="badge {{ $client->status === 'active' ? 'badge-success' : 'badge-error' }}">
        {{ $client->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
      </span>
    </div>
    <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $client->name }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ collect([$client->company_type, $client->city, $client->industry])->filter()->join(' · ') }}</p>
  </div>
  <div class="flex gap-2 shrink-0">
    @can('update_clients')<a href="{{ route('clients.edit', $client) }}" class="btn btn-primary btn-sm">Edit</a>@endcan
    @can('delete_clients')<button class="btn btn-sm btn-secondary text-error-500" onclick="confirmDelete('{{ route('clients.destroy', $client) }}','Hapus klien ini?')">Hapus</button>@endcan
  </div>
</div>

{{-- Stats --}}
<div class="mb-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
  @php
    $items = [
      ['Total Tender', $stats['total_tenders'], 'text-gray-800'],
      ['Menang', $stats['won'], 'text-success-600'],
      ['Kalah', $stats['lost'], 'text-error-600'],
      ['Aktif', $stats['active'], 'text-blue-light-600'],
      ['Win Rate', $stats['win_rate'].'%', 'text-brand-600'],
      ['Total Nilai', $stats['total_value'] ? 'Rp '.number_format($stats['total_value']/1000000,0,',','.').'M' : '—', 'text-gray-600'],
    ];
  @endphp
  @foreach($items as [$label, $value, $color])
  <div class="card p-4 text-center">
    <p class="text-xl font-bold {{ $color }} dark:text-white">{{ $value }}</p>
    <p class="mt-1 text-xs text-gray-500">{{ $label }}</p>
  </div>
  @endforeach
</div>

<div class="grid grid-cols-12 gap-5">
  <div class="col-span-12 xl:col-span-8 space-y-5">

    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Kontak</h3>
      <div class="grid grid-cols-2 gap-x-8 gap-y-4">
        @foreach([
          ['Telepon', $client->phone], ['Email', $client->email],
          ['Website', $client->website], ['Kota', $client->city],
        ] as [$lbl, $val])
        @if($val)
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $lbl }}</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
            @if($lbl === 'Website')<a href="{{ $val }}" target="_blank" class="text-brand-500 hover:underline">{{ $val }}</a>
            @else {{ $val }} @endif
          </p>
        </div>
        @endif
        @endforeach
        @if($client->address)
        <div class="col-span-2">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Alamat</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $client->address }}</p>
        </div>
        @endif
        @if($client->notes)
        <div class="col-span-2">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Catatan</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $client->notes }}</p>
        </div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Riwayat Tender</h3>
        <a href="{{ route('tenders.index', ['client_id'=>$client->id]) }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
      </div>
      @if($client->tenders->isEmpty())
      <div class="py-8 text-center text-sm text-gray-400">Belum ada tender</div>
      @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead><tr class="bg-gray-50 dark:bg-gray-800/50">
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Kode</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Tender</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Deadline</th>
            <th class="px-5 py-3"></th>
          </tr></thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($client->tenders as $t)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $t->code }}</td>
              <td class="px-5 py-3"><a href="{{ route('tenders.show',$t) }}" class="font-medium text-gray-800 hover:text-brand-500 dark:text-white">{{ Str::limit($t->title,40) }}</a></td>
              <td class="px-5 py-3"><x-status-badge :status="$t->status" /></td>
              <td class="px-5 py-3 text-gray-500">{{ $t->submission_deadline?->format('d M Y') ?? '—' }}</td>
              <td class="px-5 py-3"><a href="{{ route('tenders.show',$t) }}" class="btn-icon"><svg class="fill-current" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619Z" fill=""/></svg></a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>

  </div>

  <div class="col-span-12 xl:col-span-4">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">PIC Klien</h3>
      @if($client->pic_name)
      <div class="flex items-center gap-3 mb-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
          {{ strtoupper(substr($client->pic_name, 0, 2)) }}
        </div>
        <div>
          <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $client->pic_name }}</p>
          <p class="text-xs text-gray-500">{{ $client->pic_position }}</p>
        </div>
      </div>
      <div class="space-y-2">
        @if($client->pic_phone)
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
          <svg class="fill-gray-400 shrink-0" width="15" height="15" viewBox="0 0 20 20"><path d="M2.5 4.5C2.5 3.67157 3.17157 3 4 3H6.27924C6.64204 3 6.96408 3.24247 7.06716 3.59111L7.98841 6.8533C8.09516 7.22007 7.93049 7.61255 7.59774 7.79892L6.07026 8.61827C6.02029 8.64498 5.99635 8.70354 6.01361 8.75759C6.81499 11.2665 8.73354 13.185 11.2424 13.9864C11.2965 14.0037 11.355 13.9797 11.3817 13.9297L12.2011 12.4023C12.3874 12.0695 12.7799 11.9048 13.1467 12.0116L16.4089 12.9328C16.7575 13.0359 17 13.358 17 13.7208V16C17 16.8284 16.3284 17.5 15.5 17.5H14C7.64873 17.5 2.5 12.3513 2.5 6V4.5Z" fill=""/></svg>
          {{ $client->pic_phone }}
        </div>
        @endif
        @if($client->pic_email)
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
          <svg class="fill-gray-400 shrink-0" width="15" height="15" viewBox="0 0 20 20"><path d="M2.94 6.412A1 1 0 0 1 4 5.75h12a1 1 0 0 1 1.06.662l-7 4.375L2.94 6.412ZM2 7.998V14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7.998l-7.5 4.687L2 7.998Z" fill=""/></svg>
          {{ $client->pic_email }}
        </div>
        @endif
      </div>
      @else
      <p class="text-sm text-gray-400">Belum ada data PIC.</p>
      @endif
    </div>
  </div>
</div>
@endsection
