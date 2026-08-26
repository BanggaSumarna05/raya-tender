{{--
  x-financial-value

  Reusable component untuk menampilkan nilai finansial sensitif.
  Default: hidden (Rp ••••••••••)
  Setelah authorized via AJAX: tampilkan nilai real

  Props:
    - endpoint : string  URL AJAX untuk mengambil data finansial
    - field    : string  nama field dalam response data (e.g. 'estimated_value', 'bid_value')
    - label    : string  label tampilan (e.g. 'Nilai Estimasi')
    - size     : string  'normal' | 'sm' (default: 'normal')

  Security:
    - Nilai TIDAK dikirim ke browser dari server
    - Nilai hanya dikirim setelah user klik Tampilkan
    - Backend melakukan authorization check setiap request
    - Setiap akses tercatat di activity log
--}}

@props([
    'endpoint',
    'field',
    'label'    => 'Nilai',
    'size'     => 'normal',
])

<div
    x-data="{
        shown: false,
        loading: false,
        value: null,
        error: null,
        endpoint: '{{ $endpoint }}',
        field: '{{ $field }}',

        async toggle() {
            if (this.shown) {
                this.shown = false;
                this.value = null;
                return;
            }
            await this.reveal();
        },

        async reveal() {
            this.loading = true;
            this.error   = null;
            try {
                const res = await fetch(this.endpoint, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    credentials: 'same-origin',
                });
                const json = await res.json();
                if (json.success && json.data && json.data[this.field] !== null) {
                    this.value = json.data[this.field];
                    this.shown = true;
                } else if (!json.success) {
                    this.error = json.message || 'Tidak memiliki izin.';
                } else {
                    this.value = null;
                    this.shown = true; // show as '—'
                }
            } catch (e) {
                this.error = 'Gagal mengambil data. Coba lagi.';
            } finally {
                this.loading = false;
            }
        }
    }"
    class="financial-value-wrapper"
