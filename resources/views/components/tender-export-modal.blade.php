{{--
  Komponen: tender-export-modal
  Props:
    $exportType  : 'excel' | 'pdf'
    $modalId     : ID unik modal (default: 'exportModal')
    $filters     : array filter aktif (start_date, end_date, dll.)
    $columns     : array semua kolom tersedia [ key => label ]
    $defaults    : array key kolom default
    $canFinancial: bool — apakah user boleh lihat estimated_value
--}}

@props([
    'exportType'   => 'excel',
    'modalId'      => 'exportModal',
    'filters'      => [],
    'columns'      => [],
    'defaults'     => [],
    'canFinancial' => false,
])

@php
  // Kelompokkan kolom per kategori
  $groups = [
    'Informasi Utama' => ['code', 'title', 'client', 'category', 'status', 'priority'],
    'Tim'             => ['pic', 'backup_pic'],
    'Lokasi & Sumber' => ['location', 'source'],
    'Tanggal'         => ['received_date', 'submission_deadline', 'project_start_date', 'project_end_date', 'created_at'],
    'Finansial'       => ['estimated_value'],
    'Teks Tambahan'   => ['description', 'notes'],
  ];

  // Semua status tender untuk multi-select
  $tenderStatuses = \App\Enums\TenderStatus::cases();
@endphp

<div
  x-data="{
    open: false,
    exportType: '{{ $exportType }}',
    selected: @js($defaults),

    // Status multi-select — [] berarti semua status (All)
    selectedStatuses: [],
    statusDropdownOpen: false,

    toggleAll(keys) {
      const allChecked = keys.every(k => this.selected.includes(k));
      if (allChecked) {
        this.selected = this.selected.filter(k => !keys.includes(k));
      } else {
        keys.forEach(k => { if (!this.selected.includes(k)) this.selected.push(k); });
      }
    },
    groupAllChecked(keys) {
      return keys.every(k => this.selected.includes(k));
    },
    groupSomeChecked(keys) {
      return keys.some(k => this.selected.includes(k)) && !this.groupAllChecked(keys);
    },

    toggleStatus(val) {
      if (this.selectedStatuses.includes(val)) {
        this.selectedStatuses = this.selectedStatuses.filter(s => s !== val);
      } else {
        this.selectedStatuses.push(val);
      }
    },
    statusLabel() {
      if (this.selectedStatuses.length === 0) return 'Semua Status';
      if (this.selectedStatuses.length === 1) {
        const map = @js(collect($tenderStatuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->all());
        return map[this.selectedStatuses[0]] ?? this.selectedStatuses[0];
      }
      return this.selectedStatuses.length + ' status dipilih';
    },
    statusChecked(val) {
      return this.selectedStatuses.includes(val);
    },
  }"
  id="{{ $modalId }}-root"
  @click.outside="statusDropdownOpen = false"
