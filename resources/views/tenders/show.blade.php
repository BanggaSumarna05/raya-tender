@extends('layouts.app')
@section('title', $tender->title)
@section('page-title','Detail Tender')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('tenders.index') }}" class="hover:text-brand-500">Data Tender</a>
  <span class="text-gray-400">/</span><span>{{ $tender->code }}</span>
@endsection

@section('content')

{{-- ======================================================
  HEADER
====================================================== --}}
<div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
  <div>
    <div class="mb-2 flex flex-wrap items-center gap-2">
      <span class="font-mono text-xs text-gray-400">{{ $tender->code }}</span>
      <x-status-badge :status="$tender->status" />
      <x-status-badge :status="$tender->priority" />
      @if($tender->days_until_deadline !== null)
      @php
        $days = $tender->days_until_deadline;
        $dlClass = $days < 0 ? 'badge badge-error' : ($days <= 3 ? 'badge badge-error' : ($days <= 7 ? 'badge badge-warning' : 'badge badge-success'));
      @endphp
      <span class="{{ $dlClass }}">{{ $tender->deadline_label }}</span>
      @endif
    </div>
    <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $tender->title }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ $tender->client?->name }} · {{ $tender->category?->name }}</p>
  </div>
  <div class="flex flex-wrap items-center gap-2 shrink-0">
    @can('create_tenders')
    <form action="{{ route('tenders.duplicate', $tender) }}" method="POST" class="inline" onsubmit="return confirm('Duplikasi tender ini sebagai draft baru?')">
      @csrf
      <button type="submit" class="btn btn-secondary btn-sm">
        <svg class="fill-current mr-1" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.75 2.5A.75.75 0 0 1 8.5 1.75h8A.75.75 0 0 1 17.25 2.5v10.75A.75.75 0 0 1 16.5 14H14v2A.75.75 0 0 1 13.25 16.75h-9.5A.75.75 0 0 1 3 16V5.25A.75.75 0 0 1 3.75 4.5H6V2.5ZM6 6H4.5v9.25h8.75V14H7.75A.75.75 0 0 1 7 13.25V6ZM8.5 3.25V12.5h7V3.25H8.5Z" fill=""/></svg>
        Duplikasi
      </button>
    </form>
    @endcan
    @can('update_tenders')
    <a href="{{ route('tenders.edit', $tender) }}" class="btn btn-primary btn-sm">Edit Tender</a>
    @endcan
    @can('delete_tenders')
    <button class="btn btn-sm btn-secondary text-error-500"
      onclick="confirmDelete('{{ route('tenders.destroy', $tender) }}','Hapus tender ini?')">Hapus</button>
    @endcan
  </div>
</div>

{{-- ======================================================
  STATUS STEPPER — Tender Workflow Pipeline
====================================================== --}}
@php
  $stepLabels = [
    'draft'         => 'Draft',
    'identified'    => 'Identified',
    'qualification' => 'Qualification',
    'preparation'   => 'Preparation',
    'submitted'     => 'Submitted',
    'evaluation'    => 'Evaluation',
    'clarification' => 'Clarification',
    'negotiation'   => 'Negotiation',
    'won'           => 'Won / Lost',
  ];
  $currentStep   = $tender->status->stepIndex();
  $isCancelled   = $tender->status->value === 'cancelled';
  $isCompleted   = in_array($tender->status->value, ['completed']);
  $isWon         = $tender->status->value === 'won';
  $isLost        = $tender->status->value === 'lost';
@endphp

