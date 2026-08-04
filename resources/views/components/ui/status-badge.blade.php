@props([
    'status',
])

@php
    $color = match ($status) {
        \App\Models\CustomerProfile::ACTIVE => 'green',
        \App\Models\CustomerProfile::REJECTED => 'red',
        \App\Models\CustomerProfile::SUSPENDED => 'amber',
        default => 'zinc',
    };
@endphp

<flux:badge :color="$color" rounded size="sm">{{ $status }}</flux:badge>
