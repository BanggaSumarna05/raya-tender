@extends('layouts.app')
@section('title', $proposal->title)
@section('page-title','Detail Proposal')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('proposals.index') }}" class="hover:text-brand-500">Data Proposal</a>
  <span class="text-gray-400">/</span><span>{{ $proposal->code }}</span>
@endsection

@section('content')

{{-- ======================================================
  HEADER
====================================================== --}}
<div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
  <div>
    <div class="mb-2 flex flex-wrap items-center gap-2">
      <span class="font-mono text-xs text-gray-400">{{ $proposal->code }}</span>
      <x-status-badge :status="$proposal->status" />
      {{-- Current version badge --}}
      <span class="badge badge-brand">V{{ $proposal->current_version }}</span>
      @php
        $currentVersionRecord = $proposal->versions->sortByDesc('version_number')->first();
      @endphp
      @if($currentVersionRecord && $currentVersionRecord->revision_number > 0)
      <span class="badge badge-gray">Revisi {{ $currentVersionRecord->revision_number }}</span>
      @elseif($currentVersionRecord && $currentVersionRecord->revision_number === 0)
      <span class="badge badge-gray">Original</span>
      @endif
    </div>
    <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $proposal->title }}</h1>
    <p class="mt-1 text-sm text-gray-500">
      Tender:
      <a href="{{ route('tenders.show', $proposal->tender) }}" class="text-brand-500 hover:text-brand-600">{{ $proposal->tender?->title }}</a>
      · {{ $proposal->tender?->client?->name }}
    </p>
  </div>
  <div class="flex flex-wrap gap-2 shrink-0">
    @can('create_proposals')
    <form action="{{ route('proposals.duplicate', $proposal) }}" method="POST" class="inline" onsubmit="return confirm('Duplikasi proposal ini sebagai proposal baru?')">
      @csrf
      <button type="submit" class="btn btn-secondary btn-sm">
        <svg class="fill-current mr-1" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.75 2.5A.75.75 0 0 1 8.5 1.75h8A.75.75 0 0 1 17.25 2.5v10.75A.75.75 0 0 1 16.5 14H14v2A.75.75 0 0 1 13.25 16.75h-9.5A.75.75 0 0 1 3 16V5.25A.75.75 0 0 1 3.75 4.5H6V2.5ZM6 6H4.5v9.25h8.75V14H7.75A.75.75 0 0 1 7 13.25V6ZM8.5 3.25V12.5h7V3.25H8.5Z" fill=""/></svg>
        Duplikasi
      </button>
    </form>
    @endcan
    @can('update_proposals')
    <a href="{{ route('proposals.edit', $proposal) }}" class="btn btn-primary btn-sm">Edit</a>
    @endcan
    @can('delete_proposals')
    <button class="btn btn-sm btn-secondary text-error-500"
      onclick="confirmDelete('{{ route('proposals.destroy', $proposal) }}','Hapus proposal ini?')">Hapus</button>
    @endcan
  </div>
</div>