<div class="card p-5 mb-5 overflow-x-auto">
  @if($isCancelled)
  <div class="flex items-center gap-3 rounded-lg bg-gray-100 dark:bg-gray-800 px-4 py-3">
    <svg class="fill-gray-400 shrink-0" width="18" height="18" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58 2 2 5.58 2 10s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3.54 10.46a1 1 0 01-1.41 1.41L10 11.41l-2.12 2.12a1 1 0 01-1.41-1.41L8.59 10 6.47 7.88A1 1 0 017.88 6.47L10 8.59l2.12-2.12a1 1 0 011.41 1.41L11.41 10l2.13 2.46z" fill=""/></svg>
    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Tender ini telah <strong>Dibatalkan</strong>.</span>
  </div>
  @elseif($isCompleted)
  <div class="flex items-center gap-3 rounded-lg bg-success-50 dark:bg-success-500/10 px-4 py-3">
    <svg class="fill-success-500 shrink-0" width="18" height="18" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58 2 2 5.58 2 10s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm4.29 5.29a1 1 0 010 1.42l-5 5a1 1 0 01-1.42 0l-2-2a1 1 0 111.42-1.42L8.5 11.59l4.29-4.3a1 1 0 011.5 0z" fill=""/></svg>
    <span class="text-sm font-medium text-success-700 dark:text-success-400">Tender ini telah <strong>Completed</strong>.</span>
  </div>
  @else
  <div class="min-w-[700px]">
    <div class="flex items-center">
      @foreach($stepLabels as $stepKey => $stepName)
      @php
        $stepIdx     = array_search($stepKey, array_keys($stepLabels));
        $isDone      = $currentStep > $stepIdx;
        $isCurrent   = $currentStep === $stepIdx || ($stepIdx === 8 && in_array($tender->status->value, ['won','lost']));
        $isFuture    = $currentStep < $stepIdx && !($stepIdx === 8 && in_array($tender->status->value, ['won','lost']));
        $isLast      = $stepIdx === count($stepLabels) - 1;
        // Special: won/lost share step 8
        if ($stepKey === 'won') {
          $isDone    = in_array($tender->status->value, ['completed']);
          $isCurrent = in_array($tender->status->value, ['won','lost']);
        }
      @endphp
      <div class="flex flex-col items-center {{ !$isLast ? 'flex-1' : '' }}">
        {{-- Node --}}
        <div class="relative flex items-center justify-center">
          @if($isDone)
          <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500 text-white">
            <svg class="fill-white" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 111.4-1.4L9 11.6l6.3-6.3a1 1 0 011.4 0z" fill=""/></svg>
          </div>
          @elseif($isCurrent)
          <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-brand-500 bg-white dark:bg-gray-900">
            <div class="h-3 w-3 rounded-full bg-brand-500"></div>
          </div>
          @else
          <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            <div class="h-2.5 w-2.5 rounded-full bg-gray-200 dark:bg-gray-700"></div>
          </div>
          @endif
        </div>
        {{-- Label --}}
        <span class="mt-1.5 whitespace-nowrap text-center text-xs
          {{ $isCurrent ? 'font-bold text-brand-600 dark:text-brand-400' : ($isDone ? 'font-medium text-gray-600 dark:text-gray-400' : 'text-gray-400 dark:text-gray-600') }}">
          @if($stepKey === 'won')
            @if($isWon) <span class="text-success-600 dark:text-success-400">Won</span>
            @elseif($isLost) <span class="text-error-600 dark:text-error-400">Lost</span>
            @else Won / Lost
            @endif
          @else
            {{ $stepName }}
          @endif
        </span>
      </div>
      {{-- Connector line --}}
      @if(!$isLast)
      <div class="mb-5 h-0.5 flex-1 {{ $currentStep > $stepIdx ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
      @endif
      @endforeach
    </div>
  </div>
  @endif
</div>

