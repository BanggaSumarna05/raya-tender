@props(['column', 'label', 'current' => null, 'direction' => 'asc'])
@php
    $isActive    = $current === $column;
    $nextDir     = ($isActive && $direction === 'asc') ? 'desc' : 'asc';
    $params      = array_merge(request()->except('sort','direction','page'), [
                       'sort'      => $column,
                       'direction' => $nextDir,
                   ]);
    $url = request()->url() . '?' . http_build_query($params);
@endphp
<th>
  <a href="{{ $url }}" class="th-inner group flex items-center gap-1 select-none hover:text-gray-700 dark:hover:text-gray-200">
    <p class="{{ $isActive ? 'text-gray-800 dark:text-white/90 font-semibold' : '' }}">{{ $label }}</p>
    <span class="flex flex-col">
      {{-- Up arrow --}}
      <svg width="8" height="5" viewBox="0 0 8 5" fill="none"
        class="{{ $isActive && $direction === 'asc' ? 'fill-brand-500' : 'fill-gray-300 dark:fill-gray-600 group-hover:fill-gray-500' }}">
        <path d="M4 0L7.46411 4.5H0.535898L4 0Z"/>
      </svg>
      {{-- Down arrow --}}
      <svg width="8" height="5" viewBox="0 0 8 5" fill="none"
        class="{{ $isActive && $direction === 'desc' ? 'fill-brand-500' : 'fill-gray-300 dark:fill-gray-600 group-hover:fill-gray-500' }}">
        <path d="M4 5L0.535898 0.5H7.46411L4 5Z"/>
      </svg>
    </span>
  </a>
</th>