>
  {{-- ── TRIGGER SLOT ── --}}
  <span @click="open = true; exportType = '{{ $exportType }}'" class="inline-flex">
    {{ $slot }}
  </span>

  {{-- ── MODAL BACKDROP ── --}}
  <div
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    @keydown.escape.window="open = false; statusDropdownOpen = false"
    style="display:none"
  >
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-gray-900/50 dark:bg-gray-900/70" @click="open = false"></div>

    {{-- Modal Panel --}}
    <div
      x-show="open"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      class="relative z-10 w-full max-w-lg rounded-2xl bg-white shadow-xl dark:bg-gray-900"
      style="display:none"
    >
      {{-- Header --}}
      <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
        <div class="flex items-center gap-2.5">
          <template x-if="exportType === 'excel'">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-success-50 dark:bg-success-500/10">
              <svg class="fill-success-600" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4Zm2 2v8h10V6H5Z" fill=""/></svg>
            </span>
          </template>
          <template x-if="exportType === 'pdf'">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-error-50 dark:bg-error-500/10">
              <svg class="fill-error-500" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.414A2 2 0 0 0 17.414 6L14 2.586A2 2 0 0 0 12.586 2H4Zm7 1.5V7h3.5L11 3.5ZM5 9.75A.75.75 0 0 1 5.75 9h8.5a.75.75 0 0 1 0 1.5h-8.5A.75.75 0 0 1 5 9.75Zm.75 2.25a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5h-5.5Z" fill=""/></svg>
            </span>
          </template>
          <div>
            <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
              Pilih Kolom Export
              <span x-text="exportType === 'excel' ? '(Excel)' : '(PDF)'" class="ml-1 text-xs font-normal text-gray-400"></span>
            </h4>
            <p class="text-xs text-gray-400">Centang kolom yang ingin ditampilkan</p>
          </div>
        </div>
        <button @click="open = false" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M15 5L5 15M5 5l10 10" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
        </button>
      </div>

      {{-- Body --}}
      <div class="max-h-[60vh] overflow-y-auto px-5 py-4 space-y-4">

        {{-- Info: jumlah kolom terpilih --}}
        <div class="flex items-center justify-between">
          <span class="text-xs text-gray-400">
            <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="selected.length"></span> kolom dipilih
          </span>
          <div class="flex gap-2">
            <button
              type="button"
              @click="selected = @js(array_values(array_filter(array_keys($columns), fn($k) => $k !== 'estimated_value' || $canFinancial)))"
              class="text-xs text-brand-500 hover:text-brand-600 hover:underline"
            >Pilih Semua</button>
            <span class="text-gray-200 dark:text-gray-700">|</span>
            <button type="button" @click="selected = []; selectedStatuses = []" class="text-xs text-gray-400 hover:text-gray-600 hover:underline">Reset</button>
          </div>
        </div>

        @foreach($groups as $groupName => $groupKeys)
          @php
            $visibleKeys = array_values(array_filter($groupKeys, function($k) use ($columns, $canFinancial) {
              return isset($columns[$k]) && ($k !== 'estimated_value' || $canFinancial);
            }));
          @endphp
          @if(count($visibleKeys) === 0) @continue @endif

          <div class="rounded-xl border border-gray-100 dark:border-gray-800">
            {{-- Group header --}}
            <button
              type="button"
              class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-left"
              @click="toggleAll(@js($visibleKeys))"
            >
              <span class="relative flex h-4 w-4 shrink-0 items-center justify-center">
                <template x-if="groupSomeChecked(@js($visibleKeys))">
                  <span class="block h-4 w-4 rounded border-2 border-brand-500 bg-white dark:bg-gray-900">
                    <span class="absolute inset-0 flex items-center justify-center">
                      <span class="block h-0.5 w-2 bg-brand-500"></span>
                    </span>
                  </span>
                </template>
                <template x-if="groupAllChecked(@js($visibleKeys))">
                  <span class="flex h-4 w-4 items-center justify-center rounded border-2 border-brand-500 bg-brand-500">
                    <svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </span>
                </template>
                <template x-if="!groupAllChecked(@js($visibleKeys)) && !groupSomeChecked(@js($visibleKeys))">
                  <span class="block h-4 w-4 rounded border-2 border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-900"></span>
                </template>
              </span>
              <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $groupName }}</span>
              @if($groupName === 'Finansial')
                <span class="ml-auto rounded-full bg-warning-50 px-2 py-0.5 text-[10px] font-medium text-warning-600 dark:bg-warning-500/10">Butuh akses finansial</span>
              @endif
            </button>

            {{-- Items --}}
            <div class="divide-y divide-gray-50 dark:divide-gray-800/60 border-t border-gray-100 dark:border-gray-800">
              @foreach($visibleKeys as $key)
                <div class="flex items-center gap-3 px-3.5 py-2.5 hover:bg-gray-50 dark:hover:bg-white/[0.02]">

                  {{-- Checkbox --}}
                  <label class="flex flex-1 cursor-pointer items-center gap-3 min-w-0">
                    <span class="relative flex h-4 w-4 shrink-0">
                      <span
                        x-show="selected.includes('{{ $key }}')"
                        class="absolute inset-0 flex items-center justify-center rounded border-2 border-brand-500 bg-brand-500"
                        style="display:none"
                      >
                        <svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      </span>
                      <span
                        x-show="!selected.includes('{{ $key }}')"
                        class="absolute inset-0 rounded border-2 border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-900"
                        style="display:none"
                      ></span>
                      <input
                        type="checkbox"
                        :value="'{{ $key }}'"
                        x-model="selected"
                        class="sr-only"
                        @change="{{ $key === 'status' ? 'if (!selected.includes(\'status\')) { selectedStatuses = []; statusDropdownOpen = false; }' : '' }}"
                      />
                    </span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $columns[$key] }}</span>
                  </label>

                  @if($key === 'status')
                  {{-- ── STATUS MULTI-SELECT DROPDOWN ── --}}
                  <div
                    class="relative shrink-0"
                    x-show="selected.includes('status')"
                    style="display:none"
                    @click.stop
                  >
                    {{-- Trigger button --}}
                    <button
                      type="button"
                      @click="statusDropdownOpen = !statusDropdownOpen"
                      class="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 max-w-[160px]"
                    >
                      <span class="truncate" x-text="statusLabel()"></span>
                      <svg class="shrink-0 fill-gray-400 transition-transform" :class="statusDropdownOpen ? 'rotate-180' : ''" width="10" height="10" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.22 6.97a.75.75 0 0 1 1.06 0L10 11.69l4.72-4.72a.75.75 0 1 1 1.06 1.06l-5.25 5.25a.75.75 0 0 1-1.06 0L4.22 8.03a.75.75 0 0 1 0-1.06Z" fill=""/></svg>
                    </button>

                    {{-- Dropdown panel --}}
                    <div
                      x-show="statusDropdownOpen"
                      x-transition:enter="transition ease-out duration-100"
                      x-transition:enter-start="opacity-0 scale-95"
                      x-transition:enter-end="opacity-100 scale-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100 scale-100"
                      x-transition:leave-end="opacity-0 scale-95"
                      @click.outside="statusDropdownOpen = false"
                      class="absolute right-0 z-30 mt-1 w-52 rounded-xl border border-gray-100 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800"
                      style="display:none"
                    >
                      {{-- All option --}}
                      <button
                        type="button"
                        @click="selectedStatuses = []"
                        class="flex w-full items-center gap-2.5 px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700"
                        :class="selectedStatuses.length === 0 ? 'text-brand-600 font-medium' : 'text-gray-700 dark:text-gray-200'"
                      >
                        <span class="relative flex h-4 w-4 shrink-0">
                          <span
                            x-show="selectedStatuses.length === 0"
                            class="absolute inset-0 flex items-center justify-center rounded border-2 border-brand-500 bg-brand-500"
                            style="display:none"
                          >
                            <svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                          </span>
                          <span
                            x-show="selectedStatuses.length !== 0"
                            class="absolute inset-0 rounded border-2 border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-900"
                            style="display:none"
                          ></span>
                        </span>
                        <span class="text-sm">Semua Status</span>
                      </button>

                      <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                      {{-- Status items --}}
                      @foreach($tenderStatuses as $status)
                      <button
                        type="button"
                        @click="toggleStatus('{{ $status->value }}')"
                        class="flex w-full items-center gap-2.5 px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700"
                      >
                        <span class="relative flex h-4 w-4 shrink-0">
                          <span
                            x-show="statusChecked('{{ $status->value }}')"
                            class="absolute inset-0 flex items-center justify-center rounded border-2 border-brand-500 bg-brand-500"
                            style="display:none"
                          >
                            <svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                          </span>
                          <span
                            x-show="!statusChecked('{{ $status->value }}')"
                            class="absolute inset-0 rounded border-2 border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-900"
                            style="display:none"
                          ></span>
                        </span>
                        <span class="text-sm text-gray-700 dark:text-gray-200">{{ $status->label() }}</span>
                      </button>
                      @endforeach

                      {{-- Footer: clear --}}
                      @if(count($tenderStatuses) > 0)
                      <div class="mt-1 border-t border-gray-100 px-3 py-2 dark:border-gray-700">
                        <button
                          type="button"
                          @click="selectedStatuses = []"
                          class="text-xs text-gray-400 hover:text-gray-600 hover:underline"
                        >Hapus pilihan</button>
                      </div>
                      @endif
                    </div>
                  </div>
                  @endif

                </div>
              @endforeach
            </div>
          </div>
        @endforeach

      </div>

      {{-- Footer --}}
      <div class="flex items-center justify-between border-t border-gray-100 px-5 py-4 dark:border-gray-800">
        <p class="text-xs text-gray-400">Kolom "No." selalu disertakan otomatis</p>
        <div class="flex gap-2">
          <button
            type="button"
            @click="open = false; statusDropdownOpen = false"
            class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
          >Batal</button>

          <form method="POST" action="{{ route('reports.export.tender-columns') }}" id="{{ $modalId }}-form">
            @csrf
            <input type="hidden" name="export_type" :value="exportType">
            @foreach($filters as $k => $v)
              @if($v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endif
            @endforeach
            <div id="{{ $modalId }}-cols"></div>

            <button
              type="button"
              @click="
                const form = document.getElementById('{{ $modalId }}-form');
                const container = document.getElementById('{{ $modalId }}-cols');
                container.innerHTML = '';

                // Inject selected columns
                selected.forEach(col => {
                  const inp = document.createElement('input');
                  inp.type = 'hidden';
                  inp.name = 'columns[]';
                  inp.value = col;
                  container.appendChild(inp);
                });

                // Inject selected statuses (hanya jika kolom status diceklis)
                if (selected.includes('status') && selectedStatuses.length > 0) {
                  selectedStatuses.forEach(s => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'export_statuses[]';
                    inp.value = s;
                    container.appendChild(inp);
                  });
                }

                form.submit();
              "
              :disabled="selected.length === 0"
              class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50"
            >
              <template x-if="exportType === 'excel'">
                <svg class="fill-white" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.25C10.4142 2.25 10.75 2.58579 10.75 3V11.1893L12.9697 8.96967C13.2626 8.67678 13.7374 8.67678 14.0303 8.96967C14.3232 9.26256 14.3232 9.73744 14.0303 10.0303L10.5303 13.5303C10.2374 13.8232 9.76256 13.8232 9.46967 13.5303L5.96967 10.0303C5.67678 9.73744 5.67678 9.26256 5.96967 8.96967C6.26256 8.67678 6.73744 8.67678 7.03033 8.96967L9.25 11.1893V3C9.25 2.58579 9.58579 2.25 10 2.25ZM3.25 15C3.25 14.5858 3.58579 14.25 4 14.25H16C16.4142 14.25 16.75 14.5858 16.75 15C16.75 15.4142 16.4142 15.75 16 15.75H4C3.58579 15.75 3.25 15.4142 3.25 15Z" fill=""/></svg>
              </template>
              <template x-if="exportType === 'pdf'">
                <svg class="fill-white" width="14" height="14" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.414A2 2 0 0 0 17.414 6L14 2.586A2 2 0 0 0 12.586 2H4Zm7 1.5V7h3.5L11 3.5Z" fill=""/></svg>
              </template>
              Export
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
