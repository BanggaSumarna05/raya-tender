@extends('layouts.app')
@section('title','Data Proposal')
@section('page-title','Data Proposal')
@section('breadcrumb')
  <span class="text-gray-400">/</span><span>Data Proposal</span>
@endsection

@section('content')
<div class="space-y-5 sm:space-y-6">
  <div class="dt-wrapper">

    <div class="dt-header border-b border-gray-100 dark:border-gray-800">
      <h3 class="dt-title">Data Proposal
        <span class="ml-2 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $proposals->total() }}</span>
      </h3>
      <div class="dt-toolbar">
        {{-- Quick Filter Presets --}}
        <div class="flex items-center gap-1.5 flex-wrap">
          @php $isMyProposal = request()->boolean('my_proposal'); @endphp
          <a href="{{ route('proposals.index', array_merge(request()->except('my_proposal','page'), $isMyProposal ? [] : ['my_proposal'=>1])) }}"
            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition-colors
              {{ $isMyProposal ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
            <svg class="fill-current" width="12" height="12" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C7.24 2 5 4.24 5 7C5 9.76 7.24 12 10 12C12.76 12 15 9.76 15 7C15 4.24 12.76 2 10 2ZM3 17C3 14.34 7.33 13 10 13C12.67 13 17 14.34 17 17V18H3V17Z" fill=""/></svg>
            My Proposal
          </a>
          <a href="{{ route('proposals.index', ['status' => 'internal_review']) }}"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors {{ request('status')==='internal_review' ? 'bg-warning-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' }}">
            Review
          </a>
          <a href="{{ route('proposals.index', ['status' => 'submitted']) }}"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors {{ request('status')==='submitted' ? 'bg-gray-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' }}">
            Submitted
          </a>
        </div>
        <form method="GET" class="dt-show" id="pp-form">
          @foreach(request()->except('per_page','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <span>Tampilkan</span>
          <select name="per_page" onchange="document.getElementById('pp-form').submit()">
            @foreach([10,25,50,100] as $n)<option value="{{ $n }}" @selected(request('per_page',10)==$n)>{{ $n }}</option>@endforeach
          </select>
          <span>data</span>
        </form>
        <form method="GET" class="flex flex-wrap items-center gap-2">
          @foreach(request()->except('search','status','pic_id','page') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
          <div class="dt-search w-full sm:w-auto">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/></svg>
            <input type="text" name="search" placeholder="Cari proposal..." value="{{ request('search') }}" />
          </div>
          <select name="status" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach(['draft'=>'Draft','internal_review'=>'Internal Review','final'=>'Final','submitted'=>'Submitted','revision'=>'Revision','won'=>'Won','lost'=>'Lost','cancelled'=>'Cancelled'] as $v=>$l)
            <option value="{{ $v }}" @selected(request('status')===$v)>{{ $l }}</option>
            @endforeach
          </select>
          <select name="pic_id" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" onchange="this.form.submit()">
            <option value="">Semua PIC</option>
            @foreach($pics as $p)<option value="{{ $p->id }}" @selected(request('pic_id')==$p->id)>{{ $p->name }}</option>@endforeach
          </select>
          @if(request()->hasAny(['search','status','pic_id']))
          <a href="{{ route('proposals.index') }}" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Reset</a>
          @endif
        </form>
        @can('create_proposals')
        <a href="{{ route('proposals.create') }}" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
          <svg class="fill-white" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.25C10.4142 3.25 10.75 3.58579 10.75 4V9.25H16C16.4142 9.25 16.75 9.58579 16.75 10C16.75 10.4142 16.4142 10.75 16 10.75H10.75V16C10.75 16.4142 10.4142 16.75 10 16.75C9.58579 16.75 9.25 16.4142 9.25 16V10.75H4C3.58579 10.75 3.25 10.4142 3.25 10C3.25 9.58579 3.58579 9.25 4 9.25H9.25V4C9.25 3.58579 9.58579 3.25 10 3.25Z" fill=""/></svg>
          Tambah Proposal
        </a>
        @endcan
      </div>
    </div>

    <div class="max-w-full overflow-x-auto">
      @if($proposals->isEmpty())
      <div class="dt-empty">
        <div class="dt-empty-icon"><svg class="fill-gray-400" width="28" height="28" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25H18.5C19.74 3.25 20.75 4.26 20.75 5.5V18.5C20.75 19.74 19.74 20.75 18.5 20.75H5.5C4.26 20.75 3.25 19.74 3.25 18.5V5.5C3.25 4.26 4.26 3.25 5.5 3.25ZM7 9.71H17V8.21H7V9.71ZM7 14.29H14V12.79H7V14.29Z" fill=""/></svg></div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada data proposal</p>
        @can('create_proposals')<a href="{{ route('proposals.create') }}" class="mt-3 flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Tambah Proposal</a>@endcan
      </div>
      @else
      <table class="dt-table">
        <thead>
          <tr>
            <th><div class="th-inner"><p>Kode</p></div></th>
            <x-sort-th column="title" label="Judul Proposal" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Tender</p></div></th>
            <th><div class="th-inner"><p>PIC</p></div></th>
            <x-sort-th column="status" label="Status" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Versi</p></div></th>
            <x-sort-th column="deadline" label="Deadline" :current="$sortField" :direction="$sortDir" />
            <th><div class="th-inner"><p>Aksi</p></div></th>
          </tr>
        </thead>
        <tbody>
          @foreach($proposals as $p)
          <tr>
            <td><span class="font-mono text-xs text-gray-400">{{ $p->code }}</span></td>
            <td>
              <a href="{{ route('proposals.show', $p) }}" class="td-main hover:text-brand-500">{{ Str::limit($p->title, 38) }}</a>
            </td>
            <td><span class="font-mono text-xs text-gray-400">{{ $p->tender?->code ?? '—' }}</span></td>
            <td>{{ $p->pic?->name ?? '—' }}</td>
            <td><x-status-badge :status="$p->status" /></td>
            {{-- VERSION COLUMN — versi terkini + popover riwayat revisi --}}
            <td>
              @php $sortedVersions = $p->versions->sortByDesc('version_number'); @endphp
              <div
                x-data="{ open: false }"
                class="relative inline-block"
                @mouseenter="open = true"
                @mouseleave="open = false"
              >
                {{-- Badge versi terkini --}}
                <button
                  @click="open = !open"
                  class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-600 hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20 transition-colors cursor-pointer"
                >
                  V{{ $p->current_version }}
                  @if($p->versions->count() > 1)
                  <svg class="fill-current opacity-60" width="10" height="10" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" fill=""/>
                  </svg>
                  @endif
                </button>

                {{-- Popover riwayat revisi --}}
                @if($p->versions->count() > 0)
                <div
                  x-show="open"
                  x-transition:enter="transition ease-out duration-100"
                  x-transition:enter-start="opacity-0 scale-95"
                  x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-75"
                  x-transition:leave-start="opacity-100 scale-100"
                  x-transition:leave-end="opacity-0 scale-95"
                  @click.outside="open = false"
                  class="absolute left-0 top-full z-50 mt-1.5 w-[min(256px,calc(100vw-2rem))] rounded-xl border border-gray-200 bg-white shadow-theme-lg dark:border-gray-700 dark:bg-gray-900"
                >
                  <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-3 py-2">
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Riwayat Revisi</span>
                    <span class="badge badge-gray">{{ $p->versions->count() }} versi</span>
                  </div>
                  <ul class="max-h-52 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($sortedVersions as $v)
                    @php $isCurrentV = $v->version_number === $p->current_version; @endphp
                    <li>
                      <a
                        href="{{ route('proposals.versions.show', [$p, $v]) }}"
                        class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors"
                      >
                        {{-- Version circle --}}
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold
                          {{ $isCurrentV ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                          V{{ $v->version_number }}
                        </span>
                        {{-- Version detail --}}
                        <div class="flex-1 min-w-0">
                          <div class="flex items-center gap-1.5">
                            <span class="text-xs font-medium {{ $isCurrentV ? 'text-brand-600 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300' }}">
                              {{ $v->revision_label }}
                            </span>
                            @if($isCurrentV)
                            <span class="inline-block rounded-full bg-brand-100 px-1.5 py-0.5 text-[10px] font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">Current</span>
                            @endif
                          </div>
                          @if($v->change_notes)
                          <p class="truncate text-[11px] text-gray-400">{{ $v->change_notes }}</p>
                          @endif
                          <p class="text-[11px] text-gray-400">{{ $v->created_at->format('d M Y') }}</p>
                        </div>
                        {{-- Arrow --}}
                        <svg class="fill-gray-300 shrink-0" width="12" height="12" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.17 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" fill=""/>
                        </svg>
                      </a>
                    </li>
                    @endforeach
                  </ul>
                </div>
                @endif
              </div>
            </td>
            <td>{{ $p->deadline?->format('d M Y') ?? '—' }}</td>
            <td>
              <div class="dt-action">
                <a href="{{ route('proposals.show', $p) }}" class="dt-btn-view" title="Detail">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619Z" class="fill-current"/></svg>
                </a>
                @can('update_proposals')
                <a href="{{ route('proposals.edit', $p) }}" class="dt-btn-edit" title="Edit">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M14.1213 2.87868C14.9024 2.09763 16.1684 2.09763 16.9497 2.87868L17.1213 3.05025C17.9024 3.8313 17.9024 5.09763 17.1213 5.87868L8.12132 14.8787C7.89419 15.1058 7.60948 15.2656 7.2981 15.3406L3.52814 16.2394C3.17306 16.3234 2.82898 16.0657 2.87106 15.7047L3.30906 11.921C3.34697 11.5946 3.51174 11.2942 3.76777 11.0782L14.1213 2.87868Z" class="fill-current"/></svg>
                </a>
                @endcan
                @can('create_proposals')
                <form action="{{ route('proposals.duplicate', $p) }}" method="POST" class="inline" onsubmit="return confirm('Duplikasi proposal ini sebagai proposal baru?')">
                  @csrf
                  <button type="submit" class="dt-btn-view" title="Duplikasi" style="color:#7A5AF8;">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.75 2.5A.75.75 0 0 1 8.5 1.75h8A.75.75 0 0 1 17.25 2.5v10.75A.75.75 0 0 1 16.5 14H14v2A.75.75 0 0 1 13.25 16.75h-9.5A.75.75 0 0 1 3 16V5.25A.75.75 0 0 1 3.75 4.5H6V2.5ZM6 6H4.5v9.25h8.75V14H7.75A.75.75 0 0 1 7 13.25V6ZM8.5 3.25V12.5h7V3.25H8.5Z" class="fill-current"/></svg>
                  </button>
                </form>
                @endcan
                @can('delete_proposals')
                <button class="dt-btn-delete" title="Hapus"
                  onclick="confirmDelete('{{ route('proposals.destroy', $p) }}','Hapus proposal ini?')">
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

    @if($proposals->isNotEmpty())
    <div class="dt-footer">
      <p class="dt-info">Menampilkan <span class="font-medium text-gray-700 dark:text-gray-200">{{ $proposals->firstItem() }}</span> sampai <span class="font-medium text-gray-700 dark:text-gray-200">{{ $proposals->lastItem() }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-200">{{ $proposals->total() }}</span> data</p>
      <div class="dt-pagination">{{ $proposals->withQueryString()->links() }}</div>
    </div>
    @endif

  </div>
</div>
@endsection
