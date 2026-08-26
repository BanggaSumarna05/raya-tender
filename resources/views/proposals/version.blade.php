@extends('layouts.app')
@section('title', "Proposal {$proposal->code} — {$version->version_label}")
@section('page-title','Detail Versi Proposal')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('proposals.index') }}" class="hover:text-brand-500">Data Proposal</a>
  <span class="text-gray-400">/</span><a href="{{ route('proposals.show', $proposal) }}" class="hover:text-brand-500">{{ $proposal->code }}</a>
  <span class="text-gray-400">/</span><span>{{ $version->version_label }}</span>
@endsection

@section('content')

{{-- ======================================================
  READ-ONLY BANNER
====================================================== --}}
@if(!$isCurrentVersion)
<div class="mb-5 flex items-start gap-3 rounded-xl border border-warning-200 bg-warning-50 p-4 dark:border-warning-700/30 dark:bg-warning-500/10">
  <svg class="mt-0.5 shrink-0 fill-warning-500" width="18" height="18" viewBox="0 0 20 20">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" fill=""/>
  </svg>
  <div>
    <p class="text-sm font-semibold text-warning-800 dark:text-warning-300">
      Anda sedang melihat Proposal Version {{ $version->version_label }}
    </p>
    <p class="mt-0.5 text-sm text-warning-700 dark:text-warning-400">
      Version ini adalah histori dan <strong>tidak dapat diedit</strong>.
      Untuk mengubah, buat versi baru dari
      <a href="{{ route('proposals.show', $proposal) }}" class="underline hover:no-underline">versi terkini</a>.
    </p>
  </div>
  <a href="{{ route('proposals.show', $proposal) }}"
    class="ml-auto shrink-0 rounded-lg bg-warning-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-warning-600">
    Versi Terkini
  </a>
</div>
@else
<div class="mb-5 flex items-center gap-3 rounded-xl border border-brand-200 bg-brand-50 p-4 dark:border-brand-700/30 dark:bg-brand-500/10">
  <svg class="shrink-0 fill-brand-500" width="18" height="18" viewBox="0 0 20 20">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58 2 2 5.58 2 10s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3.7 6.3a1 1 0 010 1.4l-4 4a1 1 0 01-1.4 0l-2-2a1 1 0 111.4-1.4L9 11.6l3.3-3.3a1 1 0 011.4 0z" fill=""/>
  </svg>
  <p class="text-sm font-medium text-brand-700 dark:text-brand-300">
    Ini adalah <strong>versi terkini</strong> dari proposal ini.
  </p>
  <a href="{{ route('proposals.show', $proposal) }}" class="ml-auto shrink-0 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">
    Kembali ke Detail
  </a>
</div>
@endif

{{-- ======================================================
  HEADER
====================================================== --}}
<div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
  <div>
    <div class="mb-2 flex flex-wrap items-center gap-2">
      <span class="font-mono text-xs text-gray-400">{{ $proposal->code }}</span>
      <span class="badge badge-brand">V{{ $version->version_number }}</span>
      <span class="badge badge-gray">{{ $version->revision_label }}</span>
      @if($version->status)
      @php
        try { $vStatus = \App\Enums\ProposalStatus::from($version->status); } catch (\ValueError $e) { $vStatus = null; }
      @endphp
      @if($vStatus)<x-status-badge :status="$vStatus" />@endif
      @endif
    </div>
    <h1 class="text-xl font-bold text-gray-800 dark:text-white">
      {{ $version->title ?? $proposal->title }}
    </h1>
    <p class="mt-1 text-sm text-gray-500">
      Tender: <a href="{{ route('tenders.show', $proposal->tender) }}" class="text-brand-500 hover:text-brand-600">{{ $proposal->tender?->title }}</a>
      · {{ $proposal->tender?->client?->name }}
    </p>
  </div>
  {{-- No edit button for version view — read-only --}}
  <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-secondary btn-sm shrink-0">
    ← Kembali
  </a>
</div>

{{-- ======================================================
  VERSION DETAIL
====================================================== --}}
<div class="grid grid-cols-12 gap-5">
  <div class="col-span-12 xl:col-span-8 space-y-5">

    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">
        Snapshot Data — {{ $version->version_label }}
      </h3>
      <div class="grid grid-cols-2 gap-x-8 gap-y-4">

        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">PIC</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $proposal->pic?->name ?? '—' }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Deadline</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
            {{ $version->deadline?->format('d M Y') ?? '—' }}
          </p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Valid Hingga</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
            {{ $version->valid_until?->format('d M Y') ?? '—' }}
          </p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Dibuat pada</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
            {{ $version->created_at->format('d M Y, H:i') }}
          </p>
        </div>

        {{-- Financial field — masked, AJAX loaded --}}
        <x-financial-value
          :endpoint="route('proposals.financial-data', $proposal)"
          field="bid_value"
          label="Nilai Penawaran (saat ini)"
        />

      </div>

      @if($version->description)
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Deskripsi</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $version->description }}</p>
      </div>
      @endif

      @if($version->change_notes)
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Catatan Revisi</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $version->change_notes }}</p>
      </div>
      @endif
    </div>

  </div>

  {{-- Right sidebar --}}
  <div class="col-span-12 xl:col-span-4 space-y-5">

    {{-- Version Navigation --}}
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">Semua Versi</h3>
      <div class="space-y-2">
        @foreach($proposal->versions->sortByDesc('version_number') as $v)
        @php $isThis = $v->id === $version->id; @endphp
        <a href="{{ route('proposals.versions.show', [$proposal, $v]) }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 transition-colors
             {{ $isThis ? 'bg-brand-50 dark:bg-brand-500/10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
          <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold
            {{ $isThis ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
            V{{ $v->version_number }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium {{ $isThis ? 'text-brand-700 dark:text-brand-300' : 'text-gray-700 dark:text-gray-300' }}">
              {{ $v->version_label }}
              @if($v->version_number === $proposal->current_version)
              <span class="ml-1 badge badge-brand text-xs">Current</span>
              @endif
            </p>
            <p class="text-xs text-gray-400 truncate">{{ $v->created_at->format('d M Y') }}</p>
          </div>
        </a>
        @endforeach
      </div>
    </div>

    {{-- Version created by --}}
    <div class="card p-5">
      <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">Tentang Versi Ini</h3>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="text-gray-500">Versi</span>
          <span class="font-medium text-gray-700 dark:text-gray-300">{{ $version->version_label }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Dibuat oleh</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $version->creator?->name ?? '—' }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Dibuat pada</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $version->created_at->format('d M Y, H:i') }}</span>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
