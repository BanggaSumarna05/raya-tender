@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')

{{-- ===== LOGIN REMINDER MODAL ===== --}}
@if(session()->pull('show_login_reminder'))
<div
  id="loginReminderModal"
  x-data="{
    show: true,
    loading: true,
    items: [],
    countdown: 15,
    timer: null,

    async init() {
      try {
        const res = await fetch('{{ route('notifications.index') }}', {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
          },
          credentials: 'same-origin',
        });
        const json = await res.json();
        this.items = json.notifications || [];
      } catch(e) {
        this.items = [];
      } finally {
        this.loading = false;
      }

      if (this.items.length === 0) {
        this.show = false;
        return;
      }

      this.timer = setInterval(() => {
        this.countdown--;
        if (this.countdown <= 0) {
          clearInterval(this.timer);
          this.show = false;
        }
      }, 1000);
    },

    close() {
      clearInterval(this.timer);
      this.show = false;
    }
  }"
  x-show="show"
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 z-[999999] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm"
  @keydown.escape.window="close()"
>
  <div
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
    class="w-full max-w-md rounded-2xl border border-gray-200 bg-white shadow-theme-xl dark:border-gray-800 dark:bg-gray-900"
    @click.stop
  >
    {{-- Header --}}
    <div class="flex items-start justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-warning-50 dark:bg-warning-500/10">
          <svg class="fill-warning-500" width="20" height="20" viewBox="0 0 24 24">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM12 6.25C12.4142 6.25 12.75 6.58579 12.75 7V11.6893L15.5303 14.4697C15.8232 14.7626 15.8232 15.2374 15.5303 15.5303C15.2374 15.8232 14.7626 15.8232 14.4697 15.5303L11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12V7C11.25 6.58579 11.5858 6.25 12 6.25Z" fill=""/>
          </svg>
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Reminder Deadline</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">Halo, {{ auth()->user()->name }}! Ada yang perlu diperhatikan.</p>
        </div>
      </div>
      <button @click="close()" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300 transition-colors">
        <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill=""/>
        </svg>
      </button>
    </div>

    {{-- Body --}}
    <div class="px-6 py-4">

      {{-- Loading --}}
      <div x-show="loading" class="flex items-center justify-center py-8">
        <svg class="animate-spin text-brand-500" width="28" height="28" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
      </div>

      {{-- Notification list --}}
      <div x-show="!loading" class="max-h-72 overflow-y-auto space-y-2 custom-scrollbar">
        <template x-for="item in items" :key="item.url + item.type">
          <a
            :href="item.url"
            @click="close()"
            class="flex items-start gap-3 rounded-xl border p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/60"
            :class="item.type === 'overdue'
              ? 'border-error-200 bg-error-50/60 dark:border-error-800/40 dark:bg-error-500/5'
              : 'border-warning-200 bg-warning-50/60 dark:border-warning-800/40 dark:bg-warning-500/5'"
          >
            {{-- Icon --}}
            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
              :class="item.type === 'overdue' ? 'bg-error-100 dark:bg-error-500/20' : 'bg-warning-100 dark:bg-warning-500/20'">
              <template x-if="item.type === 'overdue'">
                <svg class="fill-error-500" width="14" height="14" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM11 14C11 14.5523 10.5523 15 10 15C9.44772 15 9 14.5523 9 14C9 13.4477 9.44772 13 10 13C10.5523 13 11 13.4477 11 14ZM9 6C9 5.44772 9.44772 5 10 5C10.5523 5 11 5.44772 11 6V11C11 11.5523 10.5523 12 10 12C9.44772 12 9 11.5523 9 11V6Z" fill=""/>
                </svg>
              </template>
              <template x-if="item.type === 'upcoming'">
                <svg class="fill-warning-500" width="14" height="14" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM10.75 6C10.75 5.58579 10.4142 5.25 10 5.25C9.58579 5.25 9.25 5.58579 9.25 6V10C9.25 10.2122 9.33902 10.4153 9.4948 10.5607L11.9948 12.8107C12.3009 13.0963 12.7754 13.0748 13.0607 12.7687C13.3461 12.4627 13.3246 11.9881 13.0185 11.7028L10.75 9.67085V6Z" fill=""/>
                </svg>
              </template>
            </div>

            {{-- Content --}}
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2">
                <span class="text-xs font-semibold"
                  :class="item.type === 'overdue' ? 'text-error-600 dark:text-error-400' : 'text-warning-600 dark:text-warning-400'"
                  x-text="item.type === 'overdue' ? 'OVERDUE' : 'UPCOMING'"></span>
                <span class="font-mono text-[10px] text-gray-400" x-text="item.code"></span>
              </div>
              <p class="truncate text-sm font-medium text-gray-800 dark:text-white/90" x-text="item.title"></p>
              <div class="mt-0.5 flex items-center gap-2">
                <svg class="shrink-0 fill-gray-400" width="11" height="11" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM10.75 6C10.75 5.58579 10.4142 5.25 10 5.25C9.58579 5.25 9.25 5.58579 9.25 6V10C9.25 10.2122 9.33902 10.4153 9.4948 10.5607L11.9948 12.8107C12.3009 13.0963 12.7754 13.0748 13.0607 12.7687C13.3461 12.4627 13.3246 11.9881 13.0185 11.7028L10.75 9.67085V6Z" fill=""/></svg>
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="item.deadline"></span>
                <span class="text-xs font-semibold"
                  :class="item.type === 'overdue' ? 'text-error-500' : 'text-warning-600'"
                  x-text="item.days"></span>
              </div>
            </div>

            {{-- Arrow --}}
            <svg class="mt-1 shrink-0 fill-gray-400" width="14" height="14" viewBox="0 0 20 20">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M7.29289 4.29289C7.68342 3.90237 8.31658 3.90237 8.70711 4.29289L14.7071 10.2929C15.0976 10.6834 15.0976 11.3166 14.7071 11.7071L8.70711 17.7071C8.31658 18.0976 7.68342 18.0976 7.29289 17.7071C6.90237 17.3166 6.90237 16.6834 7.29289 16.2929L12.5858 11L7.29289 5.70711C6.90237 5.31658 6.90237 4.68342 7.29289 4.29289Z" fill=""/>
            </svg>
          </a>
        </template>
      </div>
    </div>

    {{-- Footer --}}
    <div x-show="!loading" class="flex items-center justify-between border-t border-gray-100 px-6 py-3 dark:border-gray-800">
      <p class="text-xs text-gray-400">
        Menutup otomatis dalam <span class="font-semibold text-gray-600 dark:text-gray-300" x-text="countdown"></span> detik
      </p>
      <button
        @click="close()"
        class="rounded-lg bg-brand-500 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-600 transition-colors"
      >
        Mengerti
      </button>
    </div>
  </div>
