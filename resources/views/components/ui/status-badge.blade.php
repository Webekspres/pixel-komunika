@props([
    'status',
])

@php
    $classes = match ($status) {
        \App\Models\CustomerProfile::ACTIVE => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        \App\Models\CustomerProfile::REJECTED => 'bg-red-50 text-red-600 border-red-200',
        \App\Models\CustomerProfile::SUSPENDED => 'bg-amber-50 text-amber-800 border-amber-200',
        default => 'bg-zinc-100 text-zinc-700 border-zinc-200',
    };
@endphp

<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $classes }}">{{ $status }}</span>
