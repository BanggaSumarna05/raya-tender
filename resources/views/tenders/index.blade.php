@extends('layouts.app')
@section('title','Data Tender')
@section('page-title','Data Tender')
@section('breadcrumb')
  <span class="text-gray-400">/</span><span>Data Tender</span>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6">
  <div class="dt-wrapper">

    {{-- Header --}}
    <div class="dt-header border-b border-gray-100 dark:border-gray-800">
      <h3 class="dt-title">Data Tender
        <span class="ml-2 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
          {{ $tenders->total() }}
        </span>
      </h3>
      <div class="dt-toolbar">
        {{-- Quick Filter Presets --}}
        <div class="flex items-center gap-1.5 flex-wrap">
          @php $isMyTender = request()->boolean('my_tender'); @endphp
          <a href="{{ route('tenders.index', array_merge(request()->except('my_tender','page'), $isMyTender ? [] : ['my_tender'=>1])) }}"
            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition-colors
              {{ $isMyTender ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
            <svg class="fill-current" width="12" height="12" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C7.24 2 5 4.24 5 7C5 9.76 7.24 12 10 12C12.76 12 15 9.76 15 7C15 4.24 12.76 2 10 2ZM3 17C3 14.34 7.33 13 10 13C12.67 13 17 14.34 17 17V18H3V17Z" fill=""/></svg>
            My Tender
          </a>
          <a href="{{ route('tenders.index', ['status' => 'evaluation']) }}"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors {{ request('status')==='evaluation' ? 'bg-blue-light-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' }}">
            Evaluation
          </a>
          <a href="{{ route('tenders.index', ['status' => 'negotiation']) }}"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors {{ request('status')==='negotiation' ? 'bg-theme-purple-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' }}">
            Negotiation
          </a>
          <a href="{{ route('tenders.index', ['deadline_to' => now()->addDays(7)->format('Y-m-d')]) }}"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors bg-gray-100 text-gray-600 hover:bg-warning-100 hover:text-warning-700 dark:bg-gray-800 dark:text-gray-400">
            Deadline Minggu Ini
          </a>
        </div>
        {{-- Show entries --}}
        <form method="GET" class="dt-show" id="perpage-form">
          @foreach(request()->except('per_page','page') as $k => $v)
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
          @endforeach
          <span>Tampilkan</span>
          <select name="per_page" onchange="document.getElementById('perpage-form').submit()">
            @foreach([10,25,50,100] as $n)
            <option value="{{ $n }}" @selected(request('per_page',10)==$n)>{{ $n }}</option>
            @endforeach
          </select>
          <span>data</span>
        </form>
        {{-- Search --}}
        <form method="GET" class="flex flex-wrap items-center gap-2">
          @foreach(request()->except('search','page') as $k => $v)
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
          @endforeach
          <div class="dt-search w-full sm:w-auto">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/>
            </svg>
            <input type="text" name="search" placeholder="Cari tender..." value="{{ request('search') }}" />
          </div>
          {{-- Filter Status --}}
          <select name="status" class="max-w-[150px] rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach($statuses as $s)
            <option value="{{ $s->value }}" @selected(request('status')===$s->value)>{{ $s->label() }}</option>
            @endforeach
          </select>
          <select name="priority" class="max-w-[140px] rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Prioritas</option>
            @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $v=>$l)
            <option value="{{ $v }}" @selected(request('priority')===$v)>{{ $l }}</option>
            @endforeach
          </select>
          @if(request()->hasAny(['search','status','priority','client_id']))
          <a href="{{ route('tenders.index') }}" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Reset</a>
          @endif
        </form>
        @can('create_tenders')
        <a href="{{ route('tenders.create') }}"
          class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
          <svg class="fill-white" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.25C10.4142 3.25 10.75 3.58579 10.75 4V9.25H16C16.4142 9.25 16.75 9.58579 16.75 10C16.75 10.4142 16.4142 10.75 16 10.75H10.75V16C10.75 16.4142 10.4142 16.75 10 16.75C9.58579 16.75 9.25 16.4142 9.25 16V10.75H4C3.58579 10.75 3.25 10.4142 3.25 10C3.25 9.58579 3.58579 9.25 4 9.25H9.25V4C9.25 3.58579 9.58579 3.25 10 3.25Z" fill=""/></svg>
          Tambah Tender
        </a>
        @endcan
      </div>
    </div>

    {{-- Table --}}
    <div class="max-w-full overflow-x-auto">
      @if($tenders->isEmpty())
      <div class="dt-empty">
        <div class="dt-empty-icon"><svg class="fill-gray-400" width="28" height="28" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.50391 4.25H17.7504V16.75H9.25391C8.83969 16.75 8.50391 16.4142 8.50391 16V4.25ZM5.498 7.25H7.004V16C7.004 17.243 8.011 18.25 9.254 18.25H15.75V19C15.75 20.243 14.743 21.25 13.5 21.25H5.748C4.505 21.25 3.498 20.243 3.498 19V8.5C3.498 7.257 4.505 6.25 5.748 6.25L5.498 7.25Z" fill=""/></svg></div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada data tender</p>
        @can('create_tenders')<a href="{{ route('tenders.create') }}" class="mt-3 flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Tambah Tender</a>@endcan
      </div>
      @else
      <table class="dt-table">
        <thead>
          <tr>
            <th><div class="th-inner"><p>Kode</p></div></th>
            <x-sort-th column="title" label="Nama Tender" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Klien</p></div></th>
            <th><div class="th-inner"><p>PIC</p></div></th>
            <x-sort-th column="status" label="Status" :current="$sortField" :direction="$sortDir" />
            <x-sort-th column="priority" label="Prioritas" :current="$sortField" :direction="$sortDir" />
            <x-sort-th column="submission_deadline" label="Deadline" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Aksi</p></div></th>
          </tr>
        </thead>
        <tbody>
          @foreach($tenders as $tender)
          @php
            $days = $tender->days_until_deadline;
            $dlColor = match(true) {
              $days === null => 'text-gray-400',
              $days < 0     => 'text-error-600 font-bold',
              $days <= 3    => 'text-error-500 font-semibold',
              $days <= 7    => 'text-warning-600',
              default       => 'text-success-600',
            };
          @endphp
          <tr>
            <td><span class="font-mono text-xs text-gray-400">{{ $tender->code }}</span></td>
            <td>
              <a href="{{ route('tenders.show', $tender) }}" class="td-main hover:text-brand-500">{{ Str::limit($tender->title, 40) }}</a>
            </td>
            <td>{{ $tender->client?->name ?? '—' }}</td>
            <td>{{ $tender->pic?->name ?? '—' }}</td>
            <td><x-status-badge :status="$tender->status" /></td>
            <td><x-status-badge :status="$tender->priority" /></td>
            <td>
              @if($tender->submission_deadline)
              <span class="block">{{ $tender->submission_deadline->format('d M Y') }}</span>
              <span class="block text-xs {{ $dlColor }}">{{ $tender->deadline_label }}</span>
              @else <span class="text-gray-400">—</span> @endif
            </td>
            <td>
              <div class="dt-action">
                <a href="{{ route('tenders.show', $tender) }}" class="dt-btn-view" title="Detail">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" class="fill-current"/></svg>
                </a>
                @can('update_tenders')
                <a href="{{ route('tenders.edit', $tender) }}" class="dt-btn-edit" title="Edit">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M14.1213 2.87868C14.9024 2.09763 16.1684 2.09763 16.9497 2.87868L17.1213 3.05025C17.9024 3.8313 17.9024 5.09763 17.1213 5.87868L8.12132 14.8787C7.89419 15.1058 7.60948 15.2656 7.2981 15.3406L3.52814 16.2394C3.17306 16.3234 2.82898 16.0657 2.87106 15.7047L3.30906 11.921C3.34697 11.5946 3.51174 11.2942 3.76777 11.0782L14.1213 2.87868Z" class="fill-current"/></svg>
                </a>
                @endcan
                @can('create_tenders')
                <form action="{{ route('tenders.duplicate', $tender) }}" method="POST" class="inline" onsubmit="return confirm('Duplikasi tender ini sebagai draft baru?')">
                  @csrf
                  <button type="submit" class="dt-btn-view" title="Duplikasi" style="color:#7A5AF8;">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.75 2.5A.75.75 0 0 1 8.5 1.75h8A.75.75 0 0 1 17.25 2.5v10.75A.75.75 0 0 1 16.5 14H14v2A.75.75 0 0 1 13.25 16.75h-9.5A.75.75 0 0 1 3 16V5.25A.75.75 0 0 1 3.75 4.5H6V2.5ZM6 6H4.5v9.25h8.75V14H7.75A.75.75 0 0 1 7 13.25V6ZM8.5 3.25V12.5h7V3.25H8.5Z" class="fill-current"/></svg>
                  </button>
                </form>
                @endcan
                @can('delete_tenders')
                <button class="dt-btn-delete" title="Hapus"
                  onclick="confirmDelete('{{ route('tenders.destroy', $tender) }}','Hapus tender &quot;{{ addslashes($tender->title) }}&quot;?')">
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

    {{-- Footer --}}
    @if($tenders->isNotEmpty())
    <div class="dt-footer">
      <p class="dt-info">
        Menampilkan <span class="font-medium text-gray-700 dark:text-gray-200">{{ $tenders->firstItem() }}</span>
        sampai <span class="font-medium text-gray-700 dark:text-gray-200">{{ $tenders->lastItem() }}</span>
        dari <span class="font-medium text-gray-700 dark:text-gray-200">{{ $tenders->total() }}</span> data
      </p>
      <div class="dt-pagination">{{ $tenders->withQueryString()->links() }}</div>
    </div>
    @endif

  </div>
</div>
@endsection
