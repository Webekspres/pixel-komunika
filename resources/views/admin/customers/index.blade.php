@php
    $statusLabels = [
        \App\Models\CustomerProfile::PENDING => 'Menunggu Verifikasi',
        \App\Models\CustomerProfile::ACTIVE => 'Aktif',
        \App\Models\CustomerProfile::REJECTED => 'Ditolak',
        \App\Models\CustomerProfile::SUSPENDED => 'Ditangguhkan',
    ];
    $statusClasses = [
        \App\Models\CustomerProfile::PENDING => 'bg-orange-100 text-orange-700',
        \App\Models\CustomerProfile::ACTIVE => 'bg-emerald-100 text-emerald-700',
        \App\Models\CustomerProfile::REJECTED => 'bg-red-100 text-red-700',
        \App\Models\CustomerProfile::SUSPENDED => 'bg-zinc-100 text-zinc-600',
    ];
@endphp

<x-layouts.app :title="'Pelanggan - Pixel Komunika'">
    <div class="space-y-5 p-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-xl font-black text-zinc-900">Pelanggan</h1>
            <p class="text-sm text-zinc-500">{{ number_format($statusCounts['all']) }} total pelanggan</p>
        </div>

        <div class="rounded-2xl border border-neutral-100 bg-white">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="px-4 pt-4">
                @if ($selectedStatus !== '')
                    <input type="hidden" name="status" value="{{ $selectedStatus }}">
                @endif
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-zinc-400" />
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari nama, usaha, email..."
                        class="w-full rounded-xl border border-neutral-200 py-2.5 pr-4 pl-10 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                    >
                </div>
            </form>

            <div class="mt-3 flex gap-0.5 overflow-x-auto px-4">
                @foreach ($tabs as $tab)
                    @php
                        $isActive = $selectedStatus === $tab['key'];
                        $count = $statusCounts[$tab['countKey']] ?? 0;
                        $href = route('admin.customers.index', array_filter([
                            'status' => $tab['key'] !== '' ? $tab['key'] : null,
                            'q' => $search !== '' ? $search : null,
                        ]));
                    @endphp
                    <a
                        href="{{ $href }}"
                        wire:navigate
                        class="flex items-center gap-1.5 whitespace-nowrap border-b-2 px-3 py-2 text-xs font-semibold transition-colors {{ $isActive ? 'border-brand-yellow text-zinc-900' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}"
                    >
                        {{ $tab['label'] }}
                        <span class="rounded-full px-1.5 py-0.5 text-[10px] {{ $isActive ? 'bg-brand-yellow/20 text-brand-yellow-dark' : 'bg-neutral-100 text-zinc-400' }}">
                            {{ $count }}
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-t border-neutral-100">
                            @foreach (['Pelanggan', 'Email', 'Telepon', 'Kota', 'Status', 'Terdaftar', ''] as $heading)
                                <th class="px-5 py-3 text-left text-xs font-semibold tracking-wide whitespace-nowrap text-zinc-400 uppercase">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse ($customers as $customer)
                            @php
                                $city = $customer->user?->addresses->firstWhere('is_default', true)?->city_name
                                    ?? $customer->user?->addresses->first()?->city_name
                                    ?? '—';
                                $label = $statusLabels[$customer->verification_status] ?? $customer->verification_status;
                                $badgeClass = $statusClasses[$customer->verification_status] ?? 'bg-zinc-100 text-zinc-700';
                            @endphp
                            <tr class="transition-colors hover:bg-neutral-50">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-yellow/15">
                                            <span class="text-xs font-bold text-brand-yellow-dark">{{ strtoupper(substr($customer->user?->name ?? '?', 0, 1)) }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-zinc-900">{{ $customer->user?->name }}</p>
                                            <p class="truncate text-xs text-zinc-400">{{ $customer->business_name ?: 'Usaha belum diisi' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $customer->user?->email }}</td>
                                <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $customer->user?->phone ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $city }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badgeClass }}">{{ $label }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-xs whitespace-nowrap text-zinc-500">
                                    {{ $customer->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <a
                                        href="{{ route('admin.customers.show', $customer) }}"
                                        wire:navigate
                                        class="inline-flex items-center gap-1 whitespace-nowrap text-xs font-semibold text-zinc-500 transition hover:text-brand-black"
                                    >
                                        Detail
                                        <x-icon name="arrow-right" class="size-3.5" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-zinc-400">
                                    Tidak ada pelanggan yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div class="border-t border-neutral-100 px-5 py-4">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
