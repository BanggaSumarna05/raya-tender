@extends('layouts.app')
@section('title','Laporan Tender')
@section('page-title','Laporan Tender')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('reports.index') }}" class="hover:text-brand-500">Laporan</a>
  <span class="text-gray-400">/</span><span>Tender</span>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6">

  {{-- Filter --}}
  <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 sm:px-6 border-b border-gray-100 dark:border-gray-800">
      <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Filter Laporan</h3>
    </div>
    <div class="p-5 sm:p-6">
      <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
          <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Dari Tanggal</label>
          <input type="date" name="start_date"
            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
            value="{{ $filters['start_date'] ?? '' }}" />
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Sampai Tanggal</label>
          <input type="date" name="end_date"
            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
            value="{{ $filters['end_date'] ?? '' }}" />
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Status</label>
          <select name="status" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            <option value="">Semua Status</option>
            @foreach(\App\Enums\TenderStatus::cases() as $s)
            <option value="{{ $s->value }}" @selected(($filters['status'] ?? '') === $s->value)>{{ $s->label() }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Klien</label>
          <select name="client_id" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            <option value="">Semua Klien</option>
            @foreach($clients as $c)
            <option value="{{ $c->id }}" @selected(($filters['client_id'] ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">PIC</label>
          <select name="pic_id" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            <option value="">Semua PIC</option>
            @foreach($pics as $p)
            <option value="{{ $p->id }}" @selected(($filters['pic_id'] ?? '') == $p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="fill-white" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/></svg>
            Filter
          </button>
          <a href="{{ route('reports.tenders') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Reset</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Summary Cards — no financial values --}}
  <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
    @foreach([
      ['Total',     $data['total'],     'text-gray-800 dark:text-white/90'],
      ['Aktif',     $data['active'],    'text-blue-light-600'],
      ['Menang',    $data['won'],       'text-success-600'],
      ['Kalah',     $data['lost'],      'text-error-600'],
      ['Dibatalkan',$data['cancelled'], 'text-gray-500'],
      ['Win Rate',  $data['win_rate'].'%', 'text-brand-600'],
    ] as [$lbl, $val, $color])
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
      <span class="text-xs text-gray-500 dark:text-gray-400">{{ $lbl }}</span>
      <h4 class="mt-2 text-xl font-bold {{ $color }}">{{ $val }}</h4>
    </div>
    @endforeach
  </div>

  {{-- Export toolbar (no financial totals) --}}
  @can('export_reports')
  <div class="flex justify-end items-center gap-2" x-data="{ openExcel: false, openPdf: false }">

    {{-- Excel dropdown --}}
    <div class="relative">
      <button @click="openExcel = !openExcel; openPdf = false"
        class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
        <svg class="fill-success-600" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.25C10.4142 2.25 10.75 2.58579 10.75 3V11.1893L12.9697 8.96967C13.2626 8.67678 13.7374 8.67678 14.0303 8.96967C14.3232 9.26256 14.3232 9.73744 14.0303 10.0303L10.5303 13.5303C10.2374 13.8232 9.76256 13.8232 9.46967 13.5303L5.96967 10.0303C5.67678 9.73744 5.67678 9.26256 5.96967 8.96967C6.26256 8.67678 6.73744 8.67678 7.03033 8.96967L9.25 11.1893V3C9.25 2.58579 9.58579 2.25 10 2.25ZM3.25 15C3.25 14.5858 3.58579 14.25 4 14.25H16C16.4142 14.25 16.75 14.5858 16.75 15C16.75 15.4142 16.4142 15.75 16 15.75H4C3.58579 15.75 3.25 15.4142 3.25 15Z" fill=""/></svg>
        Excel
        <svg class="fill-gray-400 transition-transform" :class="openExcel ? 'rotate-180' : ''" width="12" height="12" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.22 6.97a.75.75 0 0 1 1.06 0L10 11.69l4.72-4.72a.75.75 0 1 1 1.06 1.06l-5.25 5.25a.75.75 0 0 1-1.06 0L4.22 8.03a.75.75 0 0 1 0-1.06Z" fill=""/></svg>
      </button>
      <div x-show="openExcel" @click.outside="openExcel = false" x-transition
        class="absolute right-0 z-20 mt-1 w-52 rounded-xl border border-gray-100 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Pilih Tipe</div>
        <a href="{{ route('reports.export.tender-excel', request()->all()) }}"
          class="flex items-start gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700">
          <svg class="mt-0.5 shrink-0 fill-success-500" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4Zm2 2v8h10V6H5Z" fill=""/></svg>
          <div>
            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Laporan Ringkasan</div>
            <div class="text-xs text-gray-400">Format profesional + statistik</div>
          </div>
        </a>
        <a href="{{ route('reports.export.tender-raw', request()->all()) }}"
          class="flex items-start gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700">
          <svg class="mt-0.5 shrink-0 fill-gray-400" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4Zm2 2v8h10V6H5Z" fill=""/></svg>
          <div>
            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Data Mentah (Raw)</div>
            <div class="text-xs text-gray-400">Semua kolom, siap pivot/analisis</div>
          </div>
        </a>
      </div>
    </div>

    {{-- PDF dropdown --}}
    <div class="relative">
      <button @click="openPdf = !openPdf; openExcel = false"
        class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
        <svg class="fill-error-500" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.414A2 2 0 0 0 17.414 6L14 2.586A2 2 0 0 0 12.586 2H4Zm7 1.5V7h3.5L11 3.5ZM5 9.75A.75.75 0 0 1 5.75 9h8.5a.75.75 0 0 1 0 1.5h-8.5A.75.75 0 0 1 5 9.75Zm.75 2.25a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5h-5.5Z" fill=""/></svg>
        PDF
        <svg class="fill-gray-400 transition-transform" :class="openPdf ? 'rotate-180' : ''" width="12" height="12" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.22 6.97a.75.75 0 0 1 1.06 0L10 11.69l4.72-4.72a.75.75 0 1 1 1.06 1.06l-5.25 5.25a.75.75 0 0 1-1.06 0L4.22 8.03a.75.75 0 0 1 0-1.06Z" fill=""/></svg>
      </button>
      <div x-show="openPdf" @click.outside="openPdf = false" x-transition
        class="absolute right-0 z-20 mt-1 w-52 rounded-xl border border-gray-100 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Pilih Tipe</div>
        <a href="{{ route('reports.export.tender-pdf', request()->all()) }}"
          class="flex items-start gap-3 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700">
          <svg class="mt-0.5 shrink-0 fill-error-500" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4Zm2 2v8h10V6H5Z" fill=""/></svg>
          <div>
            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Laporan Tender</div>
            <div class="text-xs text-gray-400">Format profesional dengan chart</div>
          </div>
        </a>
      </div>
    </div>

  </div>
  @endcan

  {{-- Data Table — no estimated_value column --}}
  <div class="dt-wrapper">
    <div class="dt-header border-b border-gray-100 dark:border-gray-800">
      <h3 class="dt-title">
        Daftar Tender
        <span class="ml-2 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
          {{ $data['tenders']->count() }}
        </span>
      </h3>
    </div>
    <div class="max-w-full overflow-x-auto">
      @if($data['tenders']->isEmpty())
      <div class="dt-empty">
        <p class="text-sm text-gray-500">Tidak ada data untuk filter ini.</p>
      </div>
      @else
      <table class="dt-table">
        <thead>
          <tr>
            <th><div class="th-inner"><p>Kode</p></div></th>
            <th><div class="th-inner"><p>Nama Tender</p></div></th>
            <th><div class="th-inner"><p>Klien</p></div></th>
            <th><div class="th-inner"><p>Kategori</p></div></th>
            <th><div class="th-inner"><p>PIC</p></div></th>
            <th><div class="th-inner"><p>Status</p></div></th>
            <th><div class="th-inner"><p>Prioritas</p></div></th>
            <th><div class="th-inner"><p>Deadline</p></div></th>
          </tr>
        </thead>
        <tbody>
          @foreach($data['tenders'] as $t)
          <tr>
            <td><span class="font-mono text-xs text-gray-400">{{ $t->code }}</span></td>
            <td>
              <a href="{{ route('tenders.show', $t) }}" class="td-main hover:text-brand-500">
                {{ Str::limit($t->title, 38) }}
              </a>
            </td>
            <td>{{ $t->client?->name ?? '—' }}</td>
            <td>{{ $t->category?->name ?? '—' }}</td>
            <td>{{ $t->pic?->name ?? '—' }}</td>
            <td><x-status-badge :status="$t->status" /></td>
            <td><x-status-badge :status="$t->priority" /></td>
            <td>{{ $t->submission_deadline?->format('d M Y') ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
  </div>

</div>
@endsection
