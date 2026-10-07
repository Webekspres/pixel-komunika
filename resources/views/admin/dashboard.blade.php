<x-layouts.app :title="'Admin Dashboard - Pixel Komunika'">
    <div
        class="space-y-6 p-4 sm:p-6 lg:p-8"

    >
        {{-- ================================================================= --}}
        {{-- 1. HEADER SECTION                                                 --}}
        {{-- ================================================================= --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">Dashboard</h1>
                <p class="mt-1 text-sm text-zinc-600">
                    Antrean yang perlu ditindak dan ringkasan penjualan. Grafik mencakup 7 hari terakhir.
                </p>
            </div>

            <a
                href="{{ route('admin.dashboard') }}"
                class="inline-flex min-h-11 items-center gap-1.5 self-start rounded-xl border border-zinc-200 bg-white px-3.5 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-50 sm:self-auto"
            >
                <x-icon name="refresh-cw" class="size-3.5 text-zinc-500" />
                Muat ulang data
            </a>
        </div>

        {{-- ================================================================= --}}
        {{-- 2. TOP METRICS ROW (4 Stat Cards Grid)                            --}}
        {{-- ================================================================= --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- 1. Total Omzet / Revenue --}}
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-5 ">
                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold text-zinc-600">
                            Total omzet (semua waktu)
                        </p>
                        <p class="mt-2 text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </p>
                        <div class="mt-2.5 flex items-center gap-1.5">
                            @if ($revenueGrowthPercent === null)
                                <span class="text-xs font-medium text-zinc-600">Belum ada omzet bulan lalu untuk dibandingkan</span>
                            @else
                                <span @class([
                                    'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-bold ring-1 ring-inset',
                                    'bg-emerald-50 text-emerald-700 ring-emerald-600/20' => $revenueGrowthPercent >= 0,
                                    'bg-rose-50 text-rose-700 ring-rose-600/20' => $revenueGrowthPercent < 0,
                                ])>
                                    <x-icon :name="$revenueGrowthPercent >= 0 ? 'trending-up' : 'trending-down'" class="size-3" />
                                    Bulan ini {{ $revenueGrowthPercent > 0 ? '+' : '' }}{{ $revenueGrowthPercent }}% vs bulan lalu
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Pesanan Masuk --}}
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-5 ">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-zinc-600">Pesanan Hari Ini</p>
                        <p class="mt-2 text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">
                            {{ $ordersToday }} <span class="text-base font-bold text-zinc-500">Pesanan</span>
                        </p>
                        <p class="mt-2.5 flex items-center gap-1 text-xs font-medium text-amber-700">
                            <x-icon name="clock" class="size-3.5 text-amber-600" />
                            <span>{{ $pendingProcessOrdersCount }} perlu diproses segera</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- 3. Pembayaran Perlu Verifikasi --}}
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-5 ">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <p class="text-xs font-bold text-zinc-600">
                                Pembayaran Pending
                            </p>
                        </div>
                        <p class="mt-2 text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">
                            {{ $pendingPayments }} <span class="text-base font-bold text-zinc-500">Menunggu</span>
                        </p>
                        <div class="mt-2.5">
                            @if ($pendingPayments > 0)
                                <a
                                    href="{{ route('admin.payments.index') }}"
                                    wire:navigate
                                    class="inline-flex min-h-11 items-center gap-1 rounded-full bg-rose-50 px-3 text-xs font-bold text-rose-700 ring-1 ring-rose-600/20 ring-inset transition hover:bg-rose-100"
                                >
                                    <x-icon name="alert-circle" class="size-3" />
                                    Segera Periksa
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600">
                                    <x-icon name="circle-check" class="size-3.5" />
                                    Semua beres
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Pelanggan B2B Terverifikasi --}}
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-5 ">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-zinc-600">
                            Pelanggan Aktif
                        </p>
                        <p class="mt-2 text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">
                            {{ $activeCustomers }} <span class="text-base font-bold text-zinc-500">Toko Aktif</span>
                        </p>
                        <p class="mt-2.5 flex items-center gap-1 text-xs font-semibold text-emerald-700">
                            <x-icon name="user-check" class="size-3.5" />
                            <span>{{ $activeCustomersTrend }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- 3. MAIN BODY: 2-COLUMN GRID (~65% vs ~35%)                        --}}
        {{-- ================================================================= --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

            {{-- ============================================================= --}}
            {{-- LEFT COLUMN (~65% / 8 Cols): Charts & Recent Orders           --}}
            {{-- ============================================================= --}}
            <div class="space-y-6 lg:col-span-8">

                {{-- CARD 1: Omzet & pesanan per hari, dari data nyata (bar = nilai hari itu) --}}
                @php
                    $shortRupiah = fn (float $v) => $v >= 1_000_000
                        ? str_replace('.', ',', rtrim(rtrim(number_format($v / 1_000_000, 1, '.', ''), '0'), '.')).' jt'
                        : ($v >= 1_000 ? number_format($v / 1_000, 0, ',', '.').' rb' : number_format($v, 0, ',', '.'));
                    $series = [
                        'revenue' => ['values' => $chartRevenue, 'format' => $shortRupiah, 'empty' => 'Belum ada omzet terverifikasi dalam 7 hari terakhir.'],
                        'orders' => ['values' => $chartOrders, 'format' => fn ($v) => (string) $v, 'empty' => 'Belum ada pesanan dalam 7 hari terakhir.'],
                    ];
                @endphp
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 sm:p-6" x-data="{ chartTab: 'revenue' }">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-zinc-950 sm:text-lg" x-text="chartTab === 'revenue' ? 'Omzet terverifikasi per hari, 7 hari terakhir' : 'Jumlah pesanan per hari, 7 hari terakhir'">Omzet terverifikasi per hari, 7 hari terakhir</h2>
                        </div>
                        <div class="inline-flex rounded-xl bg-zinc-100 p-1" role="group" aria-label="Pilih data grafik">
                            <button type="button" @click="chartTab = 'revenue'" :aria-pressed="chartTab === 'revenue'" class="min-h-11 rounded-lg px-3 text-xs font-bold transition" :class="chartTab === 'revenue' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-600 hover:text-zinc-900'">Omzet (Rp)</button>
                            <button type="button" @click="chartTab = 'orders'" :aria-pressed="chartTab === 'orders'" class="min-h-11 rounded-lg px-3 text-xs font-bold transition" :class="chartTab === 'orders' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-600 hover:text-zinc-900'">Jumlah Pesanan</button>
                        </div>
                    </div>

                    @foreach ($series as $key => $set)
                        @php $max = max($set['values']) ?: 0; @endphp
                        <div x-show="chartTab === '{{ $key }}'" @if ($key !== 'revenue') x-cloak @endif class="mt-6">
                            @if ($max <= 0)
                                <p class="rounded-xl bg-zinc-50 px-4 py-10 text-center text-sm text-zinc-600">{{ $set['empty'] }}</p>
                            @else
                                <ol class="grid h-56 grid-cols-7 items-end gap-2 sm:gap-4">
                                    @foreach ($set['values'] as $i => $value)
                                        <li class="flex h-full flex-col items-center justify-end gap-1.5">
                                            <span class="text-[11px] font-semibold text-zinc-700">{{ ($set['format'])($value) }}</span>
                                            <span class="w-full rounded-t-md {{ $value > 0 ? 'bg-brand-yellow' : 'bg-zinc-200' }}" style="height: {{ $value > 0 ? max(4, round($value / $max * 100)) : 2 }}%" aria-hidden="true"></span>
                                            <span class="text-xs font-semibold text-zinc-600" title="{{ $chartDates[$i] }}">{{ $chartDays[$i] }}</span>
                                            <span class="sr-only">{{ $chartDates[$i] }}: {{ ($set['format'])($value) }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>
                    @endforeach

                    <div class="mt-6 grid grid-cols-2 gap-3 border-t border-zinc-100 pt-4 text-center">
                        <div>
                            <p class="text-xs font-semibold text-zinc-600">Rata-rata omzet harian</p>
                            <p class="mt-0.5 text-sm font-bold text-zinc-900 sm:text-base">
                                Rp {{ number_format(array_sum($chartRevenue) / 7, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="border-l border-zinc-100">
                            <p class="text-xs font-semibold text-zinc-600">Omzet harian tertinggi</p>
                            <p class="mt-0.5 text-sm font-bold text-zinc-900 sm:text-base">
                                Rp {{ number_format(max($chartRevenue), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                    </div>
                </div>

                {{-- CARD 2: Pesanan Terbaru (Recent Orders Table) --}}
                <div class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 sm:px-6">
                        <div>
                            <h2 class="text-base font-bold text-zinc-950">Pesanan Terbaru</h2>
                            <p class="mt-0.5 text-xs text-zinc-500">Daftar transaksi B2B teranyar yang masuk ke sistem.</p>
                        </div>
                        <a
                            href="{{ route('admin.orders.index') }}"
                            wire:navigate
                            class="inline-flex items-center gap-1 rounded-xl bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-800 transition hover:bg-amber-100 hover:text-amber-900"
                        >
                            <span>Lihat Semua Pesanan</span>
                            <x-icon name="arrow-right" class="size-3.5" />
                        </a>
                    </div>

                    @if ($recentOrders->isEmpty())
                        <div class="px-6 py-12 text-center">
                            <div class="mx-auto inline-flex size-12 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-500">
                                <x-icon name="inbox" class="size-6" />
                            </div>
                            <p class="mt-3 text-sm font-semibold text-zinc-800">Belum ada pesanan terbaru</p>
                            <p class="mt-1 text-xs text-zinc-500">Pesanan dari mitra B2B akan otomatis muncul di sini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-zinc-100 bg-zinc-50/70 text-xs font-bold text-zinc-500">
                                        <th class="px-5 py-3 sm:px-6">No. Order</th>
                                        <th class="px-4 py-3">Pelanggan (Nama Toko)</th>
                                        <th class="hidden px-4 py-3 md:table-cell">Item Ringkas</th>
                                        <th class="px-4 py-3 text-right">Total Tagihan</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-5 py-3 text-right sm:px-6">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100">
                                    @foreach ($recentOrders as $order)
                                        @php
                                            $firstItem = $order->items->first();
                                            $itemsCount = $order->items->count();
                                            $itemsSummary = $firstItem
                                                ? ($firstItem->product_name ?? 'Item').($itemsCount > 1 ? ' (+'.($itemsCount - 1).' item)' : '')
                                                : 'Aksesoris B2B';
                                            $storeName = $order->user?->customerProfile?->business_name;
                                        @endphp
                                        <tr class="transition hover:bg-zinc-50/80">
                                            {{-- No. Order --}}
                                            <td class="px-5 py-3.5 whitespace-nowrap sm:px-6">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-bold text-zinc-900 hover:text-amber-600">
                                                    {{ $order->order_number }}
                                                </a>
                                                <span class="block text-[11px] text-zinc-500">
                                                    {{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                                </span>
                                            </td>

                                            {{-- Pelanggan (Nama Toko) --}}
                                            <td class="px-4 py-3.5">
                                                <p class="font-bold text-zinc-900 truncate max-w-[160px]">
                                                    {{ $storeName ?: ($order->user?->name ?? $order->recipient_name) }}
                                                </p>
                                                @if ($storeName && $order->user?->name)
                                                    <span class="text-[11px] text-zinc-500 truncate block max-w-[160px]">
                                                        {{ $order->user->name }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Item Ringkas --}}
                                            <td class="hidden px-4 py-3.5 text-zinc-600 md:table-cell">
                                                <p class="max-w-[200px] truncate text-zinc-700" title="{{ $itemsSummary }}">
                                                    {{ $itemsSummary }}
                                                </p>
                                            </td>

                                            {{-- Total Tagihan --}}
                                            <td class="px-4 py-3.5 text-right font-bold text-zinc-950 whitespace-nowrap">
                                                Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                            </td>

                                            {{-- Status Badge --}}
                                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ \App\Services\Admin\AdminDashboardService::orderStatusClasses($order->status) }}">
                                                    {{ \App\Services\Admin\AdminDashboardService::orderStatusLabel($order->status) }}
                                                </span>
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="px-5 py-3.5 text-right whitespace-nowrap sm:px-6">
                                                <a
                                                    href="{{ route('admin.orders.show', $order) }}"
                                                    class="inline-flex items-center gap-1 rounded-xl border border-zinc-200 bg-white px-2.5 py-1.5 text-xs font-bold text-zinc-700 shadow-xs transition hover:border-amber-400 hover:bg-amber-50 hover:text-amber-800"
                                                >
                                                    <span>Detail</span>
                                                    <x-icon name="chevron-right" class="size-3.5 text-zinc-500" />
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

            {{-- ============================================================= --}}
            {{-- RIGHT COLUMN (~35% / 4 Cols): Actionable Tasks, Sync, Top SKUs--}}
            {{-- ============================================================= --}}
            <div class="space-y-6 lg:col-span-4">

                {{-- WIDGET 1: Antrean Verifikasi & Tindakan Prioritas (Actionable Tasks) --}}
                <div
                    class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm"
                    x-data="{ activeActionTab: 'payments' }"
                >
                    <div class="border-b border-zinc-100 bg-zinc-50/50 p-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold text-zinc-950">
                                Tindakan Prioritas
                            </h2>
                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-bold text-rose-700">
                                {{ $pendingPayments + $pendingVerificationCount }} Antrean
                            </span>
                        </div>

                        {{-- Action Mini Tabs --}}
                        <div class="mt-3 flex rounded-xl bg-zinc-200/70 p-1 text-xs">
                            <button
                                type="button"
                                @click="activeActionTab = 'payments'"
                                class="flex-1 rounded-lg py-1.5 font-bold transition text-center"
                                :class="activeActionTab === 'payments' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-600 hover:text-zinc-900'"
                            >
                                Bukti Bayar ({{ $pendingPayments }})
                            </button>
                            <button
                                type="button"
                                @click="activeActionTab = 'customers'"
                                class="flex-1 rounded-lg py-1.5 font-bold transition text-center"
                                :class="activeActionTab === 'customers' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-600 hover:text-zinc-900'"
                            >
                                Verifikasi Pelanggan ({{ $pendingVerificationCount }})
                            </button>
                        </div>
                    </div>

                    {{-- TAB 1: Bukti Bayar Pending --}}
                    <div x-show="activeActionTab === 'payments'">
                        @if ($pendingPaymentProofs->isEmpty())
                            <div class="px-5 py-8 text-center">
                                <x-icon name="circle-check" class="mx-auto size-7 text-emerald-500" />
                                <p class="mt-2 text-xs font-semibold text-zinc-800">Semua bukti bayar terverifikasi</p>
                                <p class="text-[11px] text-zinc-500">Tidak ada antrean pembayaran pending.</p>
                            </div>
                        @else
                            <div class="divide-y divide-zinc-100">
                                @foreach ($pendingPaymentProofs as $proof)
                                    <div class="flex items-center gap-3 p-4 transition hover:bg-zinc-50">
                                        <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-200">
                                            <x-icon name="receipt" class="size-4.5" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-xs font-bold text-zinc-900">
                                                {{ $proof->order?->order_number ?? 'Order #'.$proof->order_id }}
                                            </p>
                                            <p class="truncate text-[11px] text-zinc-500">
                                                {{ $proof->bank_name }} · Rp {{ number_format($proof->amount, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <a
                                            href="{{ route('admin.payments.index') }}"
                                            wire:navigate
                                            class="inline-flex shrink-0 items-center rounded-xl bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-800 ring-1 ring-amber-500/20 ring-inset transition hover:bg-amber-100 hover:text-amber-900"
                                        >
                                            Periksa
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- TAB 2: Verifikasi Pelanggan Baru --}}
                    <div x-show="activeActionTab === 'customers'" x-cloak>
                        @if ($pendingVerifications->isEmpty())
                            <div class="px-5 py-8 text-center">
                                <x-icon name="circle-check" class="mx-auto size-7 text-emerald-500" />
                                <p class="mt-2 text-xs font-semibold text-zinc-800">Semua verifikasi selesai</p>
                                <p class="text-[11px] text-zinc-500">Tidak ada pendaftar baru menunggu review.</p>
                            </div>
                        @else
                            <div class="divide-y divide-zinc-100">
                                @foreach ($pendingVerifications as $profile)
                                    <div class="flex items-center gap-3 p-4 transition hover:bg-zinc-50">
                                        <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-brand-yellow/20 text-amber-800 ring-1 ring-amber-500/20">
                                            <span class="text-xs font-black">{{ strtoupper(substr($profile->user?->name ?? '?', 0, 1)) }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-xs font-bold text-zinc-900">{{ $profile->user?->name }}</p>
                                            <p class="truncate text-[11px] text-zinc-500">{{ $profile->business_name ?: 'Toko belum diisi' }}</p>
                                        </div>
                                        <a
                                            href="{{ route('admin.customers.show', $profile) }}"
                                            wire:navigate
                                            class="inline-flex shrink-0 items-center rounded-xl bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-800 ring-1 ring-amber-500/20 ring-inset transition hover:bg-amber-100 hover:text-amber-900"
                                        >
                                            Periksa
                                        </a>
                                    </div>
                                @endforeach
                                @if ($pendingVerificationCount > 4)
                                    <div class="bg-zinc-50 p-2.5 text-center">
                                        <a href="{{ route('admin.customers.index', ['status' => \App\Models\CustomerProfile::PENDING]) }}" wire:navigate class="text-xs font-bold text-zinc-600 hover:text-amber-700">
                                            Lihat +{{ $pendingVerificationCount - 4 }} verifikasi lainnya →
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- WIDGET 2: Status Integrasi & Sinkronisasi POS --}}
                @php
                    [$syncLabel, $syncBadge] = match (true) {
                        $posDriver === 'sample' => ['Data contoh', 'bg-zinc-100 text-zinc-700 ring-zinc-500/20'],
                        $lastSyncStatus === 'SUCCEEDED' => ['Sinkron', 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
                        $lastSyncStatus === 'FAILED' => ['Sinkron gagal', 'bg-rose-50 text-rose-700 ring-rose-600/20'],
                        $lastSyncStatus === 'RUNNING' => ['Sedang sinkron', 'bg-amber-50 text-amber-700 ring-amber-600/20'],
                        default => ['Belum pernah sinkron', 'bg-zinc-100 text-zinc-700 ring-zinc-500/20'],
                    };
                @endphp
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="database" class="size-4 text-zinc-500" />
                            <h2 class="text-sm font-bold text-zinc-950">Status Sinkronisasi POS</h2>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-bold ring-1 ring-inset {{ $syncBadge }}">
                            {{ $syncLabel }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-2.5 rounded-xl bg-zinc-50/80 p-3 text-xs border border-zinc-100">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Jumlah Produk</span>
                            <span class="font-bold text-zinc-900">{{ number_format($productSkuCount) }} SKU</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Sinkronisasi Terakhir</span>
                            <span class="font-semibold text-zinc-700">{{ $lastSyncTimeFormatted }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Jadwal</span>
                            <span class="font-semibold text-zinc-700">Otomatis harian 01:00 WIB</span>
                        </div>
                    </div>
                </div>

                {{-- WIDGET 3: Produk Terlaris (Top Selling SKUs) --}}
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-zinc-950">Produk Terlaris</h2>
                            <p class="text-[11px] text-zinc-500">Top 3 SKU berdasarkan volume</p>
                        </div>
                        <a
                            href="{{ route('admin.products.index') }}"
                            wire:navigate
                            class="text-xs font-bold text-amber-700 hover:underline"
                        >
                            Katalog →
                        </a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($topSellingProducts as $index => $prod)
                            <div class="flex items-center gap-3 rounded-xl border border-zinc-100 p-2.5 transition hover:border-zinc-200 hover:bg-zinc-50">
                                {{-- Thumbnail / Icon --}}
                                <div class="size-10 shrink-0 overflow-hidden rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center">
                                    @if (!empty($prod['image_url']))
                                        <img src="{{ $prod['image_url'] }}" alt="{{ $prod['name'] }}" class="h-full w-full object-cover">
                                    @else
                                        <x-icon name="package" class="size-5 text-zinc-500" />
                                    @endif
                                </div>

                                {{-- Product Name & Sold Stats --}}
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-zinc-900" title="{{ $prod['name'] }}">
                                        {{ $prod['name'] }}
                                    </p>
                                    <div class="flex items-center gap-1.5 text-[11px] text-zinc-500">
                                        <span class="font-mono text-zinc-500">{{ $prod['sku'] }}</span>
                                        <span>·</span>
                                        <span class="font-semibold text-emerald-600">{{ $prod['sold'] }} unit terjual</span>
                                    </div>
                                </div>

                                {{-- Total Revenue from SKU --}}
                                <div class="text-right">
                                    <p class="text-xs font-bold text-zinc-950">
                                        Rp {{ number_format($prod['revenue'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-zinc-200 p-4 text-center text-xs text-zinc-500">Belum ada penjualan terkirim.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Nav Link Shortcuts (Verifikasi Bayar, Laporan) --}}
                <div class="grid grid-cols-2 gap-3">
                    <a
                        href="{{ route('admin.payments.index') }}"
                        wire:navigate
                        class="flex flex-col items-center gap-1.5 rounded-2xl border border-zinc-200/80 bg-white p-4 shadow-sm transition hover:border-amber-400 hover:bg-amber-50/50"
                    >
                        <div class="inline-flex size-9 items-center justify-center rounded-xl bg-orange-50 text-orange-600 ring-1 ring-orange-200">
                            <x-icon name="credit-card" class="size-4.5" />
                        </div>
                        <span class="text-xs font-bold text-zinc-800">Verifikasi Bayar</span>
                    </a>
                    <a
                        href="{{ route('admin.reports.index') }}"
                        wire:navigate
                        class="flex flex-col items-center gap-1.5 rounded-2xl border border-zinc-200/80 bg-white p-4 shadow-sm transition hover:border-amber-400 hover:bg-amber-50/50"
                    >
                        <div class="inline-flex size-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                            <x-icon name="bar-chart-3" class="size-4.5" />
                        </div>
                        <span class="text-xs font-bold text-zinc-800">Laporan</span>
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
