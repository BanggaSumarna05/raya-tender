@extends('layouts.app')
@section('title','Tambah Proposal')
@section('page-title','Tambah Proposal')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('proposals.index') }}" class="hover:text-brand-500">Data Proposal</a>
  <span class="text-gray-400">/</span><span>Tambah</span>
@endsection

@section('content')
<form action="{{ route('proposals.store') }}" method="POST" novalidate>
@csrf
<div class="grid grid-cols-12 gap-5">

  <div class="col-span-12 xl:col-span-8 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Proposal</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Tender <span class="text-error-500">*</span></label>
          <select name="tender_id" class="form-select @error('tender_id') border-error-300 @enderror">
            <option value="">— Pilih Tender —</option>
            @foreach($tenders as $t)
            <option value="{{ $t->id }}" @selected(old('tender_id', $selectedTender?->id)==$t->id)>{{ $t->code }} — {{ $t->title }}</option>
            @endforeach
          </select>
          @error('tender_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Judul Proposal <span class="text-error-500">*</span></label>
          <input type="text" name="title" class="form-input @error('title') border-error-300 @enderror"
            value="{{ old('title') }}" placeholder="Masukkan judul proposal" />
          @error('title')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-textarea" rows="3" placeholder="Deskripsi proposal...">{{ old('description') }}</textarea>
        </div>
        <div>
          <label class="form-label">Catatan</label>
          <textarea name="notes" class="form-textarea" rows="2" placeholder="Catatan internal...">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="col-span-12 xl:col-span-4 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Status & Pengaturan</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Status <span class="text-error-500">*</span></label>
          <select name="status" class="form-select">
            @foreach(['draft'=>'Draft','internal_review'=>'Internal Review','final'=>'Final','submitted'=>'Submitted','revision'=>'Revision'] as $v=>$l)
            <option value="{{ $v }}" @selected(old('status','draft')===$v)>{{ $l }}</option>
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
          @error('backup_pic_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Nilai Penawaran (Rp)</label>
          <input type="number" name="bid_value" class="form-input" value="{{ old('bid_value') }}" min="0" placeholder="0" />
        </div>
        <div>
          <label class="form-label">Deadline</label>
          <input type="date" name="deadline" class="form-input" value="{{ old('deadline') }}" />
        </div>
        <div>
          <label class="form-label">Valid Hingga</label>
          <input type="date" name="valid_until" class="form-input" value="{{ old('valid_until') }}" />
        </div>
      </div>
    </div>
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary flex-1">Simpan Proposal</button>
      <a href="{{ route('proposals.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </div>

</div>
</form>
@endsection