{{-- ======================================================
  MAIN GRID
====================================================== --}}
<div class="grid grid-cols-12 gap-5">

  {{-- ================================================
    LEFT COLUMN
  ================================================ --}}
  <div class="col-span-12 lg:col-span-8 space-y-5">

    {{-- Info Card --}}
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Proposal</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">

        {{-- Non-financial fields --}}
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">PIC</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $proposal->pic?->name ?? '—' }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Backup PIC</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $proposal->backupPic?->name ?? '—' }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Deadline</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $proposal->deadline?->format('d M Y') ?? '—' }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Valid Hingga</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $proposal->valid_until?->format('d M Y') ?? '—' }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Versi Saat Ini</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">V{{ $proposal->current_version }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Dibuat Oleh</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $proposal->creator?->name ?? '—' }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Terakhir Diubah</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $proposal->updated_at->format('d M Y, H:i') }}</p>
        </div>

        {{-- FINANCIAL FIELD — masked, loaded via AJAX --}}
        <x-financial-value
          :endpoint="route('proposals.financial-data', $proposal)"
          field="bid_value"
          label="Nilai Penawaran"
        />

      </div>

      @if($proposal->description)
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Deskripsi</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $proposal->description }}</p>
      </div>
      @endif
      @if($proposal->notes)
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Catatan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $proposal->notes }}</p>
      </div>
      @endif
    </div>

    {{-- Documents --}}
    <div class="card">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
          Dokumen <span class="ml-1 badge badge-gray">{{ $proposal->documents->count() }}</span>
        </h3>
        @can('upload_documents')
        <a href="{{ route('documents.create', ['proposal_id' => $proposal->id]) }}" class="btn btn-sm btn-primary">Upload</a>
        @endcan
      </div>
      @if($proposal->documents->isEmpty())
      <div class="py-8 text-center text-sm text-gray-400">Belum ada dokumen</div>
      @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-800/50">
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori</th>
              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Ukuran</th>
              <th class="px-5 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($proposal->documents as $doc)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <td class="px-5 py-3">
                <p class="font-medium text-gray-700 dark:text-gray-300">{{ $doc->name }}</p>
                <p class="text-xs text-gray-400">{{ $doc->original_name }}</p>
              </td>
              <td class="px-5 py-3 text-gray-500">{{ $doc->category?->name ?? '—' }}</td>
              <td class="px-5 py-3 text-gray-500">{{ $doc->file_size_formatted }}</td>
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
                    <svg class="fill-current" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.75H11C11.41 3.75 11.75 4.09 11.75 4.5V5.25H13C13.41 5.25 13.75 5.59 13.75 6C13.75 6.41 13.41 6.75 13 6.75H7C6.59 6.75 6.25 6.41 6.25 6C6.25 5.59 6.59 5.25 7 5.25H8.25V4.5C8.25 4.09 8.59 3.75 9 3.75ZM6.75 8.25H13.25L12.74 16.25C12.7 16.89 12.17 17.39 11.53 17.39H8.47C7.83 17.39 7.3 16.89 7.26 16.25L6.75 8.25Z" fill=""/></svg>
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

    {{-- ================================================
      VERSION HISTORY
    ================================================ --}}
    <div class="card">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
          Riwayat Versi
          <span class="ml-1 badge badge-gray">{{ $proposal->versions->count() }}</span>
        </h3>
      </div>

      <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($proposal->versions->sortByDesc('version_number') as $v)
        @php $isCurrent = ($v->version_number === $proposal->current_version); @endphp
        <div class="flex items-center gap-4 px-5 py-4 {{ $isCurrent ? 'bg-brand-50/50 dark:bg-brand-500/5' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
          {{-- Version badge --}}
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $isCurrent ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }} text-xs font-bold">
            V{{ $v->version_number }}
          </div>

          {{-- Version info --}}
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <span class="text-sm font-semibold text-gray-800 dark:text-white">
                {{ $v->version_label }}
              </span>
              @if($isCurrent)
              <span class="badge badge-brand">Current</span>
              @endif
            </div>
            @if($v->change_notes)
            <p class="mt-0.5 text-xs text-gray-500 truncate">{{ $v->change_notes }}</p>
            @endif
            <p class="mt-0.5 text-xs text-gray-400">
              {{ $v->creator?->name ?? '—' }} · {{ $v->created_at->format('d M Y, H:i') }}
            </p>
          </div>

          {{-- Action --}}
          <div class="shrink-0">
            <a href="{{ route('proposals.versions.show', [$proposal, $v]) }}"
               class="btn btn-sm btn-secondary">
              Lihat Versi
            </a>
          </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada riwayat versi.</div>
        @endforelse
      </div>
    </div>

  </div>{{-- end left col --}}

  {{-- ================================================
    RIGHT COLUMN — Status + Create Revision
  ================================================ --}}
  <div class="col-span-12 lg:col-span-4 space-y-5">

    {{-- Create Revision --}}
    @can('create_proposal_revisions')
    <div class="card p-5" x-data="{ open: false }">
      <div class="flex items-center justify-between mb-1">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Buat Revisi</h3>
        <span class="badge badge-brand">V{{ $proposal->current_version }}</span>
      </div>
      <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
        Membuat revisi akan membuat snapshot versi baru dari proposal ini. Histori versi lama tidak akan berubah.
      </p>
      <button @click="open = !open" class="btn btn-primary w-full btn-sm">
        <svg class="fill-white mr-1.5" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.25C10.4142 3.25 10.75 3.58579 10.75 4V9.25H16C16.4142 9.25 16.75 9.58579 16.75 10C16.75 10.4142 16.4142 10.75 16 10.75H10.75V16C10.75 16.4142 10.4142 16.75 10 16.75C9.58579 16.75 9.25 16.4142 9.25 16V10.75H4C3.58579 10.75 3.25 10.4142 3.25 10C3.25 9.58579 3.58579 9.25 4 9.25H9.25V4C9.25 3.58579 9.58579 3.25 10 3.25Z" fill=""/></svg>
        Buat Revisi V{{ $proposal->current_version + 1 }}
      </button>

      <div x-show="open" x-transition class="mt-4 border-t border-gray-100 dark:border-gray-800 pt-4">
        <form action="{{ route('proposals.revisions.create', $proposal) }}" method="POST" class="space-y-3">
          @csrf
          <div>
            <label class="form-label">Catatan Revisi <span class="text-error-500">*</span></label>
            <textarea
              name="revision_note"
              class="form-textarea"
              rows="3"
              placeholder="Jelaskan perubahan yang dilakukan pada revisi ini..."
              required
            ></textarea>
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-1">Konfirmasi Revisi</button>
            <button type="button" @click="open = false" class="btn btn-secondary btn-sm">Batal</button>
          </div>
        </form>
      </div>
    </div>
    @endcan

    {{-- Update Status --}}
    @can('update_proposals')
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">Ubah Status</h3>
      <form action="{{ route('proposals.update', $proposal) }}" method="POST" class="space-y-3">
        @csrf @method('PUT')
        {{-- Required hidden fields to pass validation --}}
        <input type="hidden" name="title" value="{{ $proposal->title }}" />
        <input type="hidden" name="pic_id" value="{{ $proposal->pic_id }}" />
        <input type="hidden" name="tender_id" value="{{ $proposal->tender_id }}" />
        <select name="status" class="form-select">
          @foreach(\App\Enums\ProposalStatus::cases() as $s)
          <option value="{{ $s->value }}" @selected($proposal->status->value === $s->value)>{{ $s->label() }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary w-full btn-sm">Simpan Status</button>
      </form>
    </div>
    @endcan

    {{-- Meta --}}
    <div class="card p-5">
      <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">Informasi Lainnya</h3>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="text-gray-500">Dibuat oleh</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $proposal->creator?->name ?? '—' }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Dibuat pada</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $proposal->created_at->format('d M Y, H:i') }}</span>
        </div>
        @if($proposal->submitted_at)
        <div class="flex justify-between">
          <span class="text-gray-500">Dikirim pada</span>
          <span class="text-gray-700 dark:text-gray-300">{{ $proposal->submitted_at->format('d M Y, H:i') }}</span>
        </div>
        @endif
      </div>
    </div>

  </div>{{-- end right col --}}
</div>

@endsection
