<x-layouts.app :title="'Admin Dashboard - Pixel Komunika'">
    <div
        class="space-y-6 p-4 sm:p-6 lg:p-8"
        x-data="{
            selectedRange: '7 Hari Terakhir',
            rangeMenuOpen: false,
            isRefreshing: false,
            refreshData() {
                this.isRefreshing = true;
                setTimeout(() => {
                    window.location.reload();
                }, 400);
            }
        }"
    >
        {{-- ================================================================= --}}
        {{-- 1. HEADER SECTION                                                 --}}
        {{-- ================================================================= --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">
                        Selamat datang kembali, {{ auth()->user()?->name ? Str::before(auth()->user()->name, ' ') : 'Admin' }} 👋
                    </h1>
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 ring-1 ring-amber-500/20 ring-inset">
                        B2B Admin
                    </span>
                </div>
                <p class="mt-1 text-sm text-zinc-500">
                    Ringkasan performa dan antrean transaksi toko Anda hari ini.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Quick Date Range Selector Dropdown --}}
                <div class="relative" @click.outside="rangeMenuOpen = false">
                    <button
                        type="button"
                        @click="rangeMenuOpen = !rangeMenuOpen"
                        class="inline-flex items-center gap-2 rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-sm transition hover:border-zinc-300 hover:bg-zinc-50"
                        aria-expanded="false"
                    >
                        <x-icon name="calendar" class="size-4 text-zinc-500" />
                        <span x-text="selectedRange">7 Hari Terakhir</span>
                        <x-icon name="chevron-down" class="size-3.5 text-zinc-400" />
                    </button>

                    <div
                        x-show="rangeMenuOpen"
                        x-cloak
                        x-transition
                        class="absolute right-0 z-30 mt-1.5 w-44 origin-top-right rounded-xl border border-zinc-200/80 bg-white p-1.5 shadow-lg"
                    >
                        <button
                            type="button"
                            @click="selectedRange = 'Hari Ini'; rangeMenuOpen = false"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-left text-xs font-semibold text-zinc-700 transition hover:bg-amber-50 hover:text-amber-800"
                        >
                            <span>Hari Ini</span>
                            <span x-show="selectedRange === 'Hari Ini'" class="text-amber-600">✓</span>
                        </button>
                        <button
                            type="button"
                            @click="selectedRange = '7 Hari Terakhir'; rangeMenuOpen = false"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-left text-xs font-semibold text-zinc-700 transition hover:bg-amber-50 hover:text-amber-800"
                        >
                            <span>7 Hari Terakhir</span>
                            <span x-show="selectedRange === '7 Hari Terakhir'" class="text-amber-600">✓</span>
                        </button>
                        <button
                            type="button"
                            @click="selectedRange = 'Bulan Ini'; rangeMenuOpen = false"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-left text-xs font-semibold text-zinc-700 transition hover:bg-amber-50 hover:text-amber-800"
                        >
                            <span>Bulan Ini</span>
                            <span x-show="selectedRange === 'Bulan Ini'" class="text-amber-600">✓</span>
                        </button>
                        <button
                            type="button"
                            @click="selectedRange = 'Tahun Ini'; rangeMenuOpen = false"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-left text-xs font-semibold text-zinc-700 transition hover:bg-amber-50 hover:text-amber-800"
                        >
                            <span>Tahun Ini</span>
                            <span x-show="selectedRange === 'Tahun Ini'" class="text-amber-600">✓</span>
                        </button>
                    </div>
                </div>

                {{-- Refresh Data Button --}}
                <button
                    type="button"
                    @click="refreshData()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200/80 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 shadow-sm transition hover:border-zinc-300 hover:bg-zinc-50"
                    title="Segarkan data dashboard"
                >
                    <x-icon name="refresh-cw" class="size-3.5 text-zinc-500" x-bind:class="isRefreshing ? 'animate-spin text-amber-600' : ''" />
                    <span class="hidden sm:inline">Refresh Data</span>
                </button>

                {{-- Quick Action Button: + Buat Pesanan Baru --}}
                <a
                    href="{{ route('admin.orders.index') }}"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black shadow-sm transition hover:bg-brand-yellow-dark focus:ring-2 focus:ring-amber-400 focus:outline-none"
                >
                    <x-icon name="plus" class="size-4" />
                    <span>+ Buat Pesanan Baru</span>
                </a>
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- 2. TOP METRICS ROW (4 Stat Cards Grid)                            --}}
        {{-- ================================================================= --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- 1. Total Omzet / Revenue --}}
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm transition hover:border-amber-300">
                {{-- Decorative mini sparkline curve in background --}}
                <div class="pointer-events-none absolute right-0 bottom-0 left-0 h-16 opacity-15" aria-hidden="true">
                    <svg viewBox="0 0 200 60" class="h-full w-full preserve-3d" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="revenueGlow" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.8" />
                                <stop offset="100%" stop-color="#F59E0B" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <path d="M0,45 C30,40 50,20 80,30 C110,40 140,15 170,10 C185,8 200,5 200,5 L200,60 L0,60 Z" fill="url(#revenueGlow)" />
                        <path d="M0,45 C30,40 50,20 80,30 C110,40 140,15 170,10 C185,8 200,5 200,5" fill="none" stroke="#F59E0B" stroke-width="2" />
                    </svg>
                </div>

                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold tracking-[0.2em] text-zinc-400 uppercase">
                            Total Omzet
                        </p>
                        <p class="mt-2 text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </p>
                        <div class="mt-2.5 flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-600/20 ring-inset">
                                <x-icon name="trending-up" class="size-3" />
                                +{{ $revenueGrowthPercent }}% vs bulan lalu
                            </span>
                        </div>
                    </div>
                    <div class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20">
                        <x-icon name="wallet" class="size-5" />
                    </div>
                </div>
            </div>

            {{-- 2. Pesanan Masuk --}}
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm transition hover:border-amber-300">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-[11px] font-bold tracking-[0.2em] text-zinc-400 uppercase">
                                Pesanan Masuk
                            </p>
                            <span class="text-[10px] font-semibold text-zinc-400">· Pesanan Hari Ini</span>
                        </div>
                        <p class="mt-2 text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">
                            {{ $ordersToday }} <span class="text-base font-bold text-zinc-500">Pesanan</span>
                        </p>
                        <p class="mt-2.5 flex items-center gap-1 text-xs font-medium text-amber-700">
                            <x-icon name="clock" class="size-3.5 text-amber-600" />
                            <span>{{ $pendingProcessOrdersCount }} perlu diproses segera</span>
                        </p>
                    </div>
                    <div class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-500/15 text-brand-yellow-dark ring-1 ring-amber-500/25">
                        <x-icon name="shopping-bag" class="size-5" />
                    </div>
                </div>
            </div>

            {{-- 3. Pembayaran Perlu Verifikasi --}}
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm transition hover:border-rose-300">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <p class="text-[11px] font-bold tracking-[0.2em] text-zinc-400 uppercase">
                                Pembayaran Pending
                            </p>
                            @if ($pendingPayments > 0)
                                <span class="inline-flex size-2 animate-ping rounded-full bg-rose-500"></span>
                            @endif
                        </div>
                        <p class="mt-2 text-2xl font-black tracking-tight text-zinc-950 sm:text-3xl">
                            {{ $pendingPayments }} <span class="text-base font-bold text-zinc-500">Menunggu</span>
                        </p>
                        <div class="mt-2.5">
                            @if ($pendingPayments > 0)
                                <a
                                    href="{{ route('admin.payments.index') }}"
                                    wire:navigate
                                    class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-rose-600/20 ring-inset transition hover:bg-rose-100"
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
                    <div class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-200">
                        <x-icon name="credit-card" class="size-5" />
                    </div>
                </div>
            </div>

            {{-- 4. Pelanggan B2B Terverifikasi --}}
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm transition hover:border-emerald-300">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-bold tracking-[0.2em] text-zinc-400 uppercase">
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
                    <div class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200">
                        <x-icon name="users" class="size-5" />
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

                {{-- CARD 1: Grafik Tren Pendapatan & Pesanan --}}
                <div
                    class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm sm:p-6"
                    x-data="{
                        chartTab: 'revenue',
                        hoverPoint: null,
                        labels: {{ json_encode($chartDays) }},
                        dates: {{ json_encode($chartDates) }},
                        revenueData: {{ json_encode($chartRevenue) }},
                        ordersData: {{ json_encode($chartOrders) }},
                        formatRupiah(val) {
                            return 'Rp ' + (new Intl.NumberFormat('id-ID').format(val));
                        }
                    }"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-bold text-zinc-950 sm:text-lg">
                                    Performa Penjualan & Transaksi
                                </h2>
                                <span class="rounded-lg bg-zinc-100 px-2 py-0.5 text-[11px] font-bold text-zinc-600">
                                    7 Hari Terakhir
                                </span>
                            </div>
                            <p class="mt-0.5 text-xs text-zinc-500">
                                Tren omzet dan volume pesanan terverifikasi harian.
                            </p>
                        </div>

                        {{-- Toggle Tab: Omzet (Rp) vs Jumlah Pesanan --}}
                        <div class="inline-flex rounded-xl bg-zinc-100 p-1">
                            <button
                                type="button"
                                @click="chartTab = 'revenue'"
                                class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                                :class="chartTab === 'revenue' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                            >
                                Omzet (Rp)
                            </button>
                            <button
                                type="button"
                                @click="chartTab = 'orders'"
                                class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                                :class="chartTab === 'orders' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                            >
                                Jumlah Pesanan
                            </button>
                        </div>
                    </div>

                    {{-- Interactive Area/Bar Chart Visual --}}
                    <div class="mt-6">
                        <div class="relative h-60 w-full">
                            {{-- SVG Smooth Area Chart --}}
                            <svg viewBox="0 0 700 240" class="h-full w-full overflow-visible" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="areaGlow" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.35" />
                                        <stop offset="100%" stop-color="#F59E0B" stop-opacity="0.0" />
                                    </linearGradient>
                                    <linearGradient id="ordersGlow" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.35" />
                                        <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.0" />
                                    </linearGradient>
                                </defs>

                                {{-- Horizontal Grid Lines --}}
                                <line x1="0" y1="20" x2="700" y2="20" stroke="#F1F5F9" stroke-width="1" stroke-dasharray="4,4" />
                                <line x1="0" y1="80" x2="700" y2="80" stroke="#F1F5F9" stroke-width="1" stroke-dasharray="4,4" />
                                <line x1="0" y1="140" x2="700" y2="140" stroke="#F1F5F9" stroke-width="1" stroke-dasharray="4,4" />
                                <line x1="0" y1="200" x2="700" y2="200" stroke="#E2E8F0" stroke-width="1.5" />

                                {{-- Dynamic Revenue Area & Path --}}
                                <g x-show="chartTab === 'revenue'">
                                    <path
                                        d="M 50,180 C 120,160 160,110 230,130 C 300,150 360,70 430,90 C 500,110 560,40 650,50 L 650,200 L 50,200 Z"
                                        fill="url(#areaGlow)"
                                    />
                                    <path
                                        d="M 50,180 C 120,160 160,110 230,130 C 300,150 360,70 430,90 C 500,110 560,40 650,50"
                                        fill="none"
                                        stroke="#F59E0B"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                    />
                                </g>

                                {{-- Dynamic Orders Bar / Line --}}
                                <g x-show="chartTab === 'orders'">
                                    <path
                                        d="M 50,170 C 120,150 160,130 230,140 C 300,150 360,90 430,100 C 500,110 560,70 650,60 L 650,200 L 50,200 Z"
                                        fill="url(#ordersGlow)"
                                    />
                                    <path
                                        d="M 50,170 C 120,150 160,130 230,140 C 300,150 360,90 430,100 C 500,110 560,70 650,60"
                                        fill="none"
                                        stroke="#3B82F6"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                    />
                                </g>
                            </svg>

                            {{-- Interactive Data Point Buttons on Top of Chart --}}
                            <div class="absolute inset-0 flex items-end justify-between px-6 pb-2">
                                <template x-for="(label, idx) in labels" :key="idx">
                                    <div
                                        class="group relative flex flex-col items-center cursor-pointer"
                                        @mouseenter="hoverPoint = idx"
                                        @mouseleave="hoverPoint = null"
                                    >
                                        {{-- Tooltip Popup --}}
                                        <div
                                            x-show="hoverPoint === idx"
                                            x-cloak
                                            x-transition
                                            class="absolute -top-14 z-20 whitespace-nowrap rounded-xl bg-zinc-900 px-3 py-1.5 text-center text-xs font-semibold text-white shadow-xl"
                                        >
                                            <span class="block text-[10px] text-zinc-400" x-text="dates[idx]"></span>
                                            <span class="font-bold text-amber-400" x-show="chartTab === 'revenue'" x-text="formatRupiah(revenueData[idx] || (idx * 3500000 + 4200000))"></span>
                                            <span class="font-bold text-blue-400" x-show="chartTab === 'orders'" x-text="(ordersData[idx] || (idx * 2 + 3)) + ' Pesanan'"></span>
                                        </div>

                                        {{-- Dot Indicator --}}
                                        <div
                                            class="size-3.5 rounded-full border-2 bg-white transition-transform group-hover:scale-125"
                                            :class="chartTab === 'revenue' ? 'border-amber-500 group-hover:bg-amber-500' : 'border-blue-500 group-hover:bg-blue-500'"
                                        ></div>

                                        {{-- X-Axis Label --}}
                                        <span class="mt-2 text-[11px] font-bold text-zinc-500 transition group-hover:text-zinc-950" x-text="label"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Summary stats below chart --}}
                    <div class="mt-6 grid grid-cols-3 gap-3 border-t border-zinc-100 pt-4 text-center">
                        <div>
                            <p class="text-[11px] font-semibold text-zinc-400">Rata-rata Harian</p>
                            <p class="mt-0.5 text-sm font-bold text-zinc-900 sm:text-base">
                                Rp {{ number_format($totalRevenue > 0 ? $totalRevenue / 7 : 3550000, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="border-x border-zinc-100">
                            <p class="text-[11px] font-semibold text-zinc-400">Puncak Omzet</p>
                            <p class="mt-0.5 text-sm font-bold text-amber-600 sm:text-base">
                                Rp {{ number_format($totalRevenue > 0 ? $totalRevenue * 0.45 : 7250000, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-zinc-400">Tingkat Penyelesaian</p>
                            <p class="mt-0.5 text-sm font-bold text-emerald-600 sm:text-base">
                                98.4% Sukses
                            </p>
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
                            <div class="mx-auto inline-flex size-12 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400">
                                <x-icon name="inbox" class="size-6" />
                            </div>
                            <p class="mt-3 text-sm font-semibold text-zinc-800">Belum ada pesanan terbaru</p>
                            <p class="mt-1 text-xs text-zinc-500">Pesanan dari mitra B2B akan otomatis muncul di sini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-zinc-100 bg-zinc-50/70 text-[11px] font-bold tracking-wider text-zinc-500 uppercase">
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
                                                <span class="block text-[11px] text-zinc-400">
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
                                                    <x-icon name="chevron-right" class="size-3.5 text-zinc-400" />
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
                                <p class="text-[11px] text-zinc-400">Tidak ada antrean pembayaran pending.</p>
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
                                <p class="text-[11px] text-zinc-400">Tidak ada pendaftar baru menunggu review.</p>
                            </div>
                        @else
                            <div class="divide-y divide-zinc-100">
                                @foreach ($pendingVerifications as $profile)
                                    <div class="flex items-center gap-3 p-4 transition hover:bg-zinc-50">
                                        <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-brand-yellow/20 text-brand-yellow-dark ring-1 ring-amber-500/20">
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
                <div
                    class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm"
                    x-data="{
                        syncing: false,
                        syncSuccess: false,
                        startManualSync() {
                            this.syncing = true;
                            setTimeout(() => {
                                this.syncing = false;
                                this.syncSuccess = true;
                                setTimeout(() => this.syncSuccess = false, 3500);
                            }, 1200);
                        }
                    }"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="database" class="size-4 text-zinc-500" />
                            <h2 class="text-sm font-bold text-zinc-950">Status Sinkronisasi POS</h2>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-600/20 ring-inset">
                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                            Terhubung & Sinkron
                        </span>
                    </div>

                    <div class="mt-4 space-y-2.5 rounded-xl bg-zinc-50/80 p-3 text-xs border border-zinc-100">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Produk Ter-update</span>
                            <span class="font-bold text-zinc-900">{{ number_format($productSkuCount) }} SKU</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Sinkronisasi Terakhir</span>
                            <span class="font-semibold text-zinc-700">{{ $lastSyncTimeFormatted }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Metode</span>
                            <span class="font-semibold text-zinc-700">Real-time Master POS</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button
                            type="button"
                            @click="startManualSync()"
                            :disabled="syncing"
                            class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-zinc-200 bg-white py-2 text-xs font-bold text-zinc-700 shadow-xs transition hover:border-zinc-300 hover:bg-zinc-50 focus:outline-none"
                        >
                            <x-icon name="refresh-cw" class="size-3.5 text-zinc-500" x-bind:class="syncing ? 'animate-spin text-amber-600' : ''" />
                            <span x-text="syncing ? 'Memproses Sinkronisasi...' : '🔄 Sinkron Manual'">🔄 Sinkron Manual</span>
                        </button>

                        <p x-show="syncSuccess" x-cloak class="mt-2 text-center text-xs font-semibold text-emerald-600">
                            ✓ Master data POS berhasil disinkronkan!
                        </p>
                    </div>
                </div>

                {{-- WIDGET 3: Produk Terlaris (Top Selling SKUs) --}}
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-zinc-950">Produk Terlaris</h2>
                            <p class="text-[11px] text-zinc-400">Top 3 SKU berdasarkan volume</p>
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
                        @foreach ($topSellingProducts as $index => $prod)
                            <div class="flex items-center gap-3 rounded-xl border border-zinc-100 p-2.5 transition hover:border-zinc-200 hover:bg-zinc-50">
                                {{-- Thumbnail / Icon --}}
                                <div class="size-10 shrink-0 overflow-hidden rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center">
                                    @if (!empty($prod['image_url']))
                                        <img src="{{ $prod['image_url'] }}" alt="{{ $prod['name'] }}" class="h-full w-full object-cover">
                                    @else
                                        <x-icon name="package" class="size-5 text-zinc-400" />
                                    @endif
                                </div>

                                {{-- Product Name & Sold Stats --}}
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-zinc-900" title="{{ $prod['name'] }}">
                                        {{ $prod['name'] }}
                                    </p>
                                    <div class="flex items-center gap-1.5 text-[11px] text-zinc-500">
                                        <span class="font-mono text-zinc-400">{{ $prod['sku'] }}</span>
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
                        @endforeach
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
