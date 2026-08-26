@extends('layouts.app')
@section('title', $document->name)
@section('page-title','Detail Dokumen')
@section('breadcrumb')
  <span class="text-gray-400">/</span><a href="{{ route('documents.index') }}" class="hover:text-brand-500">Dokumen</a>
  <span class="text-gray-400">/</span><span>{{ $document->name }}</span>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-5">
  <div class="col-span-12 xl:col-span-8 space-y-5">

    @if($document->isPreviewable())
    <div class="card overflow-hidden">
      <div class="border-b border-gray-100 dark:border-gray-800 px-5 py-3">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Preview</h3>
      </div>
      @if(in_array(strtolower($document->extension), ['jpg','jpeg','png','gif']))
      <div class="p-4 flex justify-center">
        <img src="{{ route('documents.download', $document) }}" alt="{{ $document->name }}" class="max-w-full rounded-lg" />
      </div>
      @else
      <iframe src="{{ route('documents.download', $document) }}" class="w-full h-[500px] border-0"></iframe>
      @endif
    </div>
    @endif

    <div class="card p-5">
      <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Informasi Dokumen</h3>
      <div class="grid grid-cols-2 gap-x-8 gap-y-4">
        @foreach([
          ['Nama Dokumen', $document->name],
          ['Nama File Asli', $document->original_name],
          ['Kategori', $document->category?->name],
          ['Extension', strtoupper($document->extension)],
          ['Ukuran', $document->file_size_formatted],
          ['MIME Type', $document->mime_type],
          ['Tender', $document->tender?->code],
          ['Proposal', $document->proposal?->code],
          ['Diupload Oleh', $document->uploader?->name],
          ['Tanggal Upload', $document->created_at->format('d M Y, H:i')],
        ] as [$lbl, $val])
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $lbl }}</p>
          <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $val ?? '—' }}</p>
        </div>
        @endforeach
      </div>
      @if($document->description)
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $document->description }}</p>
      </div>
      @endif
    </div>
  </div>

  <div class="col-span-12 xl:col-span-4">
    <div class="card p-5 space-y-3">
      <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Aksi</h3>
      @can('download_documents')
      <a href="{{ route('documents.download', $document) }}" class="btn btn-primary w-full">
        <svg class="fill-white" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.25C10.4142 2.25 10.75 2.58579 10.75 3V11.1893L12.9697 8.96967C13.2626 8.67678 13.7374 8.67678 14.0303 8.96967C14.3232 9.26256 14.3232 9.73744 14.0303 10.0303L10.5303 13.5303C10.2374 13.8232 9.76256 13.8232 9.46967 13.5303L5.96967 10.0303C5.67678 9.73744 5.67678 9.26256 5.96967 8.96967C6.26256 8.67678 6.73744 8.67678 7.03033 8.96967L9.25 11.1893V3C9.25 2.58579 9.58579 2.25 10 2.25ZM3.25 15C3.25 14.5858 3.58579 14.25 4 14.25H16C16.4142 14.25 16.75 14.5858 16.75 15C16.75 15.4142 16.4142 15.75 16 15.75H4C3.58579 15.75 3.25 15.4142 3.25 15Z" fill=""/></svg>
        Download
      </a>
      @endcan
      @can('delete_documents')
      <button class="btn btn-secondary w-full text-error-500"
        onclick="confirmDelete('{{ route('documents.destroy', $document) }}','Hapus dokumen ini?')">
        Hapus Dokumen
      </button>
      @endcan
      <a href="{{ route('documents.index') }}" class="btn btn-secondary w-full">Kembali</a>
    </div>
  </div>
</div>
@endsection
