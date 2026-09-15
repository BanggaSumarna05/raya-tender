<!DOCTYPE html>
<html lang="id"
  x-data="{
    page: '{{ Str::slug(request()->segment(1) ?? 'dashboard') }}',
    loaded: true,
    darkMode: JSON.parse(localStorage.getItem('darkMode') ?? 'false'),
    stickyMenu: false,
    sidebarToggle: false,
    scrollTop: false
  }"
  x-init="$watch('darkMode', v => localStorage.setItem('darkMode', JSON.stringify(v)))"
  :class="{'dark bg-gray-900': darkMode === true}"
>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard') - Raya Tender</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo/Logo.png') }}" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/Logo.png') }}" />
  <link rel="apple-touch-icon" href="{{ asset('images/logo/Logo.png') }}" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="{{ asset('css/eina-font.css') }}" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  @stack('styles')
</head>
<body class="font-outfit">
<div class="flex h-screen overflow-hidden">

{{-- ===== SIDEBAR (exact Raya Konstruksi structure) ===== --}}
<aside
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-[99999] flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
>
  {{-- SIDEBAR HEADER --}}
  <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-8 sidebar-header pb-7">
    <a href="{{ route('dashboard') }}">
      {{-- Full logo — tampil saat sidebar expanded --}}
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img src="{{ asset('images/logo/Raya - Logo.png') }}" alt="Raya Tender" class="h-9 w-auto object-contain" />
      </span>
      {{-- Icon-only logo — tampil saat sidebar collapsed (desktop) --}}
      <img class="logo-icon" :class="sidebarToggle ? 'lg:block' : 'hidden'"
        src="{{ asset('images/logo/Logo.png') }}" alt="Raya Tender"
        style="height:32px;width:auto;object-fit:contain;" />
    </a>
  </div>
  {{-- END SIDEBAR HEADER --}}

  <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
    <nav x-data="{ selected: $persist('Dashboard') }">

      {{-- MENU GROUP --}}
      <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">MENU</span>
          <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="mx-auto fill-current menu-group-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z" fill=""/>
          </svg>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">

          {{-- Dashboard --}}
          <li>
            <a href="{{ route('dashboard') }}" @click="selected = 'Dashboard'"
              class="menu-item group" :class="page === 'dashboard' ? 'menu-item-active' : 'menu-item-inactive'">
              <svg :class="page === 'dashboard' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
            </a>
          </li>

          {{-- Data Tender --}}
          <li>
            <a href="{{ route('tenders.index') }}" @click="selected = 'Tender'"
              class="menu-item group" :class="page === 'tenders' ? 'menu-item-active' : 'menu-item-inactive'">
              <svg :class="page === 'tenders' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.50391 4.25C8.50391 3.83579 8.83969 3.5 9.25391 3.5H15.2777C15.4766 3.5 15.6674 3.57902 15.8081 3.71967L18.2807 6.19234C18.4214 6.333 18.5004 6.52376 18.5004 6.72268V16.75C18.5004 17.1642 18.1646 17.5 17.7504 17.5H9.25391C8.83969 17.5 8.50391 17.1642 8.50391 16.75V4.25ZM7.00391 4.99854V4.25C7.00391 3.00736 8.01127 2 9.25391 2H15.2777C15.8745 2 16.4468 2.23705 16.8687 2.659L19.3414 5.13168C19.7634 5.55364 20.0004 6.12594 20.0004 6.72268V16.75C20.0004 17.9926 18.9931 19 17.7504 19H16.248L16.248 19.75C16.248 20.9926 15.2407 22 13.998 22H6.24805C5.00541 22 3.99805 20.9926 3.99805 19.75V7.24854C3.99805 6.00589 5.00541 4.99854 6.24805 4.99854H7.00391Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Data Tender</span>
            </a>
          </li>

          {{-- Proposal --}}
          <li>
            <a href="{{ route('proposals.index') }}" @click="selected = 'Proposal'"
              class="menu-item group" :class="page === 'proposals' ? 'menu-item-active' : 'menu-item-inactive'">
              <svg :class="page === 'proposals' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H18.5001C19.7427 20.75 20.7501 19.7426 20.7501 18.5V5.5C20.7501 4.25736 19.7427 3.25 18.5001 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H18.5001C18.9143 4.75 19.2501 5.08579 19.2501 5.5V18.5C19.2501 18.9142 18.9143 19.25 18.5001 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V5.5ZM6.25005 9.7143C6.25005 9.30008 6.58583 8.9643 7.00005 8.9643L17 8.96429C17.4143 8.96429 17.75 9.30008 17.75 9.71429C17.75 10.1285 17.4143 10.4643 17 10.4643L7.00005 10.4643C6.58583 10.4643 6.25005 10.1285 6.25005 9.7143ZM6.25005 14.2857C6.25005 13.8715 6.58583 13.5357 7.00005 13.5357H17C17.4143 13.5357 17.75 13.8715 17.75 14.2857C17.75 14.6999 17.4143 15.0357 17 15.0357H7.00005C6.58583 15.0357 6.25005 14.6999 6.25005 14.2857Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Proposal</span>
            </a>
          </li>

        </ul>
      </div>

      {{-- OTHERS GROUP --}}
      <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">others</span>
          <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="mx-auto fill-current menu-group-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z" fill=""/>
          </svg>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">

          {{-- Dokumen --}}
          <li>
            <a href="{{ route('documents.index') }}" @click="selected = 'Dokumen'"
              class="menu-item group" :class="page === 'documents' ? 'menu-item-active' : 'menu-item-inactive'">
              <svg :class="page === 'documents' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 6C3.25 4.75736 4.25736 3.75 5.5 3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6ZM19.25 8.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25ZM4.75 9.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75H4.75Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Dokumen</span>
            </a>
          </li>

          {{-- Laporan --}}
          <li>
            <a href="{{ route('reports.index') }}" @click="selected = 'Laporan'"
              class="menu-item group" :class="page === 'reports' ? 'menu-item-active' : 'menu-item-inactive'">
              <svg :class="page === 'reports' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C11.5858 2 11.25 2.33579 11.25 2.75V12C11.25 12.4142 11.5858 12.75 12 12.75H21.25C21.6642 12.75 22 12.4142 22 12C22 6.47715 17.5228 2 12 2ZM12.75 11.25V3.53263C17.3175 3.97919 20.9728 7.49498 21.4674 11.25H12.75ZM2 12C2 7.25083 5.31065 3.27489 9.75 2.25415V13.5H21.7459C20.7251 18.6894 16.7492 22 12 22C6.47715 22 2 17.5229 2 12Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Laporan</span>
            </a>
          </li>

          {{-- Klien --}}
          <li>
            <a href="{{ route('clients.index') }}" @click="selected = 'Klien'"
              class="menu-item group" :class="page === 'clients' ? 'menu-item-active' : 'menu-item-inactive'">
              <svg :class="page === 'clients' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Master Klien</span>
            </a>
          </li>

          @can('view_categories')
          @php $catActive = request()->routeIs('settings.categories.*'); @endphp
          <li>
            <a href="{{ route('settings.categories.index') }}" @click="selected = 'Kategori'"
              class="menu-item group {{ $catActive ? 'menu-item-active' : 'menu-item-inactive' }}">
              {{-- Tag / label icon --}}
              <svg class="{{ $catActive ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.25 5.5C4.25 4.80964 4.80964 4.25 5.5 4.25H11.3787C11.7765 4.25 12.158 4.40804 12.4393 4.68934L19.4697 11.7197C19.6103 11.8603 19.75 12.0711 19.75 12.3536C19.75 12.6362 19.6103 12.847 19.4697 12.9876L13.2197 19.2376C13.079 19.3782 12.8682 19.5179 12.5857 19.5179C12.3032 19.5179 12.0924 19.3782 11.9518 19.2376L4.92139 12.2072C4.64009 11.9259 4.48205 11.5444 4.48205 11.1466V5.5H4.25ZM5.75 5.75V11.1466L12.5857 17.9822L18.409 12.159L11.6769 5.75H5.75ZM8.5 8.75C8.5 8.05964 9.05964 7.5 9.75 7.5C10.4404 7.5 11 8.05964 11 8.75C11 9.44036 10.4404 10 9.75 10C9.05964 10 8.5 9.44036 8.5 8.75Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Kategori Tender</span>
            </a>
          </li>
          @endcan

          @can('view_users')
          @php $usersActive = request()->routeIs('settings.users.*'); @endphp
          <li>
            <a href="{{ route('settings.users.index') }}" @click="selected = 'Users'"
              class="menu-item group {{ $usersActive ? 'menu-item-active' : 'menu-item-inactive' }}">
              {{-- Users / people icon --}}
              <svg class="{{ $usersActive ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 4.25C10.2051 4.25 8.75 5.70507 8.75 7.5C8.75 9.29493 10.2051 10.75 12 10.75C13.7949 10.75 15.25 9.29493 15.25 7.5C15.25 5.70507 13.7949 4.25 12 4.25ZM7.25 7.5C7.25 4.87665 9.37665 2.75 12 2.75C14.6234 2.75 16.75 4.87665 16.75 7.5C16.75 10.1234 14.6234 12.25 12 12.25C9.37665 12.25 7.25 10.1234 7.25 7.5ZM6.5 14.25C5.25736 14.25 4.25 15.2574 4.25 16.5V17.25C4.25 17.3466 4.26871 17.4049 4.29099 17.4513C4.32052 17.5127 4.38127 17.5924 4.50403 17.6722C4.77308 17.8465 5.28982 18 6.21228 18.0837C7.12829 18.1669 8.34449 18.1874 9.87533 18.1924C10.2895 18.1937 10.623 18.5294 10.6217 18.9436C10.6204 19.3578 10.2847 19.6913 9.87047 19.69C8.34459 19.685 7.07615 19.6642 6.07524 19.574C5.08078 19.4842 4.27496 19.3083 3.65974 18.9139C3.32711 18.7034 3.03498 18.4312 2.83116 18.082C2.62734 17.7328 2.75 17.4107 2.75 17.25V16.5C2.75 14.4289 4.42893 12.75 6.5 12.75H17.5C19.5711 12.75 21.25 14.4289 21.25 16.5V17.25C21.25 17.4107 21.3727 17.7328 21.1688 18.082C20.965 18.4312 20.6729 18.7034 20.3403 18.9139C19.725 19.3083 18.9192 19.4842 17.9248 19.574C16.9239 19.6642 15.6554 19.685 14.1295 19.69C13.7153 19.6913 13.3796 19.3578 13.3783 18.9436C13.377 18.5294 13.7105 18.1937 14.1247 18.1924C15.6555 18.1874 16.8717 18.1669 17.7877 18.0837C18.7102 18 19.2269 17.8465 19.496 17.6722C19.6187 17.5924 19.6795 17.5127 19.709 17.4513C19.7313 17.4049 19.75 17.3466 19.75 17.25V16.5C19.75 15.2574 18.7426 14.25 17.5 14.25H6.5Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Kelola User</span>
            </a>
          </li>
          @endcan

          @can('view_activity_logs')
          @php $logActive = request()->routeIs('settings.activity-logs.*'); @endphp
          <li>
            <a href="{{ route('settings.activity-logs.index') }}" @click="selected = 'Log'"
              class="menu-item group {{ $logActive ? 'menu-item-active' : 'menu-item-inactive' }}">
              {{-- Clock / history icon --}}
              <svg class="{{ $logActive ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 17.1086 6.89137 21.25 12 21.25C17.1086 21.25 21.25 17.1086 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM12 6.25C12.4142 6.25 12.75 6.58579 12.75 7V11.6893L15.5303 14.4697C15.8232 14.7626 15.8232 15.2374 15.5303 15.5303C15.2374 15.8232 14.7626 15.8232 14.4697 15.5303L11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12V7C11.25 6.58579 11.5858 6.25 12 6.25Z" fill=""/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Log Aktivitas</span>
            </a>
          </li>
          @endcan

        </ul>
      </div>

    </nav>
  </div>
