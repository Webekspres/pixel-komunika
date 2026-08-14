<x-layouts.app :title="'Admin Dashboard - Pixel Komunika'">
    <div class="space-y-6 p-6">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Dashboard</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Selamat datang kembali. Berikut ringkasan hari ini.</p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-ui.stat-card
                label="Pesanan Hari Ini"
                :value="(string) $ordersToday"
                icon="shopping-bag"
                variant="admin"
                :trend="$ordersTodayTrendUp ? 'up' : 'down'"
                :trend-label="$ordersTodayTrend"
            />
            <x-ui.stat-card
                label="Pembayaran Pending"
                :value="(string) $pendingPayments"
                icon="credit-card"
                variant="admin"
                trend="down"
                trend-label="Perlu diverifikasi"
            />
            <x-ui.stat-card
                label="Pelanggan Aktif"
                :value="(string) $activeCustomers"
                icon="users"
                variant="admin"
                :trend="$activeCustomersTrendUp ? 'up' : 'down'"
                :trend-label="$activeCustomersTrend"
            />
            <x-ui.stat-card
                label="Total Omzet"
                :value="'Rp '.number_format($totalRevenue, 0, ',', '.')"
                icon="trending-up"
                variant="admin"
                description="Order shipped + completed (FR-RPT)"
            />
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-neutral-100 bg-white lg:col-span-2">
                <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
                    <h2 class="font-bold text-zinc-900">Pesanan Terbaru</h2>
                    <a href="{{ route('admin.orders.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-semibold text-brand-yellow-dark hover:underline">
                        Lihat Semua
                        <x-icon name="arrow-right" class="size-3" />
                    </a>
                </div>

                @if ($recentOrders->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <x-icon name="inbox" class="mx-auto mb-2 size-8 text-zinc-300" />
                        <p class="text-sm text-zinc-500">Belum ada pesanan.</p>
                    </div>
                @else
                    <div class="divide-y divide-neutral-50">
                        @foreach ($recentOrders as $order)
                            <div class="flex items-center gap-3 px-5 py-3.5 transition-colors hover:bg-neutral-50">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-zinc-900">{{ $order->order_number }}</p>
                                    <p class="text-xs text-zinc-500">
                                        {{ $order->user?->name ?? $order->recipient_name }}
                                        · {{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d M') }}
                                    </p>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ \App\Services\Admin\AdminDashboardService::orderStatusClasses($order->status) }}">
                                    {{ \App\Services\Admin\AdminDashboardService::orderStatusLabel($order->status) }}
                                </span>
                                <p class="w-28 text-right text-sm font-bold text-zinc-900">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </p>
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-zinc-400 transition hover:text-zinc-700" aria-label="Detail pesanan">
                                    <x-icon name="chevron-right" class="size-4" />
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-neutral-100 bg-white">
                    <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
                        <h2 class="text-sm font-bold text-zinc-900">Verifikasi Pelanggan</h2>
                        <span class="rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-600">{{ $pendingVerificationCount }}</span>
                    </div>

                    @if ($pendingVerifications->isEmpty())
                        <div class="px-5 py-6 text-center">
                            <x-icon name="circle-check" class="mx-auto mb-2 size-6 text-emerald-400" />
                            <p class="text-xs text-zinc-500">Semua verifikasi selesai</p>
                        </div>
                    @else
                        <div class="divide-y divide-neutral-50">
                            @foreach ($pendingVerifications as $profile)
                                <div class="flex items-center gap-3 px-5 py-3 transition hover:bg-neutral-50">
                                    <div class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-yellow/20">
                                        <span class="text-xs font-bold text-brand-yellow-dark">{{ strtoupper(substr($profile->user?->name ?? '?', 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-semibold text-zinc-800">{{ $profile->user?->name }}</p>
                                        <p class="truncate text-[11px] text-zinc-400">{{ $profile->business_name ?: 'Usaha belum diisi' }}</p>
                                    </div>
                                    <a href="{{ route('admin.customers.show', $profile) }}" wire:navigate class="shrink-0 text-xs font-semibold text-brand-yellow-dark hover:underline">
                                        Review
                                    </a>
                                </div>
                            @endforeach
                            @if ($pendingVerificationCount > 3)
                                <div class="px-5 py-2.5">
                                    <a href="{{ route('admin.customers.index', ['status' => \App\Models\CustomerProfile::PENDING]) }}" wire:navigate class="text-xs font-semibold text-zinc-500 hover:text-zinc-700">
                                        +{{ $pendingVerificationCount - 3 }} lainnya
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-neutral-100 bg-white p-5">
                    <div class="mb-3 flex items-center gap-2">
                        <x-icon name="refresh-cw" class="size-4 text-zinc-500" />
                        <h2 class="text-sm font-bold text-zinc-900">Status Sinkronisasi POS</h2>
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Sinkronisasi Terakhir</span>
                            <span class="font-semibold text-zinc-600">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Produk Disinkronkan</span>
                            <span class="font-semibold text-zinc-800">{{ number_format($productSkuCount) }} SKU</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">Status</span>
                            <span class="inline-flex items-center gap-1 font-semibold text-amber-700">
                                <x-icon name="circle-alert" class="size-3" />
                                Belum terhubung
                            </span>
                        </div>
                    </div>
                    <button
                        type="button"
                        disabled
                        class="mt-3 flex w-full cursor-not-allowed items-center justify-center gap-1.5 rounded-xl border border-neutral-200 py-2 text-xs font-semibold text-zinc-400"
                        title="Integrasi POS belum tersedia"
                    >
                        <x-icon name="refresh-cw" class="size-3" />
                        Sinkron Sekarang
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <a
                        href="{{ route('admin.payments.index') }}"
                        wire:navigate
                        class="flex flex-col items-center gap-1.5 rounded-xl border border-neutral-100 bg-white p-3 transition hover:border-neutral-200"
                    >
                        <div class="inline-flex size-9 items-center justify-center rounded-xl bg-orange-50">
                            <x-icon name="credit-card" class="size-4 text-orange-500" />
                        </div>
                        <span class="text-center text-[11px] font-semibold text-zinc-600">Verifikasi Bayar</span>
                    </a>
                    <a
                        href="{{ route('admin.reports.index') }}"
                        wire:navigate
                        class="flex flex-col items-center gap-1.5 rounded-xl border border-neutral-100 bg-white p-3 transition hover:border-neutral-200"
                    >
                        <div class="inline-flex size-9 items-center justify-center rounded-xl bg-blue-50">
                            <x-icon name="trending-up" class="size-4 text-blue-500" />
                        </div>
                        <span class="text-center text-[11px] font-semibold text-zinc-600">Laporan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