</div>
@endif
{{-- ===== END LOGIN REMINDER MODAL ===== --}}

{{-- Welcome --}}
<div class="mb-6">
  <h2 class="text-title-sm font-bold text-gray-800 dark:text-white/90">Selamat Datang, {{ auth()->user()->name }} 👋</h2>
  <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- Overdue alert --}}
@if($stats['overdue_tenders'] > 0)
<div class="mb-6 flex items-center gap-3 rounded-xl border border-error-200 bg-error-50 p-4 text-error-700 dark:border-error-700/30 dark:bg-error-500/10 dark:text-error-400">
  <svg class="shrink-0 fill-error-500" width="18" height="18" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM11 14C11 14.5523 10.5523 15 10 15C9.44772 15 9 14.5523 9 14C9 13.4477 9.44772 13 10 13C10.5523 13 11 13.4477 11 14ZM9 6C9 5.44772 9.44772 5 10 5C10.5523 5 11 5.44772 11 6V11C11 11.5523 10.5523 12 10 12C9.44772 12 9 11.5523 9 11V6Z" fill=""/></svg>
  <span class="text-sm font-medium"><strong>{{ $stats['overdue_tenders'] }} tender</strong> melewati deadline.</span>
  <a href="{{ route('tenders.index') }}" class="ml-auto rounded-lg bg-error-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-error-600">Lihat Tender</a>
</div>
@endif