{{-- ======================================================
  MAIN GRID
====================================================== --}}
<div class="grid grid-cols-12 gap-5">

  {{-- ================================================
    LEFT COLUMN — Detail + Proposals + Documents
  ================================================ --}}
  <div class="col-span-12 lg:col-span-8 space-y-5">

    {{-- Info Card --}}
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Tender</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">

        {{-- Non-financial fields --}}
        @foreach([
          ['Klien',           $tender->client?->name],
          ['Kategori',        $tender->category?->name],
          ['PIC',             $tender->pic?->name],
          ['Backup PIC',      $tender->backupPic?->name ?? '—'],
          ['Lokasi',          $tender->location],
          ['Sumber',          $tender->source],
          ['Tanggal Diterima',$tender->received_date?->format('d M Y')],
          ['Deadline',        $tender->submission_deadline?->format('d M Y, H:i')],
        ] as [$lbl, $val])
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $lbl }}</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $val ?? '—' }}</p>
        </div>
        @endforeach

        {{-- FINANCIAL FIELD — masked, loaded via AJAX --}}
        <x-financial-value
          :endpoint="route('tenders.financial-data', $tender)"
          field="estimated_value"
          label="Nilai Estimasi"
        />

      </div>

      @if($tender->description)
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Deskripsi</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $tender->description }}</p>
      </div>
      @endif
      @if($tender->notes)
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Catatan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $tender->notes }}</p>
      </div>
      @endif
      @if($tender->lost_reason)
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Alasan Kalah</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $tender->lost_reason }}</p>
      </div>
      @endif
    </div>

    {{-- Proposals --}}
    <div class="card">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
          Proposal <span class="ml-1 badge badge-gray">{{ $tender->proposals->count() }}</span>
        </h3>
        @can('create_proposals')
        <a href="{{ route('proposals.create', ['tender_id' => $tender->id]) }}" class="btn btn-sm btn-primary">Tambah</a>
        @endcan
      </div>
      @if($tender->proposals->isEmpty())
      <div class="py-8 text-center text-sm text-gray-400">Belum ada proposal</div>
      @else
      <div class="overflow-x-auto">
        <table class="w-full min-w-[580px] text-sm">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-800/50">
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Kode</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Judul</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Versi</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">PIC</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Deadline</th>
              <th class="px-5 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($tender->proposals as $p)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $p->code }}</td>
              <td class="px-5 py-3">
                <a href="{{ route('proposals.show', $p) }}" class="font-medium text-gray-800 hover:text-brand-500 dark:text-white">{{ $p->title }}</a>
              </td>
              <td class="px-5 py-3">
                <span class="badge badge-gray">V{{ $p->current_version }}</span>
              </td>
              <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $p->pic?->name ?? '—' }}</td>
              <td class="px-5 py-3"><x-status-badge :status="$p->status" /></td>
              <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $p->deadline?->format('d M Y') ?? '—' }}</td>
              <td class="px-5 py-3">
                <a href="{{ route('proposals.show', $p) }}" class="btn-icon">
                  <svg class="fill-current" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill=""/></svg>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>

    {{-- Documents --}}
    <div class="card">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
          Dokumen <span class="ml-1 badge badge-gray">{{ $tender->documents->count() }}</span>
        </h3>
        @can('upload_documents')
        <a href="{{ route('documents.create', ['tender_id' => $tender->id]) }}" class="btn btn-sm btn-primary">Upload</a>
        @endcan
      </div>
      @if($tender->documents->isEmpty())
      <div class="py-8 text-center text-sm text-gray-400">Belum ada dokumen</div>
      @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-800/50">
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Ukuran</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Upload</th>
              <th class="px-5 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($tender->documents as $doc)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <td class="px-5 py-3">
                <p class="font-medium text-gray-700 dark:text-gray-300">{{ $doc->name }}</p>
                <p class="text-xs text-gray-400">{{ $doc->original_name }}</p>
              </td>
              <td class="px-5 py-3 text-gray-500">{{ $doc->category?->name ?? '—' }}</td>
              <td class="px-5 py-3 text-gray-500">{{ $doc->file_size_formatted }}</td>
              <td class="px-5 py-3 text-gray-500">{{ $doc->uploader?->name }} · {{ $doc->created_at->format('d M') }}</td>
              <td class="px-5 py-3">
                <div class="flex gap-1">
                  @can('download_documents')
                  <a href="{{ route('documents.download', $doc) }}" class="btn-icon" title="Download">
                    <svg class="fill-current" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.25C10.4142 2.25 10.75 2.58579 10.75 3V11.1893L12.9697 8.96967C13.2626 8.67678 13.7374 8.67678 14.0303 8.96967C14.3232 9.26256 14.3232 9.73744 14.0303 10.0303L10.5303 13.5303C10.2374 13.8232 9.76256 13.8232 9.46967 13.5303L5.96967 10.0303C5.67678 9.73744 5.67678 9.26256 5.96967 8.96967C6.26256 8.67678 6.73744 8.67678 7.03033 8.96967L9.25 11.1893V3C9.25 2.58579 9.58579 2.25 10 2.25ZM3.25 15C3.25 14.5858 3.58579 14.25 4 14.25H16C16.4142 14.25 16.75 14.5858 16.75 15C16.75 15.4142 16.4142 15.75 16 15.75H4C3.58579 15.75 3.25 15.4142 3.25 15Z" fill=""/></svg>
                  </a>
                  @endcan
                  @can('delete_documents')
                  <button class="btn-icon text-error-500"
                    onclick="confirmDelete('{{ route('documents.destroy', $doc) }}','Hapus dokumen ini?')">
                    <svg class="fill-current" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.75H11C11.4142 3.75 11.75 4.09 11.75 4.5V5.25H13C13.4142 5.25 13.75 5.59 13.75 6C13.75 6.41 13.4142 6.75 13 6.75H7C6.59 6.75 6.25 6.41 6.25 6C6.25 5.59 6.59 5.25 7 5.25H8.25V4.5C8.25 4.09 8.59 3.75 9 3.75ZM6.75 8.25H13.25L12.74 16.25C12.7 16.89 12.17 17.39 11.53 17.39H8.47C7.83 17.39 7.3 16.89 7.26 16.25L6.75 8.25Z" fill=""/></svg>
                  </button>
                  @endcan
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>

  </div>{{-- end left col --}}

  {{-- ================================================
    RIGHT COLUMN — Status Change + History
  ================================================ --}}
  <div class="col-span-12 lg:col-span-4 space-y-5">

    {{-- Change Status --}}
    @can('change_tender_status')
    @if(!empty($allowedTransitions))
    <div class="card p-5" x-data="{ newStatus: '' }">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">Ubah Status</h3>
      <form action="{{ route('tenders.change-status', $tender) }}" method="POST" class="space-y-3">
        @csrf
        <div>
          <label class="form-label">Status Baru</label>
          <select name="status" class="form-select" x-model="newStatus" required>
            <option value="">— Pilih Status —</option>
            @foreach($allowedTransitions as $nextStatus)
            @php $statusEnum = \App\Enums\TenderStatus::from($nextStatus); @endphp
            <option value="{{ $nextStatus }}">{{ $statusEnum->label() }}</option>
            @endforeach
          </select>
        </div>
        {{-- Alasan kalah — only shows when lost selected --}}
        <div x-show="newStatus === 'lost'" x-transition>
          <label class="form-label">Alasan Kalah <span class="text-error-500">*</span></label>
          <textarea name="lost_reason" class="form-textarea" rows="2" placeholder="Jelaskan alasan tender tidak berhasil..."></textarea>
        </div>
        <div>
          <label class="form-label">Catatan</label>
          <textarea name="notes" class="form-textarea" rows="2" placeholder="Catatan perubahan status (opsional)..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-full btn-sm">Simpan Status</button>
      </form>
    </div>
    @else
    <div class="card p-4">
      <p class="text-sm text-gray-500 dark:text-gray-400">
        @if(in_array($tender->status->value, ['completed','cancelled']))
        Status <strong>{{ $tender->status->label() }}</strong> adalah status final.
        @else
        Tidak ada transisi status yang tersedia.
        @endif
      </p>
    </div>
    @endif
    @endcan

    {{-- Status History Timeline --}}
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">Riwayat Status</h3>
      <div class="space-y-4 max-h-80 overflow-y-auto custom-scrollbar">
        @forelse($tender->statusHistories->reverse() as $h)
        <div class="relative pl-5
          before:absolute before:left-1.5 before:top-2 before:h-2 before:w-2 before:rounded-full before:bg-brand-500 before:border-2 before:border-white dark:before:border-gray-900
          @if(!$loop->last) after:absolute after:left-[7px] after:top-4 after:h-full after:w-px after:bg-gray-100 dark:after:bg-gray-800 @endif">
          <p class="text-xs text-gray-400">{{ $h->created_at->format('d M Y, H:i') }}</p>
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5">
            @if($h->from_status)
              <span class="text-gray-400">{{ ucfirst(str_replace('_', ' ', $h->from_status)) }}</span>
              <span class="mx-1 text-gray-300">→</span>
            @endif
            <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $h->to_status)) }}</span>
          </p>
          @if($h->notes)<p class="text-xs text-gray-500 mt-0.5">{{ $h->notes }}</p>@endif
          <p class="text-xs text-gray-400">{{ $h->changedBy?->name }}</p>
        </div>
        @empty
        <p class="text-sm text-gray-400">Belum ada riwayat.</p>
        @endforelse
      </div>
    </div>

    {{-- Meta --}}
    <div class="card p-5">
      <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">Informasi Lainnya</h3>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="text-gray-500">Dibuat oleh</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $tender->creator?->name ?? '—' }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Dibuat pada</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $tender->created_at->format('d M Y, H:i') }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Diperbarui oleh</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $tender->updater?->name ?? '—' }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Diperbarui pada</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $tender->updated_at->format('d M Y, H:i') }}</span>
        </div>
        @if($tender->project_start_date)
        <div class="flex justify-between">
          <span class="text-gray-500">Mulai Proyek</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $tender->project_start_date->format('d M Y') }}</span>
        </div>
        @endif
        @if($tender->project_end_date)
        <div class="flex justify-between">
          <span class="text-gray-500">Selesai Proyek</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $tender->project_end_date->format('d M Y') }}</span>
        </div>
        @endif
      </div>
    </div>

  </div>{{-- end right col --}}
</div>

@endsection
