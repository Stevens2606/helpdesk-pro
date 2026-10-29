New-Item -ItemType Directory -Force resources\views\components | Out-Null

@'
@props(['status'])
@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $style = match ($value) {
        'open'        => 'bg-blue-100 text-blue-800',
        'in_progress' => 'bg-yellow-100 text-yellow-800',
        'resolved'    => 'bg-green-100 text-green-800',
        default       => 'bg-gray-100 text-gray-700',
    };
@endphp
<span {{ $attributes->merge(['class' => "px-2 py-1 rounded-full text-xs font-medium $style"]) }}>
    {{ str($value)->replace('_', ' ')->title() }}
</span>
'@ | Set-Content -Encoding utf8 resources\views\components\status-badge.blade.php

@'
@props(['priority'])
@php
    $style = match (strtolower((string) $priority)) {
        'urgent', 'high' => 'bg-red-100 text-red-800',
        'medium'         => 'bg-orange-100 text-orange-800',
        default          => 'bg-slate-100 text-slate-700',
    };
@endphp
<span {{ $attributes->merge(['class' => "px-2 py-1 rounded-full text-xs font-medium $style"]) }}>
    {{ ucfirst((string) $priority) }}
</span>
'@ | Set-Content -Encoding utf8 resources\views\components\priority-badge.blade.php