{{-- KPI Grid — exact metric-group structure from --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 md:gap-6 mb-6">

  @php
    $metrics = [
      ['label'=>'Total Tender',   'value'=>number_format($stats['total_tenders']),   'trend'=>null,   'color'=>'fill-gray-800 dark:fill-white/90', 'bg'=>'bg-gray-100 dark:bg-gray-800',
       'icon'=>'M8.50391 4.25H17.7504C18.1646 4.25 18.5004 4.58579 18.5004 5V16.75H9.25391C8.83969 16.75 8.50391 16.4142 8.50391 16V4.25ZM7.00391 4.99854V4.25C7.00391 3.00736 8.01127 2 9.25391 2H15.2777C15.8745 2 16.4468 2.23705 16.8687 2.659L19.3414 5.13168C19.7634 5.55364 20.0004 6.12594 20.0004 6.72268V16.75C20.0004 17.9926 18.9931 19 17.7504 19H16.248L16.248 19.75C16.248 20.9926 15.2407 22 13.998 22H6.24805C5.00541 22 3.99805 20.9926 3.99805 19.75V7.24854C3.99805 6.00589 5.00541 4.99854 6.24805 4.99854H7.00391Z'],
      ['label'=>'Tender Pipeline', 'value'=>number_format($stats['active_tenders']),  'trend'=>null,   'color'=>'fill-gray-800 dark:fill-white/90', 'bg'=>'bg-gray-100 dark:bg-gray-800',
       'icon'=>'M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 16.6944 7.30558 20.5 12 20.5C16.6944 20.5 20.5 16.6944 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM12 6.25C12.4142 6.25 12.75 6.58579 12.75 7V11.6893L15.5303 14.4697C15.8232 14.7626 15.8232 15.2374 15.5303 15.5303C15.2374 15.8232 14.7626 15.8232 14.4697 15.5303L11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12V7C11.25 6.58579 11.5858 6.25 12 6.25Z'],
      ['label'=>'Tender Menang',  'value'=>number_format($stats['won_tenders']),     'trend'=>'up',   'color'=>'fill-gray-800 dark:fill-white/90', 'bg'=>'bg-gray-100 dark:bg-gray-800',
       'icon'=>'M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM13.7071 8.20711C14.0976 7.81658 14.0976 7.18342 13.7071 6.79289C13.3166 6.40237 12.6834 6.40237 12.2929 6.79289L9 10.0858L7.70711 8.79289C7.31658 8.40237 6.68342 8.40237 6.29289 8.79289C5.90237 9.18342 5.90237 9.81658 6.29289 10.2071L8.29289 12.2071C8.68342 12.5976 9.31658 12.5976 9.70711 12.2071L13.7071 8.20711Z'],
      ['label'=>'Tender Kalah',   'value'=>number_format($stats['lost_tenders']),    'trend'=>'down', 'color'=>'fill-gray-800 dark:fill-white/90', 'bg'=>'bg-gray-100 dark:bg-gray-800',
       'icon'=>'M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM13.2803 7.21967C13.5732 7.51256 13.5732 7.98744 13.2803 8.28033L11.0607 10.5L13.2803 12.7197C13.5732 13.0126 13.5732 13.4874 13.2803 13.7803C12.9874 14.0732 12.5126 14.0732 12.2197 13.7803L10 11.5607L7.78033 13.7803C7.48744 14.0732 7.01256 14.0732 6.71967 13.7803C6.42678 13.4874 6.42678 13.0126 6.71967 12.7197L8.93934 10.5L6.71967 8.28033C6.42678 7.98744 6.42678 7.51256 6.71967 7.21967C7.01256 6.92678 7.48744 6.92678 7.78033 7.21967L10 9.43934L12.2197 7.21967C12.5126 6.92678 12.9874 6.92678 13.2803 7.21967Z'],
      ['label'=>'Proposal Aktif', 'value'=>number_format($stats['active_proposals']),'trend'=>'up',   'color'=>'fill-gray-800 dark:fill-white/90', 'bg'=>'bg-gray-100 dark:bg-gray-800',
       'icon'=>'M5.5 3.25H18.5C19.74 3.25 20.75 4.26 20.75 5.5V18.5C20.75 19.74 19.74 20.75 18.5 20.75H5.5C4.26 20.75 3.25 19.74 3.25 18.5V5.5C3.25 4.26 4.26 3.25 5.5 3.25ZM7 9.71H17V8.21H7V9.71ZM7 14.29H14V12.79H7V14.29Z'],
      ['label'=>'Win Rate',       'value'=>$stats['win_rate'].'%',                   'trend'=>($stats['win_rate']>=50 ? 'up' : 'down'), 'color'=>'fill-gray-800 dark:fill-white/90', 'bg'=>'bg-gray-100 dark:bg-gray-800',
       'icon'=>'M12 2C11.5858 2 11.25 2.33579 11.25 2.75V12C11.25 12.4142 11.5858 12.75 12 12.75H21.25C21.6642 12.75 22 12.4142 22 12C22 6.47715 17.5228 2 12 2ZM12.75 11.25V3.53263C17.3175 3.97919 20.9728 7.49498 21.4674 11.25H12.75ZM2 12C2 7.25083 5.31065 3.27489 9.75 2.25415V13.5H21.7459C20.7251 18.6894 16.7492 22 12 22C6.47715 22 2 17.5229 2 12Z'],
    ];
  @endphp

  @foreach($metrics as $m)
  <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 md:p-6">
    <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $m['bg'] }}">
      <svg class="{{ $m['color'] }}" width="24" height="24" viewBox="0 0 24 24" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="{{ $m['icon'] }}" fill=""/>
      </svg>
    </div>
    <div class="mt-5 flex items-end justify-between">
      <div>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $m['label'] }}</span>
        <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $m['value'] }}</h4>
      </div>
      @if($m['trend'] === 'up')
      <span class="flex items-center gap-1 rounded-full bg-success-50 py-0.5 pl-2 pr-2.5 text-sm font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
        <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z" fill=""/></svg>
        Aktif
      </span>
      @elseif($m['trend'] === 'down')
      <span class="flex items-center gap-1 rounded-full bg-error-50 py-0.5 pl-2 pr-2.5 text-sm font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
        <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753L6.62329 1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z" fill=""/></svg>
        Perlu Perhatian
      </span>
      @endif
    </div>
  </div>
  @endforeach

