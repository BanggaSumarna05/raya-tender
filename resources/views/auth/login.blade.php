<!DOCTYPE html>
<html lang="id"
  x-data="{ darkMode: JSON.parse(localStorage.getItem('darkMode') ?? 'false') }"
  x-init="$watch('darkMode', v => localStorage.setItem('darkMode', JSON.stringify(v)))"
  :class="{ 'dark': darkMode }">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Login — Raya Tender</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo/Logo.png') }}" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/Logo.png') }}" />
  <link rel="apple-touch-icon" href="{{ asset('images/logo/Logo.png') }}" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-outfit">
<div class="relative flex w-full min-h-screen">

  {{-- Full-page background photo (sama dengan panel kanan) --}}
  <div class="absolute inset-0 z-0">
    <img src="{{ asset('img/hover-login.png') }}" alt="" class="w-full h-full object-cover" />
    <div class="absolute inset-0 bg-brand-950/20"></div>
  </div>

  {{-- LEFT: Form side — rounded on the right edge --}}
  <div class="relative z-10 flex flex-col justify-center w-full lg:w-2/5
              bg-white dark:bg-gray-900
              px-8 py-12 lg:px-12"
       style="border-radius: 0 40px 40px 0; box-shadow: 8px 0 40px rgba(0,0,0,0.35), 0 8px 40px rgba(0,0,0,0.25);">

    <div class="w-full max-w-md mx-auto">

      {{-- Logo --}}
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-6">
          <img src="{{ asset('images/logo/Raya - Logo.png') }}" alt="Raya Tender" class="h-10 w-auto object-contain" />
        </div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Selamat Datang</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Masuk ke sistem informasi tender & proposal</p>
      </div>

      {{-- Error --}}
      @if($errors->any())
      <div class="alert-error mb-6 flex items-start gap-2 text-sm">
        <svg class="mt-0.5 shrink-0 fill-error-500" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM11 14C11 14.5523 10.5523 15 10 15C9.44772 15 9 14.5523 9 14C9 13.4477 9.44772 13 10 13C10.5523 13 11 13.4477 11 14ZM9 6C9 5.44772 9.44772 5 10 5C10.5523 5 11 5.44772 11 6V11C11 11.5523 10.5523 12 10 12C9.44772 12 9 11.5523 9 11V6Z" fill=""/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
      @endif

      @if(session('success'))
      <div class="alert-success mb-6 text-sm">{{ session('success') }}</div>
      @endif

      <form action="{{ route('login.post') }}" method="POST" class="space-y-5" novalidate>
        @csrf

        <div>
          <label class="form-label" for="email">Email <span class="text-error-500">*</span></label>
          <input type="email" id="email" name="email"
            class="form-input @error('email') border-error-300 focus:border-error-400 focus:ring-error-100 @enderror"
            value="{{ old('email') }}" placeholder="admin@perusahaan.com" autofocus autocomplete="email" />
        </div>

        <div>
          <label class="form-label" for="password">Password <span class="text-error-500">*</span></label>
          <div x-data="{ show: false }" class="relative">
            <input :type="show ? 'text' : 'password'" id="password" name="password"
              class="form-input @error('password') border-error-300 @enderror pr-11"
              placeholder="Masukkan password" autocomplete="current-password" />
            <button type="button" @click="show = !show"
              class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
              <svg x-show="!show" class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3"/>
              </svg>
              <svg x-show="show" class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709Z" fill="#98A2B3"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20" />
            Ingat saya
          </label>
        </div>

        <button type="submit"
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-semibold text-white shadow-theme-xs transition hover:bg-brand-600">
          Masuk
        </button>
      </form>

      <p class="mt-8 text-center text-xs text-gray-400">&copy; {{ date('Y') }} PT. Raya Konstruksi · Internal use only</p>
    </div>
  </div>

  {{-- RIGHT: spacer so form only takes half the width on desktop --}}
  <div class="hidden lg:block flex-1"></div>

</div>
</body>
</html>
