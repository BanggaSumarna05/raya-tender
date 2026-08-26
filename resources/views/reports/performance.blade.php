@extends('layouts.app')
@section('title','Laporan Performa')
@section('page-title','Laporan Performa')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('reports.index') }}" class="hover:text-brand-500">Laporan</a>
  <span class="text-gray-400">/</span><span>Performa</span>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6">

  {{-- Filter --}}
  <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="mb-1.5 block text-xs font-medium text-gray-500">Dari Tanggal</label>
        <input type="date" name="start_date" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" value="{{ $filters['start_date']??'' }}" />
      </div>
      <div>
        <label class="mb-1.5 block text-xs font-medium text-gray-500">Sampai Tanggal</label>
        <input type="date" name="end_date" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" value="{{ $filters['end_date']??'' }}" />
      </div>
      <div class="flex gap-2">
        <button type="submit" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Filter</button>
        <a href="{{ route('reports.performance') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Reset</a>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-12 gap-5">

    {{-- PIC Performance --}}
    <div class="col-span-12 lg:col-span-6">
      <div class="dt-wrapper">
        <div class="dt-header border-b border-gray-100 dark:border-gray-800">
          <h3 class="dt-title">Performa Per PIC</h3>
        </div>
        <div class="max-w-full overflow-x-auto">
          @if($picData->isEmpty())
          <div class="dt-empty"><p class="text-sm text-gray-500">Belum ada data</p></div>
          @else
          <table class="dt-table">
            <thead>
              <tr>
                <th><div class="th-inner"><p>Nama PIC</p><svg class="sort-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 2L11 6H5L8 2Z" fill=""/><path d="M8 14L5 10H11L8 14Z" fill=""/></svg></div></th>
                <th><div class="th-inner justify-center"><p>Total</p><svg class="sort-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 2L11 6H5L8 2Z" fill=""/><path d="M8 14L5 10H11L8 14Z" fill=""/></svg></div></th>
                <th><div class="th-inner justify-center"><p>Menang</p></div></th>
                <th><div class="th-inner justify-center"><p>Kalah</p></div></th>
                <th><div class="th-inner justify-center"><p>Win Rate</p><svg class="sort-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 2L11 6H5L8 2Z" fill=""/><path d="M8 14L5 10H11L8 14Z" fill=""/></svg></div></th>
              </tr>
            </thead>
            <tbody>
              @foreach($picData as $row)
              <tr>
                <td><span class="td-main">{{ $row->name }}</span></td>
                <td class="text-center">
                  <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $row->total_tenders }}</span>
                </td>
                <td class="text-center">
                  <span class="rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">{{ $row->won }}</span>
                </td>
                <td class="text-center">
                  <span class="rounded-full bg-error-50 px-2.5 py-0.5 text-xs font-medium text-error-700 dark:bg-error-500/15 dark:text-error-500">{{ $row->lost }}</span>
                </td>
                <td class="text-center">
                  <span class="text-sm font-bold {{ $row->win_rate >= 50 ? 'text-success-600' : 'text-error-600' }}">{{ $row->win_rate }}%</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @endif
        </div>
      </div>
    </div>

    {{-- Client Performance --}}
    <div class="col-span-12 lg:col-span-6">
      <div class="dt-wrapper">
        <div class="dt-header border-b border-gray-100 dark:border-gray-800">
          <h3 class="dt-title">Performa Per Klien</h3>
        </div>
        <div class="max-w-full overflow-x-auto">
          @if($clientData->isEmpty())
          <div class="dt-empty"><p class="text-sm text-gray-500">Belum ada data</p></div>
          @else
          <table class="dt-table">
            <thead>
              <tr>
                <th><div class="th-inner"><p>Klien</p></div></th>
                <th><div class="th-inner justify-center"><p>Total</p></div></th>
                <th><div class="th-inner justify-center"><p>Menang</p></div></th>
                <th><div class="th-inner justify-center"><p>Win Rate</p></div></th>
              </tr>
            </thead>
            <tbody>
              @foreach($clientData as $row)
              <tr>
                <td>
                  <span class="td-main block">{{ $row->name }}</span>
                  <span class="font-mono text-xs text-gray-400">{{ $row->code }}</span>
                </td>
                <td class="text-center">
                  <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $row->total_tenders }}</span>
                </td>
                <td class="text-center">
                  <span class="rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">{{ $row->won }}</span>
                </td>
                <td class="text-center">
                  <span class="text-sm font-bold {{ $row->win_rate >= 50 ? 'text-success-600' : 'text-error-600' }}">{{ $row->win_rate }}%</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