</div>

{{-- Charts — exact chart-01 structure --}}
<div class="grid grid-cols-12 gap-4 md:gap-6 mb-6">
  <div class="col-span-12 lg:col-span-8">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-gray-900 sm:px-6 sm:pt-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Trend Tender</h3>
        <div x-data="{openDropDown: false}" class="relative h-fit">
          <button @click="openDropDown = !openDropDown" :class="openDropDown ? 'text-gray-700 dark:text-white' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z" fill=""/></svg>
          </button>
          <div x-show="openDropDown" @click.outside="openDropDown = false"
            class="absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-theme-lg dark:border-gray-800 dark:bg-gray-900">
            <button class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5">
              12 Bulan Terakhir
            </button>
          </div>
        </div>
      </div>
      {{-- Responsive canvas wrapper: height fixed, width 100% --}}
      <div class="relative w-full pb-4" style="height:220px;">
        <canvas id="trendChart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>
      </div>
    </div>
  </div>

  <div class="col-span-12 lg:col-span-4">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-gray-900 sm:px-6 sm:pt-6">
      <div class="mb-5 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Status Tender</h3>
      </div>
      {{-- Chart wrapper — center text via absolute positioning --}}
      <div class="relative flex items-center justify-center pb-6">
        <div class="relative w-[200px] h-[200px] sm:w-[220px] sm:h-[220px]">
          <canvas id="statusChart"></canvas>
          {{-- Center label --}}
          <div id="statusChartCenter"
            class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
            <span id="statusChartTotal" class="text-2xl font-bold text-gray-800 dark:text-white/90">0</span>
            <span class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Total Tender</span>
          </div>
        </div>
      </div>
      {{-- Legend — dots adapt to dark mode via CSS class --}}
      <div id="statusChartLegend" class="flex flex-wrap justify-center gap-x-4 gap-y-2 pb-5 px-2"></div>
    </div>
  </div>
</div>

