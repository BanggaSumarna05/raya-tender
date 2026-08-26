@extends('layouts.app')
@section('title','Laporan')
@section('page-title','Laporan')
@section('breadcrumb')
  <span class="text-gray-400">/</span><span>Laporan</span>
@endsection

@section('content')
<div class="mb-6">
  <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Laporan</h2>
  <p class="text-sm text-gray-500">Analisis performa tender dan proposal marketing</p>
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

  <a href="{{ route('reports.tenders') }}"
    class="card group p-6 hover:shadow-theme-md transition-shadow cursor-pointer">
    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10 group-hover:bg-brand-100 transition-colors">
      <svg class="fill-brand-500" width="22" height="22" viewBox="0 0 24 24" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.50391 4.25H17.7504C18.1646 4.25 18.5004 4.58579 18.5004 5V16.75H9.25391C8.83969 16.75 8.50391 16.4142 8.50391 16V4.25ZM5.498 7.25H7.004V16C7.004 17.243 8.011 18.25 9.254 18.25H15.75V19C15.75 20.243 14.743 21.25 13.5 21.25H5.748C4.505 21.25 3.498 20.243 3.498 19V8.5C3.498 7.257 4.505 6.25 5.748 6.25L5.498 7.25Z" fill=""/>
      </svg>
    </div>
    <h3 class="mb-2 text-base font-semibold text-gray-800 dark:text-white">Laporan Tender</h3>
    <p class="text-sm text-gray-500">Ringkasan tender berdasarkan status, kategori, nilai, dan periode.</p>
    <div class="mt-4 flex items-center gap-1 text-sm font-medium text-brand-500">
      Lihat Laporan
      <svg class="fill-brand-500" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.22 4.47C8.51 4.18 8.99 4.18 9.28 4.47L14.28 9.47C14.57 9.76 14.57 10.24 14.28 10.53L9.28 15.53C8.99 15.82 8.51 15.82 8.22 15.53C7.93 15.24 7.93 14.76 8.22 14.47L12.69 10L8.22 5.53C7.93 5.24 7.93 4.76 8.22 4.47Z" fill=""/></svg>
    </div>
  </a>

  <a href="{{ route('reports.proposals') }}"
    class="card group p-6 hover:shadow-theme-md transition-shadow cursor-pointer">
    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 group-hover:bg-success-100 transition-colors">
      <svg class="fill-success-500" width="22" height="22" viewBox="0 0 24 24" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25H18.5C19.74 3.25 20.75 4.26 20.75 5.5V18.5C20.75 19.74 19.74 20.75 18.5 20.75H5.5C4.26 20.75 3.25 19.74 3.25 18.5V5.5C3.25 4.26 4.26 3.25 5.5 3.25ZM7 9.71H17V8.21H7V9.71ZM7 14.29H14V12.79H7V14.29Z" fill=""/>
      </svg>
    </div>
    <h3 class="mb-2 text-base font-semibold text-gray-800 dark:text-white">Laporan Proposal</h3>
    <p class="text-sm text-gray-500">Rekap proposal berdasarkan status, PIC, dan nilai penawaran.</p>
    <div class="mt-4 flex items-center gap-1 text-sm font-medium text-success-600">
      Lihat Laporan
      <svg class="fill-success-600" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.22 4.47C8.51 4.18 8.99 4.18 9.28 4.47L14.28 9.47C14.57 9.76 14.57 10.24 14.28 10.53L9.28 15.53C8.99 15.82 8.51 15.82 8.22 15.53C7.93 15.24 7.93 14.76 8.22 14.47L12.69 10L8.22 5.53C7.93 5.24 7.93 4.76 8.22 4.47Z" fill=""/></svg>
    </div>
  </a>

  <a href="{{ route('reports.performance') }}"
    class="card group p-6 hover:shadow-theme-md transition-shadow cursor-pointer">
    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-warning-50 dark:bg-warning-500/10 group-hover:bg-warning-100 transition-colors">
      <svg class="fill-warning-500" width="22" height="22" viewBox="0 0 24 24" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C11.5858 2 11.25 2.33579 11.25 2.75V12C11.25 12.4142 11.5858 12.75 12 12.75H21.25C21.6642 12.75 22 12.4142 22 12C22 6.47715 17.5228 2 12 2ZM2 12C2 7.25083 5.31065 3.27489 9.75 2.25415V13.5H21.7459C20.7251 18.6894 16.7492 22 12 22C6.47715 22 2 17.5229 2 12Z" fill=""/>
      </svg>
    </div>
    <h3 class="mb-2 text-base font-semibold text-gray-800 dark:text-white">Laporan Performa</h3>
    <p class="text-sm text-gray-500">Win rate, performa per PIC, dan performa per klien.</p>
    <div class="mt-4 flex items-center gap-1 text-sm font-medium text-warning-600">
      Lihat Laporan
      <svg class="fill-warning-600" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.22 4.47C8.51 4.18 8.99 4.18 9.28 4.47L14.28 9.47C14.57 9.76 14.57 10.24 14.28 10.53L9.28 15.53C8.99 15.82 8.51 15.82 8.22 15.53C7.93 15.24 7.93 14.76 8.22 14.47L12.69 10L8.22 5.53C7.93 5.24 7.93 4.76 8.22 4.47Z" fill=""/></svg>
    </div>
  </a>

  @can('export_reports')
  <a href="{{ route('reports.export.summary-pdf', request()->query()) }}"
    class="card group p-6 hover:shadow-theme-md transition-shadow cursor-pointer border border-dashed border-gray-200 dark:border-gray-700">
    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-error-50 dark:bg-error-500/10 group-hover:bg-error-100 transition-colors">
      <svg class="fill-error-500" width="22" height="22" viewBox="0 0 24 24" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.75 2C3.7835 2 3 2.7835 3 3.75V20.25C3 21.2165 3.7835 22 4.75 22H19.25C20.2165 22 21 21.2165 21 20.25V8.75L14.25 2H4.75ZM4.5 3.75C4.5 3.61193 4.61193 3.5 4.75 3.5H13.5V8.75C13.5 9.30228 13.9477 9.75 14.5 9.75H19.5V20.25C19.5 20.3881 19.3881 20.5 19.25 20.5H4.75C4.61193 20.5 4.5 20.3881 4.5 20.25V3.75ZM15 4.56066L18.4393 8H15V4.56066ZM7 12.75C7 12.3358 7.33579 12 7.75 12H16.25C16.6642 12 17 12.3358 17 12.75C17 13.1642 16.6642 13.5 16.25 13.5H7.75C7.33579 13.5 7 13.1642 7 12.75ZM7.75 15.5C7.33579 15.5 7 15.8358 7 16.25C7 16.6642 7.33579 17 7.75 17H13.25C13.6642 17 14 16.6642 14 16.25C14 15.8358 13.6642 15.5 13.25 15.5H7.75ZM7.75 8.5C7.33579 8.5 7 8.83579 7 9.25C7 9.66421 7.33579 10 7.75 10H10.25C10.6642 10 11 9.66421 11 9.25C11 8.83579 10.6642 8.5 10.25 8.5H7.75Z" fill=""/>
      </svg>
    </div>
    <h3 class="mb-2 text-base font-semibold text-gray-800 dark:text-white">Laporan Keseluruhan</h3>
    <p class="text-sm text-gray-500">Export PDF gabungan: tender, proposal, dan performa dalam satu dokumen.</p>
    <div class="mt-4 flex items-center gap-1 text-sm font-medium text-error-500">
      <svg class="fill-error-500" width="14" height="14" viewBox="0 0 24 24">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C11.5858 2 11.25 2.33579 11.25 2.75V14.4393L8.03033 11.2197C7.73744 10.9268 7.26256 10.9268 6.96967 11.2197C6.67678 11.5126 6.67678 11.9874 6.96967 12.2803L11.4697 16.7803C11.7626 17.0732 12.2374 17.0732 12.5303 16.7803L17.0303 12.2803C17.3232 11.9874 17.3232 11.5126 17.0303 11.2197C16.7374 10.9268 16.2626 10.9268 15.9697 11.2197L12.75 14.4393V2.75C12.75 2.33579 12.4142 2 12 2ZM3.75 19C3.75 18.5858 3.41421 18.25 3 18.25C2.58579 18.25 2.25 18.5858 2.25 19V20C2.25 21.2426 3.25736 22.25 4.5 22.25H19.5C20.7426 22.25 21.75 21.2426 21.75 20V19C21.75 18.5858 21.4142 18.25 21 18.25C20.5858 18.25 20.25 18.5858 20.25 19V20C20.25 20.4142 19.9142 20.75 19.5 20.75H4.5C4.08579 20.75 3.75 20.4142 3.75 20V19Z" fill=""/>
      </svg>
      Export PDF
    </div>
  </a>
  @endcan

</div>
@endsection
