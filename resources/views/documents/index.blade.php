@extends('layouts.app')
@section('title','Dokumen')
@section('page-title','Dokumen')
@section('breadcrumb')
  <span class="text-gray-400">/</span><span>Dokumen</span>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6">
  <div class="dt-wrapper">

    <div class="dt-header border-b border-gray-100 dark:border-gray-800">
      <h3 class="dt-title">Dokumen
        <span class="ml-2 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $documents->total() }}</span>
      </h3>
      <div class="dt-toolbar">
        <form method="GET" class="dt-show" id="pp-doc">
          @foreach(request()->except('per_page','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <span>Tampilkan</span>
          <select name="per_page" onchange="document.getElementById('pp-doc').submit()">
            @foreach([15,25,50,100] as $n)<option value="{{ $n }}" @selected(request('per_page',15)==$n)>{{ $n }}</option>@endforeach
          </select>
          <span>data</span>
        </form>
        <form method="GET" class="flex flex-wrap items-center gap-2">
          @foreach(request()->except('search','tender_id','proposal_id','category_id','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <div class="dt-search w-full sm:w-auto">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/></svg>
            <input type="text" name="search" placeholder="Cari dokumen..." value="{{ request('search') }}" />
          </div>
          <select name="tender_id" class="max-w-[180px] rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Tender</option>
            @foreach($tenders as $t)<option value="{{ $t->id }}" @selected(request('tender_id')==$t->id)>{{ $t->code }} — {{ Str::limit($t->title,25) }}</option>@endforeach
          </select>
          <select name="category_id" class="max-w-[150px] rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>@endforeach
          </select>
          @if(request()->hasAny(['search','tender_id','proposal_id','category_id']))
          <a href="{{ route('documents.index') }}" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Reset</a>
          @endif
        </form>
        @can('upload_documents')
        <a href="{{ route('documents.create') }}" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
          <svg class="fill-white" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.25C10.4142 2.25 10.75 2.58579 10.75 3V8.25H16C16.4142 8.25 16.75 8.58579 16.75 9C16.75 9.41421 16.4142 9.75 16 9.75H10.75V15C10.75 15.4142 10.4142 15.75 10 15.75C9.58579 15.75 9.25 15.4142 9.25 15V9.75H4C3.58579 9.75 3.25 9.41421 3.25 9C3.25 8.58579 3.58579 8.25 4 8.25H9.25V3C9.25 2.58579 9.58579 2.25 10 2.25Z" fill=""/></svg>
          Upload Dokumen
        </a>
        @endcan
      </div>
    </div>

    <div class="max-w-full overflow-x-auto">
      @if($documents->isEmpty())
      <div class="dt-empty">
        <div class="dt-empty-icon"><svg class="fill-gray-400" width="28" height="28" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 6C3.25 4.75736 4.25736 3.75 5.5 3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V6ZM19.25 8.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25ZM4.75 9.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75H4.75Z" fill=""/></svg></div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada dokumen</p>
        @can('upload_documents')<a href="{{ route('documents.create') }}" class="mt-3 flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Upload Dokumen</a>@endcan
      </div>
      @else
      <table class="dt-table min-w-[780px]">
        <thead>
          <tr>
            <x-sort-th column="name" label="Nama Dokumen" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Kategori</p></div></th>
            <th><div class="th-inner"><p>Tender</p></div></th>
            <x-sort-th column="extension" label="Ext" :current="$sortField" :direction="$sortDir" />
            <x-sort-th column="file_size" label="Ukuran" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Diupload</p></div></th>
            <x-sort-th column="created_at" label="Tanggal" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Aksi</p></div></th>
          </tr>
        </thead>
        <tbody>
          @foreach($documents as $doc)
          <tr>
            <td>
              <span class="td-main block">{{ $doc->name }}</span>
              <span class="td-sub block">{{ $doc->original_name }}</span>
            </td>
            <td>
              <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                {{ $doc->category?->name ?? '—' }}
              </span>
            </td>
            <td><span class="font-mono text-xs text-gray-400">{{ $doc->tender?->code ?? '—' }}</span></td>
            <td>
              <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium uppercase text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $doc->extension }}</span>
            </td>
            <td>{{ $doc->file_size_formatted }}</td>
            <td>{{ $doc->uploader?->name ?? '—' }}</td>
            <td>{{ $doc->created_at->format('d M Y') }}</td>
            <td>
              <div class="dt-action">
                <a href="{{ route('documents.show', $doc) }}" class="dt-btn-view" title="Detail">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" class="fill-current"/></svg>
                </a>
                @can('download_documents')
                <a href="{{ route('documents.download', $doc) }}" class="dt-btn-edit" title="Download">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.25C10.4142 2.25 10.75 2.58579 10.75 3V11.1893L12.9697 8.96967C13.2626 8.67678 13.7374 8.67678 14.0303 8.96967C14.3232 9.26256 14.3232 9.73744 14.0303 10.0303L10.5303 13.5303C10.2374 13.8232 9.76256 13.8232 9.46967 13.5303L5.96967 10.0303C5.67678 9.73744 5.67678 9.26256 5.96967 8.96967C6.26256 8.67678 6.73744 8.67678 7.03033 8.96967L9.25 11.1893V3C9.25 2.58579 9.58579 2.25 10 2.25Z" class="fill-current"/></svg>
                </a>
                @endcan
                @can('delete_documents')
                <button class="dt-btn-delete" title="Hapus"
                  onclick="confirmDelete('{{ route('documents.destroy', $doc) }}','Hapus dokumen &quot;{{ addslashes($doc->name) }}&quot;?')">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.75H11C11.4142 3.75 11.75 4.08579 11.75 4.5V5.25H13C13.4142 5.25 13.75 5.58579 13.75 6C13.75 6.41421 13.4142 6.75 13 6.75H7C6.58579 6.75 6.25 6.41421 6.25 6C6.25 5.58579 6.58579 5.25 7 5.25H8.25V4.5C8.25 4.08579 8.58579 3.75 9 3.75ZM6.75 8.25H13.25L12.74 16.25C12.7 16.89 12.17 17.39 11.53 17.39H8.47C7.83 17.39 7.3 16.89 7.26 16.25L6.75 8.25Z" class="fill-current"/></svg>
                </button>
                @endcan
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>

    @if($documents->isNotEmpty())
    <div class="dt-footer">
      <p class="dt-info">Menampilkan <span class="font-medium text-gray-700 dark:text-gray-200">{{ $documents->firstItem() }}</span> sampai <span class="font-medium text-gray-700 dark:text-gray-200">{{ $documents->lastItem() }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-200">{{ $documents->total() }}</span> data</p>
      <div class="dt-pagination">{{ $documents->withQueryString()->links() }}</div>
    </div>
    @endif

  </div>
</div>
@endsection