{{-- My Work — user-scoped stats --}}
@if(isset($stats['my_tenders']))
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
  {{-- My Tender --}}
  <a href="{{ route('tenders.index', ['my_tender' => 1]) }}"
    class="flex items-center gap-4 rounded-2xl border border-brand-200 bg-brand-50 p-5 hover:bg-brand-100 dark:border-brand-800/40 dark:bg-brand-500/5 dark:hover:bg-brand-500/10 transition-colors">
    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-500">
      <svg class="fill-white" width="22" height="22" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.50391 4.25H17.7504C18.1646 4.25 18.5004 4.58579 18.5004 5V16.75H9.25391C8.83969 16.75 8.50391 16.4142 8.50391 16V4.25ZM7.00391 4.99854V4.25C7.00391 3.00736 8.01127 2 9.25391 2H15.2777C15.8745 2 16.4468 2.23705 16.8687 2.659L19.3414 5.13168C19.7634 5.55364 20.0004 6.12594 20.0004 6.72268V16.75C20.0004 17.9926 18.9931 19 17.7504 19H16.248L16.248 19.75C16.248 20.9926 15.2407 22 13.998 22H6.24805C5.00541 22 3.99805 20.9926 3.99805 19.75V7.24854C3.99805 6.00589 5.00541 4.99854 6.24805 4.99854H7.00391Z" fill=""/></svg>
    </div>
    <div>
      <p class="text-xs text-brand-600 dark:text-brand-400 font-medium">My Tender</p>
      <p class="text-2xl font-bold text-brand-700 dark:text-brand-300">{{ $stats['my_tenders'] }}</p>
      <p class="text-xs text-brand-500/70">tender aktif saya</p>
    </div>
  </a>

  {{-- My Proposal --}}
  <a href="{{ route('proposals.index', ['my_proposal' => 1]) }}"
    class="flex items-center gap-4 rounded-2xl border border-brand-200 bg-brand-50 p-5 hover:bg-brand-100 dark:border-brand-800/40 dark:bg-brand-500/5 dark:hover:bg-brand-500/10 transition-colors">
    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-600">
      <svg class="fill-white" width="22" height="22" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H18.5001C19.7427 20.75 20.7501 19.7426 20.7501 18.5V5.5C20.7501 4.25736 19.7427 3.25 18.5001 3.25H5.5ZM6.25005 9.7143C6.25005 9.30008 6.58583 8.9643 7.00005 8.9643L17 8.96429C17.4143 8.96429 17.75 9.30008 17.75 9.71429C17.75 10.1285 17.4143 10.4643 17 10.4643L7.00005 10.4643C6.58583 10.4643 6.25005 10.1285 6.25005 9.7143ZM6.25005 14.2857C6.25005 13.8715 6.58583 13.5357 7.00005 13.5357H14C14.4143 13.5357 14.75 13.8715 14.75 14.2857C14.75 14.6999 14.4143 15.0357 14 15.0357H7.00005C6.58583 15.0357 6.25005 14.6999 6.25005 14.2857Z" fill=""/></svg>
    </div>
    <div>
      <p class="text-xs text-brand-600 dark:text-brand-400 font-medium">My Proposal</p>
      <p class="text-2xl font-bold text-brand-700 dark:text-brand-300">{{ $stats['my_proposals'] }}</p>
      <p class="text-xs text-brand-500/70">proposal aktif saya</p>
    </div>
  </a>

  {{-- My Upcoming Deadline --}}
  <div class="flex items-center gap-4 rounded-2xl border {{ $stats['my_overdue'] > 0 ? 'border-error-200 bg-error-50 dark:border-error-800/40 dark:bg-error-500/5' : 'border-warning-200 bg-warning-50 dark:border-warning-800/40 dark:bg-warning-500/5' }} p-5">
    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $stats['my_overdue'] > 0 ? 'bg-error-500' : 'bg-warning-500' }}">
      <svg class="fill-white" width="22" height="22" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM12 6.25C12.4142 6.25 12.75 6.58579 12.75 7V11.6893L15.5303 14.4697C15.8232 14.7626 15.8232 15.2374 15.5303 15.5303C15.2374 15.8232 14.7626 15.8232 14.4697 15.5303L11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12V7C11.25 6.58579 11.5858 6.25 12 6.25Z" fill=""/></svg>
    </div>
    <div>
      <p class="text-xs {{ $stats['my_overdue'] > 0 ? 'text-error-600 dark:text-error-400' : 'text-warning-600 dark:text-warning-400' }} font-medium">
        {{ $stats['my_overdue'] > 0 ? 'Overdue' : 'Upcoming Deadline' }}
      </p>
      <p class="text-2xl font-bold {{ $stats['my_overdue'] > 0 ? 'text-error-700 dark:text-error-300' : 'text-warning-700 dark:text-warning-300' }}">
        {{ $stats['my_overdue'] > 0 ? $stats['my_overdue'] : $stats['my_upcoming']->count() }}
      </p>
      <p class="text-xs {{ $stats['my_overdue'] > 0 ? 'text-error-500/70' : 'text-warning-500/70' }}">
        {{ $stats['my_overdue'] > 0 ? 'tender overdue saya' : 'deadline 30 hari ke depan' }}
      </p>
    </div>
  </div>

  {{-- My Upcoming deadline list --}}
  @if($stats['my_upcoming']->isNotEmpty())
  <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <p class="mb-2 text-xs font-semibold text-gray-500">Deadline Terdekat Saya</p>
    <div class="space-y-1.5">
      @foreach($stats['my_upcoming']->take(3) as $t)
      <a href="{{ route('tenders.show', $t) }}" class="flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-lg px-1 py-0.5 transition-colors">
        <span class="truncate text-xs font-medium text-gray-700 dark:text-gray-300 max-w-[120px]">{{ $t->title }}</span>
        @php $dl = $t->deadline_status; @endphp
        <span class="ml-1 shrink-0 rounded-full px-1.5 py-0.5 text-[10px] font-semibold
          {{ $dl?->value === 'overdue' ? 'bg-error-100 text-error-700' : ($dl?->value === 'critical' ? 'bg-error-50 text-error-600' : ($dl?->value === 'warning' ? 'bg-warning-50 text-warning-600' : 'bg-success-50 text-success-600')) }}">
          {{ $t->deadline_label }}
        </span>
      </a>
      @endforeach
    </div>
  </div>
  @else
  <div class="flex items-center justify-center rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <p class="text-xs text-gray-400">Tidak ada deadline mendekat</p>
  </div>
  @endif