>
    @if($size === 'sm')
    {{-- Compact version for tables / cards --}}
    <div class="flex items-center gap-2">
        <span
            class="text-sm font-medium text-gray-700 dark:text-gray-300"
            x-text="shown ? (value ?? '—') : 'Rp ••••••••••'"
        ></span>
        <button
            @click="toggle"
            :disabled="loading"
            class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-xs font-medium text-brand-600 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-500/10 transition-colors"
            :title="shown ? 'Sembunyikan' : 'Tampilkan'"
        >
            <template x-if="loading">
                <svg class="animate-spin" width="12" height="12" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </template>
            <template x-if="!loading">
                <svg class="fill-current" width="12" height="12" viewBox="0 0 20 20">
                    <template x-if="!shown">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill=""/>
                    </template>
                    <template x-if="shown">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.28033 2.21967C2.98744 1.92678 2.51256 1.92678 2.21967 2.21967C1.92678 2.51256 1.92678 2.98744 2.21967 3.28033L4.03231 5.09298C2.69839 6.21802 1.71208 7.78013 1.24469 9.57493C1.15177 9.93382 1.15177 10.3099 1.24469 10.6688C2.36759 14.9325 6.29431 18.0625 10.0002 18.0625C11.8144 18.0625 13.5245 17.4609 14.9175 16.3842L16.7197 18.1863C17.0126 18.4792 17.4874 18.4792 17.7803 18.1863C18.0732 17.8934 18.0732 17.4186 17.7803 17.1257L3.28033 2.21967ZM13.7656 15.2324C12.6552 16.0384 11.3657 16.5625 10.0002 16.5625C7.08795 16.5625 3.74997 13.9438 2.71044 10.1185C2.68485 10.0224 2.68485 9.92136 2.71044 9.82528C3.13699 8.21266 4.03407 6.84528 5.23001 5.8435L7.3028 7.91629C6.95069 8.41356 6.74902 9.01953 6.74902 9.67188C6.74902 11.3253 8.04592 12.6641 9.64941 12.6641C10.3018 12.6641 10.9077 12.4624 11.4049 12.1103L13.7656 15.2324ZM10.0002 3.4375C8.58271 3.4375 7.22046 3.80591 6.03071 4.47412L7.13879 5.58219C8.01785 5.15884 8.99164 4.9375 10.0002 4.9375C12.9125 4.9375 16.2505 7.55623 17.29 11.3815C17.3156 11.4776 17.3156 11.5786 17.29 11.6747C17.0093 12.7131 16.534 13.6475 15.9136 14.4508L16.9866 15.5238C17.7952 14.5167 18.4114 13.3236 18.7558 12.0117C18.8487 11.6528 18.8487 11.2767 18.7558 10.9178C17.6329 6.65407 13.7062 3.4375 10.0002 3.4375Z" fill=""/>
                    </template>
                </svg>
            </template>
        </button>
    </div>
    @else
    {{-- Full / detail version --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $label }}</p>
        <div class="mt-1 flex items-center gap-2">
            <p
                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                x-text="shown ? (value ?? '—') : 'Rp ••••••••••'"
            ></p>
            <button
                @click="toggle"
                :disabled="loading"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-white px-2 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors disabled:opacity-50"
            >
                <template x-if="loading">
                    <svg class="animate-spin" width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <template x-if="!loading && !shown">
                    <svg class="fill-current" width="13" height="13" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill=""/>
                    </svg>
                </template>
                <template x-if="!loading && shown">
                    <svg class="fill-current" width="13" height="13" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.28033 2.21967C2.98744 1.92678 2.51256 1.92678 2.21967 2.21967C1.92678 2.51256 1.92678 2.98744 2.21967 3.28033L4.03231 5.09298C2.69839 6.21802 1.71208 7.78013 1.24469 9.57493C1.15177 9.93382 1.15177 10.3099 1.24469 10.6688C2.36759 14.9325 6.29431 18.0625 10.0002 18.0625C11.8144 18.0625 13.5245 17.4609 14.9175 16.3842L16.7197 18.1863C17.0126 18.4792 17.4874 18.4792 17.7803 18.1863C18.0732 17.8934 18.0732 17.4186 17.7803 17.1257L3.28033 2.21967ZM13.7656 15.2324C12.6552 16.0384 11.3657 16.5625 10.0002 16.5625C7.08795 16.5625 3.74997 13.9438 2.71044 10.1185C2.68485 10.0224 2.68485 9.92136 2.71044 9.82528C3.13699 8.21266 4.03407 6.84528 5.23001 5.8435L7.3028 7.91629C6.95069 8.41356 6.74902 9.01953 6.74902 9.67188C6.74902 11.3253 8.04592 12.6641 9.64941 12.6641C10.3018 12.6641 10.9077 12.4624 11.4049 12.1103L13.7656 15.2324ZM10.0002 3.4375C8.58271 3.4375 7.22046 3.80591 6.03071 4.47412L7.13879 5.58219C8.01785 5.15884 8.99164 4.9375 10.0002 4.9375C12.9125 4.9375 16.2505 7.55623 17.29 11.3815C17.3156 11.4776 17.3156 11.5786 17.29 11.6747C17.0093 12.7131 16.534 13.6475 15.9136 14.4508L16.9866 15.5238C17.7952 14.5167 18.4114 13.3236 18.7558 12.0117C18.8487 11.6528 18.8487 11.2767 18.7558 10.9178C17.6329 6.65407 13.7062 3.4375 10.0002 3.4375Z" fill=""/>
                    </svg>
                </template>
                <span x-text="loading ? 'Memuat...' : (shown ? 'Sembunyikan' : 'Tampilkan')"></span>
            </button>
        </div>
        {{-- Error message — shows inline --}}
        <p x-show="error" x-text="error" class="mt-1 text-xs text-error-500"></p>
    </div>
    @endif
</div>
