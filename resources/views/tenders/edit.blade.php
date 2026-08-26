@extends('layouts.app')
@section('title','Edit Tender')
@section('page-title','Edit Tender')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('tenders.index') }}" class="hover:text-brand-500">Data Tender</a>
  <span class="text-gray-400">/</span><a href="{{ route('tenders.show', $tender) }}" class="hover:text-brand-500">{{ $tender->code }}</a>
  <span class="text-gray-400">/</span><span>Edit</span>
@endsection

@section('content')
<form action="{{ route('tenders.update', $tender) }}" method="POST" novalidate>
@csrf @method('PUT')
<div class="grid grid-cols-12 gap-5">

  {{-- Main --}}
  <div class="col-span-12 xl:col-span-8 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Tender</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Nama Tender <span class="text-error-500">*</span></label>
          <input type="text" name="title" class="form-input @error('title') border-error-300 @enderror" value="{{ old('title', $tender->title) }}" />
          @error('title')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">Klien <span class="text-error-500">*</span></label>
            <select name="client_id" class="form-select @error('client_id') border-error-300 @enderror">
              @foreach($clients as $c)<option value="{{ $c->id }}" @selected(old('client_id', $tender->client_id)==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
            @error('client_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="form-label">Kategori <span class="text-error-500">*</span></label>
            <select name="category_id" class="form-select @error('category_id') border-error-300 @enderror">
              @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $tender->category_id)==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
            @error('category_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">Sumber</label>
            <input type="text" name="source" class="form-input" value="{{ old('source', $tender->source) }}" />
          </div>
          <div>
            <label class="form-label">Referensi Sumber</label>
            <input type="text" name="source_reference" class="form-input" value="{{ old('source_reference', $tender->source_reference) }}" placeholder="No. Pengumuman / URL..." />
          </div>
        </div>
        <div>
          <label class="form-label">Lokasi</label>
          <input type="text" name="location" class="form-input" value="{{ old('location', $tender->location) }}" />
        </div>
        <div>
          <label class="form-label">Persyaratan</label>
          <textarea name="requirements" class="form-textarea" rows="3" placeholder="Persyaratan dokumen atau kualifikasi tender...">{{ old('requirements', $tender->requirements) }}</textarea>
        </div>
        <div>
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-textarea" rows="3">{{ old('description', $tender->description) }}</textarea>
        </div>
        <div>
          <label class="form-label">Catatan</label>
          <textarea name="notes" class="form-textarea" rows="2">{{ old('notes', $tender->notes) }}</textarea>
        </div>
      </div>
    </div>

    {{-- Alasan Kalah — hanya muncul saat status = lost --}}
    <div id="reason-card" class="card p-5 {{ old('status', $tender->status->value) === 'lost' ? '' : 'hidden' }}">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">Alasan Kalah</h3>
      <div>
        <label class="form-label">Alasan Kalah</label>
        <textarea name="lost_reason" class="form-textarea" rows="2">{{ old('lost_reason', $tender->lost_reason) }}</textarea>
      </div>
    </div>
  </div>

  {{-- Sidebar --}}
  <div class="col-span-12 xl:col-span-4 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Status & Pengaturan</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Status</label>
          <select name="status" id="status" class="form-select">
            @foreach(\App\Enums\TenderStatus::cases() as $s)
            <option value="{{ $s->value }}" @selected(old('status', $tender->status->value) === $s->value)>{{ $s->label() }}</option>
            @endforeach
          </select>
          <p class="mt-1 text-xs text-gray-400">Perubahan status via form ini tidak melewati transition validation. Gunakan tombol "Ubah Status" di halaman detail.</p>
        </div>
        <div>
          <label class="form-label">Prioritas</label>
          <select name="priority" class="form-select">
            @foreach(\App\Enums\TenderPriority::cases() as $p)
            <option value="{{ $p->value }}" @selected(old('priority', $tender->priority->value) === $p->value)>{{ $p->label() }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">PIC <span class="text-error-500">*</span></label>
          <select name="pic_id" class="form-select @error('pic_id') border-error-300 @enderror">
            @foreach($pics as $p)<option value="{{ $p->id }}" @selected(old('pic_id', $tender->pic_id)==$p->id)>{{ $p->name }}</option>@endforeach
          </select>
          @error('pic_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Backup PIC</label>
          <select name="backup_pic_id" class="form-select @error('backup_pic_id') border-error-300 @enderror">
            <option value="">— Tidak ada —</option>
            @foreach($pics as $p)<option value="{{ $p->id }}" @selected(old('backup_pic_id', $tender->backup_pic_id)==$p->id)>{{ $p->name }}</option>@endforeach
          </select>
          <p class="mt-1 text-xs text-gray-400">Backup PIC menerima reminder deadline jika PIC tidak tersedia.</p>
          @error('backup_pic_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Nilai Estimasi (Rp)</label>
          <input type="number" name="estimated_value" class="form-input" value="{{ old('estimated_value', $tender->estimated_value) }}" min="0" />
          <p class="mt-1 text-xs text-gray-400">Nilai tidak akan ditampilkan di listing.</p>
        </div>
      </div>
    </div>

    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Tanggal</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Tanggal Diterima</label>
          <input type="date" name="received_date" class="form-input" value="{{ old('received_date', $tender->received_date?->format('Y-m-d')) }}" />
        </div>
        <div>
          <label class="form-label">Deadline Submission</label>
          <input type="datetime-local" name="submission_deadline" class="form-input" value="{{ old('submission_deadline', $tender->submission_deadline?->format('Y-m-d\TH:i')) }}" />
        </div>
        <div>
          <label class="form-label">Mulai Proyek</label>
          <input type="date" name="project_start_date" class="form-input" value="{{ old('project_start_date', $tender->project_start_date?->format('Y-m-d')) }}" />
        </div>
        <div>
          <label class="form-label">Selesai Proyek</label>
          <input type="date" name="project_end_date" class="form-input" value="{{ old('project_end_date', $tender->project_end_date?->format('Y-m-d')) }}" />
        </div>
      </div>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary flex-1">Simpan Perubahan</button>
      <a href="{{ route('tenders.show', $tender) }}" class="btn btn-secondary">Batal</a>
    </div>
  </div>

</div>
</form>
@endsection

@push('scripts')
<script>
const statusEl   = document.getElementById('status');
const reasonCard = document.getElementById('reason-card');
function toggleReason() {
  if (statusEl.value === 'lost') {
    reasonCard.classList.remove('hidden');
  } else {
    reasonCard.classList.add('hidden');
  }
}
statusEl.addEventListener('change', toggleReason);
</script>
@endpush