</div>
@endif

{{-- Pipeline Tender --}}
<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
  <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
    <div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Tender Pipeline</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Distribusi tender per tahap</p>
    </div>
    <a href="{{ route('tenders.index') }}"
      class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
      Lihat Semua
      <svg class="fill-current" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715Z" fill=""/></svg>
    </a>
  </div>
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 divide-x divide-y divide-gray-100 dark:divide-gray-800">
    @php
      $pipelineItems = [
        ['key'=>'draft',         'label'=>'Draft',         'color'=>'bg-gray-400'],
        ['key'=>'identified',    'label'=>'Identified',    'color'=>'bg-blue-400'],
        ['key'=>'qualification', 'label'=>'Qualification', 'color'=>'bg-brand-500'],
        ['key'=>'preparation',   'label'=>'Preparation',   'color'=>'bg-warning-500'],
        ['key'=>'submitted',     'label'=>'Submitted',     'color'=>'bg-gray-700'],
        ['key'=>'evaluation',    'label'=>'Evaluation',    'color'=>'bg-blue-light-500'],
        ['key'=>'clarification', 'label'=>'Clarification', 'color'=>'bg-warning-600'],
        ['key'=>'negotiation',   'label'=>'Negotiation',   'color'=>'bg-theme-purple-500'],
        ['key'=>'won',           'label'=>'Won',           'color'=>'bg-success-500'],
        ['key'=>'lost',          'label'=>'Lost',          'color'=>'bg-error-500'],
        ['key'=>'completed',     'label'=>'Completed',     'color'=>'bg-success-700'],
        ['key'=>'cancelled',     'label'=>'Cancelled',     'color'=>'bg-gray-300'],
      ];
    @endphp
    @foreach($pipelineItems as $item)
    @php $count = $stats['pipeline'][$item['key']] ?? 0; @endphp
    <a href="{{ route('tenders.index', ['status' => $item['key']]) }}"
       class="flex flex-col items-center gap-1 px-4 py-5 text-center hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
      <span class="inline-flex h-2 w-8 rounded-full {{ $item['color'] }}"></span>
      <span class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $count }}</span>
      <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item['label'] }}</span>
    </a>
    @endforeach
  </div>
