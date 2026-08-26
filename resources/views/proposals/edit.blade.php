@extends('layouts.app')
@section('title','Edit Proposal')
@section('page-title','Edit Proposal')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('proposals.index') }}" class="hover:text-brand-500">Data Proposal</a>
  <span class="text-gray-400">/</span><a href="{{ route('proposals.show', $proposal) }}" class="hover:text-brand-500">{{ $proposal->code }}</a>
  <span class="text-gray-400">/</span><span>Edit</span>
@endsection

@section('content')
<form action="{{ route('proposals.update', $proposal) }}" method="POST" novalidate>
@csrf @method('PUT')
<div class="grid grid-cols-12 gap-5">

  <div class="col-span-12 xl:col-span-8 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Proposal</h3>
      <div class="space-y-4">
        {{-- Tender read-only --}}
        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
          <span class="font-medium text-gray-700 dark:text-gray-300">Tender:</span>
          {{ $proposal->tender?->code }} — {{ $proposal->tender?->title }}
        </div>
        <div>
          <label class="form-label">Judul Proposal <span class="text-error-500">*</span></label>
          <input type="text" name="title" class="form-input @error('title') border-error-300 @enderror"
            value="{{ old('title', $proposal->title) }}" />
          @error('title')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-textarea" rows="3">{{ old('description', $proposal->description) }}</textarea>
        </div>
        <div>
          <label class="form-label">Catatan</label>
          <textarea name="notes" class="form-textarea" rows="2">{{ old('notes', $proposal->notes) }}</textarea>
        </div>

        <div x-data="{ newVersion: false }" class="border-t border-gray-100 dark:border-gray-800 pt-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="new_version" value="1" x-model="newVersion"
              class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20" />
            <span class="text-sm text-gray-700 dark:text-gray-300">Simpan sebagai versi baru</span>
          </label>
          <div x-show="newVersion" x-transition class="mt-3">
            <label class="form-label">Catatan Versi</label>
            <textarea name="version_notes" class="form-textarea" rows="2" placeholder="Perubahan pada versi ini..."></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-span-12 xl:col-span-4 space-y-5">
    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Status & Pengaturan</h3>
      <div class="space-y-4">
        <div>
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            @foreach(['draft'=>'Draft','internal_review'=>'Internal Review','final'=>'Final','submitted'=>'Submitted','revision'=>'Revision','won'=>'Won','lost'=>'Lost','cancelled'=>'Cancelled'] as $v=>$l)
            <option value="{{ $v }}" @selected(old('status',$proposal->status->value)===$v)>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">PIC</label>
          <select name="pic_id" class="form-select">
            @foreach($pics as $p)<option value="{{ $p->id }}" @selected(old('pic_id',$proposal->pic_id)==$p->id)>{{ $p->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Backup PIC</label>
          <select name="backup_pic_id" class="form-select @error('backup_pic_id') border-error-300 @enderror">
            <option value="">— Tidak ada —</option>
            @foreach($pics as $p)<option value="{{ $p->id }}" @selected(old('backup_pic_id',$proposal->backup_pic_id)==$p->id)>{{ $p->name }}</option>@endforeach
          </select>
          @error('backup_pic_id')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Nilai Penawaran (Rp)</label>
          <input type="number" name="bid_value" class="form-input" value="{{ old('bid_value', $proposal->bid_value) }}" min="0" />
        </div>
        <div>
          <label class="form-label">Deadline</label>
          <input type="date" name="deadline" class="form-input" value="{{ old('deadline', $proposal->deadline?->format('Y-m-d')) }}" />
        </div>
        <div>
          <label class="form-label">Valid Hingga</label>
          <input type="date" name="valid_until" class="form-input" value="{{ old('valid_until', $proposal->valid_until?->format('Y-m-d')) }}" />
        </div>
      </div>
    </div>
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary flex-1">Simpan Perubahan</button>
      <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-secondary">Batal</a>
    </div>
  </div>

</div>
</form>
@endsection
