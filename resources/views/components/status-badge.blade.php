@props(['status'])
@php
    $label = method_exists($status, 'label') ? $status->label() : (string) $status;
    $map = [
        'badge-success'   => 'badge badge-success',
        'badge-danger'    => 'badge badge-error',
        'badge-warning'   => 'badge badge-warning',
        'badge-info'      => 'badge badge-info',
        'badge-primary'   => 'badge badge-brand',
        'badge-secondary' => 'badge badge-gray',
        'badge-dark'      => 'badge badge-dark',
        'badge-muted'     => 'badge badge-gray',
    ];
    $oldClass  = method_exists($status, 'badgeClass') ? $status->badgeClass() : 'badge-secondary';
    $cssClass  = $map[$oldClass] ?? 'badge badge-gray';
@endphp
<span class="{{ $cssClass }}">{{ $label }}</span>