</div>

{{-- Upcoming Deadlines --}}
<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
  <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
    <div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Deadline Terdekat</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">30 hari ke depan</p>
    </div>
    <a href="{{ route('tenders.index') }}"
      class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
      Lihat Semua
      <svg class="fill-current" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715Z" fill=""/></svg>
    </a>
  </div>

  @if($stats['upcoming_deadlines']->isEmpty())
  <div class="flex flex-col items-center justify-center py-12 text-center">
    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/10">
      <svg class="fill-success-500" width="28" height="28" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM16.7071 10.2071C17.0976 9.81658 17.0976 9.18342 16.7071 8.79289C16.3166 8.40237 15.6834 8.40237 15.2929 8.79289L10.5 13.5858L8.70711 11.7929C8.31658 11.4024 7.68342 11.4024 7.29289 11.7929C6.90237 12.1834 6.90237 12.8166 7.29289 13.2071L9.79289 15.7071C10.1834 16.0976 10.8166 16.0976 11.2071 15.7071L16.7071 10.2071Z" fill=""/></svg>
    </div>
    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada deadline dalam 30 hari ke depan</p>
  </div>
  @else
  <div class="overflow-x-auto custom-scrollbar">
    <table class="w-full min-w-[680px] text-sm">
      <thead>
        <tr class="border-b border-gray-100 dark:border-gray-800">
          <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Tender</th>
          <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Klien</th>
          <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">PIC</th>
          <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Deadline</th>
          <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Sisa</th>
          <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Status</th>
          <th class="px-6 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        @foreach($stats['upcoming_deadlines'] as $tender)
        @php
          $days = $tender->days_until_deadline;
          $dlColor = match(true) {
            $days < 0  => 'text-error-600 font-bold',
            $days <= 3 => 'text-error-500 font-semibold',
            $days <= 7 => 'text-warning-600',
            default    => 'text-success-600',
          };
        @endphp
        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
          <td class="px-6 py-4">
            <p class="font-medium text-gray-800 dark:text-white/90">{{ Str::limit($tender->title, 38) }}</p>
            <p class="text-xs text-gray-400 font-mono">{{ $tender->code }}</p>
          </td>
          <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $tender->client?->name ?? '-' }}</td>
          <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $tender->pic?->name ?? '-' }}</td>
          <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $tender->submission_deadline?->format('d M Y') }}</td>          <td class="px-6 py-4 text-sm {{ $dlColor }}">{{ $tender->deadline_label }}</td>
          <td class="px-6 py-4"><x-status-badge :status="$tender->status" /></td>
          <td class="px-6 py-4">
            <a href="{{ route('tenders.show', $tender) }}"
              class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
              <svg class="fill-current" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill=""/></svg>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
// ─── helpers ────────────────────────────────────────────────────────────────
const isDark = () => document.documentElement.classList.contains('dark');
const gridColor  = () => isDark() ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
const tickColor  = () => isDark() ? '#667085' : '#98a2b3';
const borderBg   = () => isDark() ? '#101828' : '#ffffff';
const legendText = () => isDark() ? '#9ca3af' : '#667085';

// Apply dark class immediately from localStorage before charts init,
// in case Alpine.js hasn't hydrated yet (deferred script race condition).
(function applyDarkEarly() {
  try {
    const stored = JSON.parse(localStorage.getItem('darkMode') ?? 'false');
    if (stored === true) {
      document.documentElement.classList.add('dark');
    }
  } catch(e) {}
})();

// ─── LINE CHART — Trend Tender ───────────────────────────────────────────────
const trendData = @json($stats['monthly_tenders']);
const months = Object.keys(trendData).map(m => {
  const [y,mo] = m.split('-');
  return new Date(y, mo-1).toLocaleDateString('id-ID',{month:'short',year:'2-digit'});
});