</aside>
{{-- ===== END SIDEBAR ===== --}}

  {{-- ===== CONTENT AREA ===== --}}
  <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">

    {{-- Small Device Overlay --}}
    <div x-show="sidebarToggle"
      @click="sidebarToggle = false"
      class="fixed inset-0 z-[9999] bg-gray-900/50 lg:hidden"
      x-transition:enter="transition-opacity ease-linear duration-300"
      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
      x-transition:leave="transition-opacity ease-linear duration-300"
      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    {{-- ===== HEADER (exact Raya Konstruksistructure) ===== --}}
    <header x-data="{ menuToggle: false }"
      class="sticky top-0 z-[99999] flex w-full border-gray-200 bg-white lg:border-b dark:border-gray-800 dark:bg-gray-900">
      <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">

        {{-- Top bar --}}
        <div class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 sm:gap-4 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4 dark:border-gray-800">

          {{-- Hamburger --}}
          <button
            :class="sidebarToggle ? 'lg:bg-transparent dark:lg:bg-transparent bg-gray-100 dark:bg-gray-800' : ''"
            class="z-[99999] flex h-10 w-10 items-center justify-center rounded-lg border-gray-200 text-gray-500 lg:h-11 lg:w-11 lg:border dark:border-gray-800 dark:text-gray-400"
            @click.stop="sidebarToggle = !sidebarToggle">
            {{-- Desktop icon --}}
            <svg class="hidden fill-current lg:block" width="16" height="12" viewBox="0 0 16 12" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z" fill=""/>
            </svg>
            {{-- Mobile open --}}
            <svg :class="sidebarToggle ? 'hidden' : 'block lg:hidden'" class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 6C3.25 5.58579 3.58579 5.25 4 5.25L20 5.25C20.4142 5.25 20.75 5.58579 20.75 6C20.75 6.41421 20.4142 6.75 20 6.75L4 6.75C3.58579 6.75 3.25 6.41422 3.25 6ZM3.25 18C3.25 17.5858 3.58579 17.25 4 17.25L20 17.25C20.4142 17.25 20.75 17.5858 20.75 18C20.75 18.4142 20.4142 18.75 20 18.75L4 18.75C3.58579 18.75 3.25 18.4142 3.25 18ZM4 11.25C3.58579 11.25 3.25 11.5858 3.25 12C3.25 12.4142 3.58579 12.75 4 12.75L12 12.75C12.4142 12.75 12.75 12.4142 12.75 12C12.75 11.5858 12.4142 11.25 12 11.25L4 11.25Z" fill=""/>
            </svg>
            {{-- Mobile close (X) --}}
            <svg :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill=""/>
            </svg>
          </button>

          {{-- Mobile logo --}}
          <a href="{{ route('dashboard') }}" class="lg:hidden">
            <img src="{{ asset('images/logo/Raya - Logo.png') }}" alt="Raya Tender" class="h-8 w-auto object-contain" />
          </a>

          {{-- Mobile 3-dot menu button --}}
          <button class="z-[99999] flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100 lg:hidden dark:text-gray-400 dark:hover:bg-gray-800"
            :class="menuToggle ? 'bg-gray-100 dark:bg-gray-800' : ''"
            @click.stop="menuToggle = !menuToggle">
            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99902 10.4951C6.82745 10.4951 7.49902 11.1667 7.49902 11.9951V12.0051C7.49902 12.8335 6.82745 13.5051 5.99902 13.5051C5.1706 13.5051 4.49902 12.8335 4.49902 12.0051V11.9951C4.49902 11.1667 5.1706 10.4951 5.99902 10.4951ZM17.999 10.4951C18.8275 10.4951 19.499 11.1667 19.499 11.9951V12.0051C19.499 12.8335 18.8275 13.5051 17.999 13.5051C17.1706 13.5051 16.499 12.8335 16.499 12.0051V11.9951C16.499 11.1667 17.1706 10.4951 17.999 10.4951ZM13.499 11.9951C13.499 11.1667 12.8275 10.4951 11.999 10.4951C11.1706 10.4951 10.499 11.1667 10.499 11.9951V12.0051C10.499 12.8335 11.1706 13.5051 11.999 13.5051C12.8275 13.5051 13.499 12.8335 13.499 12.0051V11.9951Z" fill=""/>
            </svg>
          </button>

          {{-- Desktop: breadcrumb / page title --}}
          <div class="hidden lg:block">
            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">@yield('page-title','Dashboard')</p>
            @hasSection('breadcrumb')
            <nav class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
              <a href="{{ route('dashboard') }}" class="hover:text-brand-500">Dashboard</a>
              @yield('breadcrumb')
            </nav>
            @endif
          </div>
        </div>

        {{-- Right actions (mobile collapse + desktop always show) --}}
        <div :class="menuToggle ? 'flex' : 'hidden'"
          class="shadow-theme-md w-full flex-col gap-3 px-5 py-4 lg:flex lg:flex-row lg:items-center lg:justify-end lg:px-0 lg:shadow-none lg:gap-4">

          {{-- Mobile search — full width, only visible when menu open --}}
          <div class="lg:hidden"
            x-data="{
              q: '',
              open: false,
              loading: false,
              results: [],
              async search() {
                if (this.q.length < 2) { this.results = []; this.open = false; return; }
                this.loading = true;
                try {
                  const res = await fetch('/search?q=' + encodeURIComponent(this.q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'same-origin'
                  });
                  const data = await res.json();
                  this.results = data.results || [];
                  this.open = this.results.length > 0;
                } finally { this.loading = false; }
              }
            }"
            @click.outside="open = false"
          >
            <div class="relative">
              <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-800">
                <svg class="shrink-0 fill-gray-400" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/></svg>
                <input
                  type="text"
                  x-model="q"
                  @input.debounce.300ms="search()"
                  @keydown.escape="open = false; q = ''"
                  placeholder="Cari tender, proposal, klien..."
                  class="w-full bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none dark:text-gray-300"
                />
                <svg x-show="loading" class="animate-spin shrink-0 fill-gray-400" width="14" height="14" viewBox="0 0 24 24"><path d="M12 2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8V2Z"/></svg>
              </div>
              <div x-show="open" x-transition class="absolute left-0 right-0 top-full z-[99999] mt-2 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xl dark:border-gray-700 dark:bg-gray-900">
                <template x-for="(item, i) in results" :key="i">
                  <a :href="item.url" @click="open=false;q=''" class="flex items-center gap-3 border-b border-gray-50 px-4 py-3 hover:bg-gray-50 dark:border-gray-800/50 dark:hover:bg-gray-800/60 last:border-0">
                    <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400" x-text="item.type"></span>
                    <div class="min-w-0 flex-1">
                      <p class="truncate text-sm font-medium text-gray-800 dark:text-white/90" x-text="item.title"></p>
                      <p class="text-[11px] text-gray-400" x-text="item.code + (item.client ? ' · ' + item.client : '')"></p>
                    </div>
                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="'badge '+item.badge" x-text="item.status"></span>
                  </a>
                </template>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2 2xsm:gap-3">

            {{-- Global Search --}}
          <div class="hidden lg:flex items-center"
            x-data="{
              q: '',
              open: false,
              loading: false,
              results: [],
              async search() {
                if (this.q.length < 2) { this.results = []; this.open = false; return; }
                this.loading = true;
                try {
                  const res = await fetch('/search?q=' + encodeURIComponent(this.q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'same-origin'
                  });
                  const data = await res.json();
                  this.results = data.results || [];
                  this.open = this.results.length > 0;
                } finally { this.loading = false; }
              }
            }"
            @click.outside="open = false"
          >
            <div class="relative">
              <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 w-64">
                <svg class="shrink-0 fill-gray-400" width="15" height="15" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill=""/></svg>
                <input
                  type="text"
                  x-model="q"
                  @input.debounce.300ms="search()"
                  @keydown.escape="open = false; q = ''"
                  placeholder="Cari tender, proposal, klien..."
                  class="w-full bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none dark:text-gray-300"
                />
                <svg x-show="loading" class="animate-spin shrink-0 fill-gray-400" width="14" height="14" viewBox="0 0 24 24"><path d="M12 2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8V2Z"/></svg>
              </div>

              {{-- Dropdown results --}}
              <div x-show="open" x-transition
                class="absolute left-0 top-full z-[99999] mt-2 w-[380px] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xl dark:border-gray-700 dark:bg-gray-900">
                <template x-for="(item, i) in results" :key="i">
                  <a :href="item.url"
                    @click="open = false; q = ''"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors border-b border-gray-50 dark:border-gray-800/50 last:border-0">
                    {{-- Type badge --}}
                    <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                      x-text="item.type"></span>
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-800 dark:text-white/90 truncate" x-text="item.title"></p>
                      <div class="flex items-center gap-2 mt-0.5">
                        <span class="font-mono text-[11px] text-gray-400" x-text="item.code"></span>
                        <template x-if="item.client">
                          <span class="text-[11px] text-gray-400" x-text="'· ' + item.client"></span>
                        </template>
                      </div>
                    </div>
                    {{-- Status badge --}}
                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold"
                      :class="'badge ' + item.badge"
                      x-text="item.status"></span>
                  </a>
                </template>
              </div>
            </div>
          </div>

          {{-- Dark Mode Toggle --}}
            <button
              class="hover:text-dark-900 relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
              @click.prevent="darkMode = !darkMode">
              {{-- Sun (dark mode on) --}}
              <svg class="hidden dark:block fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001Z" fill="currentColor"/>
              </svg>
              {{-- Moon (light mode) --}}
              <svg class="dark:hidden fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z" fill="currentColor"/>
              </svg>
            </button>

            {{-- Notification --}}
            <div
              class="relative"
              x-data="{
                open: false,
                loading: false,
                items: [],
                count: 0,
                hasNew: false,

                async load() {
                    if (this.loading) return;
                    this.loading = true;
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
                        this.count = json.count || 0;
                        this.hasNew = this.count > 0;
                    } catch(e) {
                        this.items = [];
                    } finally {
                        this.loading = false;
                    }
                },

                toggle() {
                    this.open = !this.open;
                    if (this.open) { this.load(); }
                }
              }"
              x-init="load()"
              @click.outside="open = false"
            >
              <button
                class="hover:text-dark-900 relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                @click.prevent="toggle">
                {{-- Dot indicator --}}
                <span x-show="hasNew" class="absolute top-0.5 right-0 z-[1] flex h-2 w-2 rounded-full bg-orange-400">
                  <span class="absolute -z-[1] inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
                </span>
                {{-- Bell icon --}}
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z" fill=""/>
                </svg>
              </button>

              {{-- Dropdown --}}
              <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="shadow-theme-lg absolute right-0 mt-[17px] flex w-[min(320px,calc(100vw-1.5rem))] flex-col rounded-2xl border border-gray-200 bg-white p-3 sm:w-[360px] dark:border-gray-800 dark:bg-gray-900 z-50"
              >
                {{-- Header --}}
                <div class="mb-3 flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                  <div class="flex items-center gap-2">
                    <h5 class="text-base font-semibold text-gray-800 dark:text-white/90">Notifikasi</h5>
                    <span x-show="count > 0"
                      x-text="count"
                      class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-error-500 px-1.5 text-xs font-bold text-white">
                    </span>
                  </div>
                  <button @click="open = false; load()" class="text-xs text-brand-500 hover:text-brand-600 font-medium">
                    Refresh
                  </button>
                </div>

                {{-- Loading state --}}
                <div x-show="loading" class="py-6 text-center">
                  <svg class="mx-auto animate-spin text-gray-400" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                </div>

                {{-- Empty state --}}
                <div x-show="!loading && items.length === 0" class="py-6 text-center">
                  <svg class="mx-auto mb-2 fill-gray-300 dark:fill-gray-600" width="32" height="32" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875Z" fill=""/>
                  </svg>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                </div>

                {{-- Notification list --}}
                <div x-show="!loading && items.length > 0" class="max-h-72 overflow-y-auto space-y-1 custom-scrollbar">
                  <template x-for="item in items" :key="item.id + item.type">
                    <a
                      :href="item.url"
                      class="flex items-start gap-3 rounded-xl px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors"
                    >
                      {{-- Icon berdasarkan type --}}
                      <div class="mt-0.5 shrink-0 flex h-8 w-8 items-center justify-center rounded-full"
                        :class="item.type === 'overdue' ? 'bg-error-50 dark:bg-error-500/10' : 'bg-warning-50 dark:bg-warning-500/10'">
                        {{-- Overdue: X circle --}}
                        <template x-if="item.type === 'overdue'">
                          <svg class="fill-error-500" width="16" height="16" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM7.28033 7.28033C7.57322 6.98744 8.04809 6.98744 8.34099 7.28033L10 8.93934L11.659 7.28033C11.9519 6.98744 12.4268 6.98744 12.7197 7.28033C13.0126 7.57322 13.0126 8.04809 12.7197 8.34099L11.0607 10L12.7197 11.659C13.0126 11.9519 13.0126 12.4268 12.7197 12.7197C12.4268 13.0126 11.9519 13.0126 11.659 12.7197L10 11.0607L8.34099 12.7197C8.04809 13.0126 7.57322 13.0126 7.28033 12.7197C6.98744 12.4268 6.98744 11.9519 7.28033 11.659L8.93934 10L7.28033 8.34099C6.98744 8.04809 6.98744 7.57322 7.28033 7.28033Z" fill=""/>
                          </svg>
                        </template>
                        {{-- Upcoming: clock --}}
                        <template x-if="item.type === 'upcoming'">
                          <svg class="fill-warning-500" width="16" height="16" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM10.75 6C10.75 5.58579 10.4142 5.25 10 5.25C9.58579 5.25 9.25 5.58579 9.25 6V10C9.25 10.2122 9.33902 10.4153 9.4948 10.5607L11.9948 12.8107C12.3009 13.0963 12.7754 13.0748 13.0607 12.7687C13.3461 12.4627 13.3246 11.9881 13.0185 11.7028L10.75 9.67085V6Z" fill=""/>
                          </svg>
                        </template>
                      </div>

                      {{-- Content --}}
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold truncate"
                          :class="item.type === 'overdue' ? 'text-error-700 dark:text-error-400' : 'text-warning-700 dark:text-warning-400'"
                          x-text="item.type === 'overdue' ? 'Deadline Terlewat' : 'Deadline Mendekat'">
                        </p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90 truncate" x-text="item.title"></p>
                        <div class="mt-0.5 flex items-center gap-2">
                          <span class="font-mono text-xs text-gray-400" x-text="item.code"></span>
                          <span class="text-xs font-semibold"
                            :class="item.type === 'overdue' ? 'text-error-500' : 'text-warning-600'"
                            x-text="item.days">
                          </span>
                        </div>
                      </div>
                    </a>
                  </template>
                </div>

                {{-- Footer --}}
                <div x-show="!loading" class="mt-2 border-t border-gray-100 dark:border-gray-800 pt-2">
                  <a href="{{ route('tenders.index') }}"
                    class="flex w-full items-center justify-center rounded-lg px-3 py-2 text-xs font-medium text-brand-600 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-500/10 transition-colors">
                    Lihat Semua Tender
                  </a>
                </div>

              </div>
            </div>

          </div>

          {{-- User Dropdown --}}
          <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
            <a class="flex items-center text-gray-700 dark:text-gray-400" href="#"
              @click.prevent="dropdownOpen = !dropdownOpen">
              <span class="mr-3 flex h-11 w-11 items-center justify-center rounded-full bg-brand-500 text-sm font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
              </span>
              <span class="text-theme-sm mr-1 hidden font-medium lg:block">{{ auth()->user()->name }}</span>
              <svg :class="dropdownOpen && 'rotate-180'" class="stroke-gray-500 dark:stroke-gray-400" width="18" height="20" viewBox="0 0 18 20" fill="none">
                <path d="M4.3125 8.65625L9 13.3437L13.6875 8.65625" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>

            <div x-show="dropdownOpen"
              class="shadow-theme-lg absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
              <div>
                <span class="text-theme-sm block font-medium text-gray-700 dark:text-gray-400">{{ auth()->user()->name }}</span>
                <span class="text-theme-xs mt-0.5 block text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</span>
                <span class="text-theme-xs mt-0.5 block text-brand-500">{{ auth()->user()->getRoleNames()->first() }}</span>
              </div>
              <ul class="flex flex-col gap-1 border-b border-gray-200 pt-4 pb-3 dark:border-gray-800">
                <li>
                  <a href="{{ route('password.change') }}"
                    class="group text-theme-sm flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5">
                    <svg class="fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M14 2.75C14 2.33579 14.3358 2 14.75 2C15.1642 2 15.5 2.33579 15.5 2.75V5.73291L17.75 5.73291H19C19.4142 5.73291 19.75 6.0687 19.75 6.48291C19.75 6.89712 19.4142 7.23291 19 7.23291H18.5L18.5 12.2329C18.5 15.5691 15.9866 18.3183 12.75 18.6901V21.25C12.75 21.6642 12.4142 22 12 22C11.5858 22 11.25 21.6642 11.25 21.25V18.6901C8.01342 18.3183 5.5 15.5691 5.5 12.2329L5.5 7.23291H5C4.58579 7.23291 4.25 6.89712 4.25 6.48291C4.25 6.0687 4.58579 5.73291 5 5.73291L6.25 5.73291L8.5 5.73291L8.5 2.75C8.5 2.33579 8.83579 2 9.25 2C9.66421 2 10 2.33579 10 2.75L10 5.73291L14 5.73291V2.75ZM7 7.23291L7 12.2329C7 14.9943 9.23858 17.2329 12 17.2329C14.7614 17.2329 17 14.9943 17 12.2329L17 7.23291L7 7.23291Z" fill=""/>
                    </svg>
                    Ganti Password
                  </a>
                </li>
              </ul>
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                  class="group text-theme-sm mt-3 flex w-full items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                  <svg class="fill-gray-500 group-hover:fill-gray-700 dark:group-hover:fill-gray-300" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.1007 19.247C14.6865 19.247 14.3507 18.9112 14.3507 18.497L14.3507 14.245H12.8507V18.497C12.8507 19.7396 13.8581 20.747 15.1007 20.747H18.5007C19.7434 20.747 20.7507 19.7396 20.7507 18.497L20.7507 5.49609C20.7507 4.25345 19.7433 3.24609 18.5007 3.24609H15.1007C13.8581 3.24609 12.8507 4.25345 12.8507 5.49609V9.74501L14.3507 9.74501V5.49609C14.3507 5.08188 14.6865 4.74609 15.1007 4.74609L18.5007 4.74609C18.9149 4.74609 19.2507 5.08188 19.2507 5.49609L19.2507 18.497C19.2507 18.9112 18.9149 19.247 18.5007 19.247H15.1007ZM3.25073 11.9984C3.25073 12.2144 3.34204 12.4091 3.48817 12.546L8.09483 17.1556C8.38763 17.4485 8.86251 17.4487 9.15549 17.1559C9.44848 16.8631 9.44863 16.3882 9.15583 16.0952L5.81116 12.7484L16.0007 12.7484C16.4149 12.7484 16.7507 12.4127 16.7507 11.9984C16.7507 11.5842 16.4149 11.2484 16.0007 11.2484L5.81528 11.2484L9.15585 7.90554C9.44864 7.61255 9.44847 7.13767 9.15547 6.84488C8.86248 6.55209 8.3876 6.55226 8.09481 6.84525L3.52309 11.4202C3.35673 11.5577 3.25073 11.7657 3.25073 11.9984Z" fill=""/>
                  </svg>
                  Sign out
                </button>
              </form>
            </div>
          </div>
          {{-- End User Dropdown --}}

        </div>
      </div>
    </header>
    {{-- ===== END HEADER ===== --}}

    {{-- ===== MAIN CONTENT ===== --}}
    <main>
      <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-xl border border-success-200 bg-success-50 p-4 text-success-700 dark:border-success-700/30 dark:bg-success-500/10 dark:text-success-400">
          <svg class="mt-0.5 shrink-0 fill-success-500" width="18" height="18" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM13.7071 8.20711C14.0976 7.81658 14.0976 7.18342 13.7071 6.79289C13.3166 6.40237 12.6834 6.40237 12.2929 6.79289L9 10.0858L7.70711 8.79289C7.31658 8.40237 6.68342 8.40237 6.29289 8.79289C5.90237 9.18342 5.90237 9.81658 6.29289 10.2071L8.29289 12.2071C8.68342 12.5976 9.31658 12.5976 9.70711 12.2071L13.7071 8.20711Z" fill=""/></svg>
          <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
          <button onclick="this.closest('[class*=success]').remove()" class="shrink-0 text-success-500 hover:text-success-700">
            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill="currentColor"/></svg>
          </button>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 flex items-start gap-3 rounded-xl border border-error-200 bg-error-50 p-4 text-error-700 dark:border-error-700/30 dark:bg-error-500/10 dark:text-error-400">
          <svg class="mt-0.5 shrink-0 fill-error-500" width="18" height="18" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM11 14C11 14.5523 10.5523 15 10 15C9.44772 15 9 14.5523 9 14C9 13.4477 9.44772 13 10 13C10.5523 13 11 13.4477 11 14ZM9 6C9 5.44772 9.44772 5 10 5C10.5523 5 11 5.44772 11 6V11C11 11.5523 10.5523 12 10 12C9.44772 12 9 11.5523 9 11V6Z" fill=""/></svg>
          <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
          <button onclick="this.closest('[class*=error]').remove()" class="shrink-0 text-error-500 hover:text-error-700">
            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill="currentColor"/></svg>
          </button>
        </div>
        @endif

        @if(session('warning'))
        <div class="mb-4 rounded-xl border border-warning-200 bg-warning-50 p-4 text-sm font-medium text-warning-700 dark:border-warning-700/30 dark:bg-warning-500/10 dark:text-warning-400">
          {{ session('warning') }}
        </div>
        @endif

        @yield('content')
      </div>
    </main>

  </div>
  {{-- ===== END CONTENT AREA ===== --}}

