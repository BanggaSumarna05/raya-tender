@extends('layouts.app')
@section('title','Tambah Tender')
@section('page-title','Tambah Tender')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('tenders.index') }}" class="hover:text-brand-500">Data Tender</a>
  <span class="text-gray-400">/</span><span>Tambah</span>
@endsection

@section('content')
<form action="{{ route('tenders.store') }}" method="POST" novalidate>
@csrf
<div class="grid grid-cols-12 gap-5">

  {{-- Main --}}
  <div class="col-span-12 xl:col-span-8 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Tender</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Nama Tender <span class="text-error-500">*</span></label>
          <input type="text" name="title" class="form-input @error('title') border-error-300 @enderror" value="{{ old('title') }}" placeholder="Masukkan nama tender" />
          @error('title')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">Klien <span class="text-error-500">*</span></label>
            <select name="client_id" class="form-select @error('client_id') border-error-300 @enderror">
              <option value="">— Pilih Klien —</option>
              @foreach($clients as $c)<option value="{{ $c->id }}" @selected(old('client_id')==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
            @error('client_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="form-label">Kategori <span class="text-error-500">*</span></label>
            <select name="category_id" class="form-select @error('category_id') border-error-300 @enderror">
              <option value="">— Pilih Kategori —</option>
              @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id')==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
            @error('category_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">Sumber</label>
            <input type="text" name="source" class="form-input" value="{{ old('source') }}" placeholder="LPSE, Direct, Referral..." />
          </div>
          <div>
            <label class="form-label">Referensi Sumber</label>
            <input type="text" name="source_reference" class="form-input" value="{{ old('source_reference') }}" placeholder="No. Pengumuman / URL..." />
          </div>
        </div>
        <div>
          <label class="form-label">Lokasi</label>
          <input type="text" name="location" class="form-input" value="{{ old('location') }}" placeholder="Kota / Provinsi" />
        </div>
        <div>
          <label class="form-label">Persyaratan</label>
          <textarea name="requirements" class="form-textarea" rows="3" placeholder="Persyaratan dokumen atau kualifikasi tender...">{{ old('requirements') }}</textarea>
        </div>
        <div>
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-textarea" rows="3" placeholder="Deskripsi singkat tender...">{{ old('description') }}</textarea>
        </div>
        <div>
          <label class="form-label">Catatan</label>
          <textarea name="notes" class="form-textarea" rows="2" placeholder="Catatan internal...">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>

    {{-- Alasan Kalah — hanya muncul saat status = lost --}}
    <div id="reason-card" class="card p-5 hidden">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">Alasan Kalah</h3>
      <div>
        <label class="form-label">Alasan Kalah <span class="text-error-500">*</span></label>
        <textarea name="lost_reason" class="form-textarea @error('lost_reason') border-error-300 @enderror" rows="2" placeholder="Jelaskan alasan tender tidak berhasil...">{{ old('lost_reason') }}</textarea>
        @error('lost_reason')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- Sidebar --}}
  <div class="col-span-12 xl:col-span-4 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Status & Pengaturan</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Status <span class="text-error-500">*</span></label>
          <select name="status" id="status" class="form-select">
            @foreach(\App\Enums\TenderStatus::cases() as $s)
            <option value="{{ $s->value }}" @selected(old('status', 'draft') === $s->value)>{{ $s->label() }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Prioritas <span class="text-error-500">*</span></label>
          <select name="priority" class="form-select">
            @foreach(\App\Enums\TenderPriority::cases() as $p)
            <option value="{{ $p->value }}" @selected(old('priority', 'medium') === $p->value)>{{ $p->label() }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">PIC <span class="text-error-500">*</span></label>
          <select name="pic_id" class="form-select @error('pic_id') border-error-300 @enderror">
            <option value="">— Pilih PIC —</option>
            @foreach($pics as $p)<option value="{{ $p->id }}" @selected(old('pic_id')==$p->id)>{{ $p->name }}</option>@endforeach
          </select>
          @error('pic_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Backup PIC</label>
          <select name="backup_pic_id" class="form-select @error('backup_pic_id') border-error-300 @enderror">
            <option value="">— Tidak ada —</option>
            @foreach($pics as $p)<option value="{{ $p->id }}" @selected(old('backup_pic_id')==$p->id)>{{ $p->name }}</option>@endforeach
          </select>
          <p class="mt-1 text-xs text-gray-400">Backup PIC menerima reminder deadline jika PIC tidak tersedia.</p>
          @error('backup_pic_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Nilai Estimasi (Rp)</label>
          <input type="number" name="estimated_value" class="form-input" value="{{ old('estimated_value') }}" min="0" placeholder="0" />
          <p class="mt-1 text-xs text-gray-400">Nilai tidak akan ditampilkan di listing.</p>
        </div>
      </div>
    </div>

    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Tanggal</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Tanggal Diterima</label>
          <input type="date" name="received_date" class="form-input" value="{{ old('received_date') }}" />
        </div>
        <div>
          <label class="form-label">Deadline Submission</label>
          <input type="datetime-local" name="submission_deadline" class="form-input @error('submission_deadline') border-error-300 @enderror" value="{{ old('submission_deadline') }}" />
          @error('submission_deadline')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Mulai Proyek</label>
          <input type="date" name="project_start_date" class="form-input" value="{{ old('project_start_date') }}" />
        </div>
        <div>
          <label class="form-label">Selesai Proyek</label>
          <input type="date" name="project_end_date" class="form-input" value="{{ old('project_end_date') }}" />
        </div>
      </div>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary flex-1">Simpan Tender</button>
      <a href="{{ route('tenders.index') }}" class="btn btn-secondary">Batal</a>
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
toggleReason();
</script>
@endpush