const trendChart = new Chart(document.getElementById('trendChart'), {
  type: 'line',
  data: {
    labels: months,
    datasets: [{
      label: 'Tender',
      data: Object.values(trendData),
      borderColor: '#465fff',
      borderWidth: 2,
      pointBackgroundColor: '#465fff',
      pointBorderColor: borderBg(),
      pointBorderWidth: 2,
      pointRadius: 4,
      pointHoverRadius: 6,
      fill: true,
      backgroundColor: function(ctx) {
        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, ctx.chart.height);
        g.addColorStop(0, 'rgba(70,95,255,0.18)');
        g.addColorStop(1, 'rgba(70,95,255,0.00)');
        return g;
      },
      tension: 0.4,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: { mode: 'index', intersect: false }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { stepSize: 1, color: tickColor(), font: { size: 11 } },
        grid: { color: gridColor() },
        border: { display: false },
      },
      x: {
        ticks: { color: tickColor(), font: { size: 11 } },
        grid: { display: false },
        border: { display: false },
      }
    },
    interaction: { mode: 'nearest', axis: 'x', intersect: false }
  }
});

// ─── DONUT CHART — Status Tender ────────────────────────────────────────────
const statusData = @json($stats['tender_by_status']);
const sColors = {
  draft:'#94A3B8', identified:'#0BA5EC', qualification:'#1D4ED8',
  preparation:'#D97706', submitted:'#344054', evaluation:'#0284C7',
  clarification:'#B45309', negotiation:'#6D28D9',
  won:'#16A34A', lost:'#DC2626', completed:'#059669', cancelled:'#9CA3AF'
};
const sLabels = {
  draft:'Draft', identified:'Identified', qualification:'Qualification',
  preparation:'Preparation', submitted:'Submitted', evaluation:'Evaluation',
  clarification:'Clarification', negotiation:'Negotiation',
  won:'Won', lost:'Lost', completed:'Completed', cancelled:'Cancelled'
};

const dLabels=[], dVals=[], dColors=[];
Object.entries(statusData).forEach(([k,v]) => {
  if (v.total > 0) {
    dLabels.push(sLabels[k] || k);
    dVals.push(v.total);
    dColors.push(sColors[k] || '#98a2b3');
  }
});

if (dVals.length) {
  const total = dVals.reduce((a,b) => a+b, 0);
  document.getElementById('statusChartTotal').textContent = total;

  // Build custom legend — text color adapts via CSS variable workaround
  const legend = document.getElementById('statusChartLegend');
  dLabels.forEach((lbl, i) => {
    const item = document.createElement('div');
    item.className = 'flex items-center gap-1.5 chart-legend-item';
    item.innerHTML =
      `<span style="width:8px;height:8px;border-radius:50%;background:${dColors[i]};display:inline-block;flex-shrink:0;"></span>` +
      `<span class="chart-legend-text text-[11px] text-gray-500 dark:text-gray-400">${lbl}</span>`;
    legend.appendChild(item);
  });

  const donutChart = new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
      labels: dLabels,
      datasets: [{
        data: dVals,
        backgroundColor: dColors,
        borderWidth: 3,
        borderColor: borderBg(),
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      cutout: '72%',
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.label}: ${ctx.parsed} tender`
          }
        }
      },
      animation: { animateRotate: true, animateScale: false }
    }
  });

  // ─── Dark mode observer — update chart colors when class toggles ──────────
  const htmlEl = document.documentElement;
  const observer = new MutationObserver(() => {
    // Update trend chart grid/tick colors
    trendChart.options.scales.y.ticks.color = tickColor();
    trendChart.options.scales.y.grid.color  = gridColor();
    trendChart.options.scales.x.ticks.color = tickColor();
    trendChart.data.datasets[0].pointBorderColor = borderBg();
    trendChart.update('none');

    // Update donut border color
    donutChart.data.datasets[0].borderColor = borderBg();
    donutChart.update('none');
  });
  observer.observe(htmlEl, { attributes: true, attributeFilter: ['class'] });
}
</script>
@endpush