</div>

{{-- Delete Confirm Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[999999] hidden items-center justify-center bg-gray-900/60 p-4">
  <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-theme-xl dark:bg-gray-900">
    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-error-50 dark:bg-error-500/10 mx-auto">
      <svg class="fill-error-500" width="24" height="24" viewBox="0 0 24 24" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5V5.25H18C18.4142 5.25 18.75 5.58579 18.75 6C18.75 6.41421 18.4142 6.75 18 6.75H6C5.58579 6.75 5.25 6.41421 5.25 6C5.25 5.58579 5.58579 5.25 6 5.25H8.25V4.5C8.25 4.08579 8.58579 3.75 9 3.75ZM10.8787 2.25H13.1213C13.3643 2.25 13.5986 2.34219 13.7742 2.50969C13.9499 2.6772 14.0523 2.9063 14.0612 3.15L14.0629 3.375H9.9371L9.93879 3.15C9.94774 2.9063 10.0501 2.6772 10.2258 2.50969C10.4014 2.34219 10.6357 2.25 10.8787 2.25ZM6.75 8.25H17.25L16.65 20.25C16.6087 21.0748 15.9435 21.75 15.1184 21.75H8.88158C8.05652 21.75 7.39129 21.0748 7.35 20.25L6.75 8.25Z" fill=""/>
      </svg>
    </div>
    <h3 class="mb-2 text-center text-base font-semibold text-gray-800 dark:text-white">Hapus Data?</h3>
    <p id="deleteModalText" class="mb-6 text-center text-sm text-gray-500 dark:text-gray-400">Tindakan ini tidak dapat dibatalkan.</p>
    <div class="flex justify-center gap-3">
      <button onclick="closeDeleteModal()" class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Batal</button>
      <form id="deleteForm" method="POST" class="flex-1">
        @csrf @method('DELETE')
        <button type="submit" class="w-full rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-error-600">Hapus</button>
      </form>
    </div>
  </div>
</div>

<script>
function confirmDelete(url, text) {
  document.getElementById('deleteForm').action = url;
  if (text) document.getElementById('deleteModalText').textContent = text;
  const m = document.getElementById('deleteModal');
  m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDeleteModal() {
  const m = document.getElementById('deleteModal');
  m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) closeDeleteModal();
});
</script>

@stack('scripts')
</body>
</html>
