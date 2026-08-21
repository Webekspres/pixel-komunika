<x-layouts.customer :title="'Ringkasan Akun - Pixel Komunika'">
    <!-- 1. Stat Cards Berorientasi Transaksi (4 Kolom Grid) -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- 1. Menunggu Pembayaran -->
        <a href="{{ route('orders.index', ['status' => 'unpaid']) }}" class="group block rounded-2xl border p-5 transition-all duration-200 shadow-2xs hover:shadow-xs {{ $waitingPaymentCount > 0 ? 'bg-amber-50/70 border-amber-200 hover:border-amber-300' : 'bg-white border-zinc-200/80 hover:border-zinc-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 group-hover:text-zinc-700">Menunggu Pembayaran</span>
                <div class="flex size-9 items-center justify-center rounded-xl {{ $waitingPaymentCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-zinc-100 text-zinc-500' }}">
                    <x-icon name="credit-card" class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-black text-zinc-900">{{ $waitingPaymentCount }}</p>
            <p class="mt-1 text-xs font-medium {{ $waitingPaymentCount > 0 ? 'text-amber-800 font-semibold' : 'text-zinc-500' }}">
                {{ $waitingPaymentCount > 0 ? 'Perlu unggah bukti bayar' : 'Tidak ada tagihan aktif' }}
            </p>
        </a>

        <!-- 2. Sedang Diproses -->
        <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="group block rounded-2xl border border-zinc-200/80 bg-white p-5 transition-all duration-200 shadow-2xs hover:border-zinc-300 hover:shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 group-hover:text-zinc-700">Pesanan Diproses</span>
                <div class="flex size-9 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <x-icon name="package" class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-black text-zinc-900">{{ $processingCount }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-500">Dalam penyiapan toko</p>
        </a>

        <!-- 3. Sedang Dikirim -->
        <a href="{{ route('orders.index', ['status' => 'shipped']) }}" class="group block rounded-2xl border border-zinc-200/80 bg-white p-5 transition-all duration-200 shadow-2xs hover:border-zinc-300 hover:shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 group-hover:text-zinc-700">Sedang Dikirim</span>
                <div class="flex size-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <x-icon name="truck" class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-black text-zinc-900">{{ $shippedCount }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-500">
                {{ $shippedCount > 0 ? 'Lacak resi pengiriman' : 'Belum ada pengiriman' }}
            </p>
        </a>

        <!-- 4. Alamat Tersimpan -->
        <a href="{{ route('account.addresses.index') }}" class="group block rounded-2xl border border-zinc-200/80 bg-white p-5 transition-all duration-200 shadow-2xs hover:border-zinc-300 hover:shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 group-hover:text-zinc-700">Alamat Tersimpan</span>
                <div class="flex size-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                    <x-icon name="map-pin" class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-black text-zinc-900">{{ $user->addresses->count() }} <span class="text-sm font-semibold text-zinc-500">Alamat</span></p>
            <p class="mt-1 text-xs font-medium text-zinc-500 truncate">
                {{ $primaryAddress ? 'Utama: ' . ($primaryAddress->label ?: $primaryAddress->recipient_name) : 'Belum ada alamat default' }}
            </p>
        </a>
    </div>

    <!-- 2. Layout 2 Kolom (Pesanan Terbaru & Ringkasan Akun) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- KOLOM KIRI (8 Kolom / ~65%): Card Pesanan Terkini -->
        <div class="lg:col-span-8 space-y-6">
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 sm:p-6 shadow-2xs">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-4 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-zinc-900">Pesanan Terbaru</h2>
                        <p class="text-xs text-zinc-500">Transaksi belanja terkini Anda di Pixel Komunika</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="text-xs font-bold text-brand-black hover:text-amber-600 transition-colors inline-flex items-center gap-1">
                        <span>Lihat Semua Pesanan</span>
                        <x-icon name="arrow-right" class="size-3.5" />
                    </a>
                </div>

                @forelse ($recentOrders as $order)
                    @php
                        $firstItem = $order->items->first();
                        $isWaiting = in_array($order->status, ['unpaid', 'payment_pending', 'payment_rejected'], true);
                    @endphp
                    <div class="py-4 first:pt-0 last:pb-0 border-b border-zinc-100 last:border-b-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-zinc-900">#{{ $order->order_number }}</span>
                                    <span class="text-xs text-zinc-400">•</span>
                                    <span class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                    <x-ui.status-badge :status="$order->status" />
                                </div>
                                <p class="mt-1.5 text-sm font-semibold text-zinc-800 line-clamp-1">
                                    {{ $firstItem?->product_name ?? 'Pesanan Produk' }}
                                    @if ($order->items->count() > 1)
                                        <span class="text-xs font-normal text-zinc-500">(+{{ $order->items->count() - 1 }} produk lainnya)</span>
                                    @endif
                                </p>
                                @if ($order->status === 'shipped' && $order->shipment && $order->shipment->tracking_number)
                                    <p class="mt-1 text-xs text-zinc-500">
                                        Resi: <span class="font-mono font-semibold text-zinc-700">{{ $order->shipment->tracking_number }}</span> ({{ strtoupper($order->courier_code) }})
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between sm:flex-col sm:items-end gap-2 shrink-0">
                                <span class="text-sm font-extrabold text-zinc-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                @if ($isWaiting)
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1 rounded-xl bg-brand-yellow px-3.5 py-1.5 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs">
                                        <x-icon name="upload" class="size-3.5" />
                                        <span>Unggah Bukti Bayar</span>
                                    </a>
                                @else
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1 rounded-xl bg-zinc-100 border border-zinc-200/80 px-3.5 py-1.5 text-xs font-semibold text-zinc-800 hover:bg-zinc-200 transition">
                                        <span>Detail Pesanan</span>
                                        <x-icon name="chevron-right" class="size-3.5" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <x-icon name="shopping-bag" class="size-12 mx-auto text-zinc-300" />
                        <h3 class="mt-3 text-sm font-bold text-zinc-800">Belum Ada Transaksi</h3>
                        <p class="mt-1 text-xs text-zinc-500 max-w-sm mx-auto">Jelajahi katalog produk Pixel Komunika dan mulai berbelanja kebutuhan toko Anda.</p>
                        <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-yellow px-4 py-2.5 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition">
                            <x-icon name="store" class="size-4" />
                            <span>Mulai Belanja</span>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- KOLOM KANAN (4 Kolom / ~35%): Profil & Alamat Utama -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Card Profil Toko & Usaha -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-3">
                    <h3 class="text-sm font-bold text-zinc-900">Profil Toko & Usaha</h3>
                    <a href="{{ route('account.profile') }}" class="text-xs font-semibold text-brand-black hover:underline inline-flex items-center gap-1">
                        <x-icon name="pencil" class="size-3" />
                        <span>Ubah Profil</span>
                    </a>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">Nama Lengkap</span>
                        <span class="font-semibold text-zinc-900">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">Nama Usaha</span>
                        <span class="font-semibold text-zinc-900">{{ $user->customerProfile?->business_name ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">Email</span>
                        <span class="font-semibold text-zinc-900 truncate max-w-[160px]" title="{{ $user->email }}">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">No. Telepon</span>
                        <span class="font-semibold text-zinc-900">{{ $user->phone ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card Alamat Pengiriman Utama -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-zinc-900">Alamat Utama</h3>
                        @if ($primaryAddress && $primaryAddress->is_default)
                            <span class="inline-flex rounded-md bg-brand-yellow/30 px-2 py-0.5 text-[10px] font-bold text-brand-black">Utama</span>
                        @endif
                    </div>
                    <a href="{{ route('account.addresses.index') }}" class="text-xs font-semibold text-brand-black hover:underline inline-flex items-center gap-1">
                        <x-icon name="map-pin" class="size-3" />
                        <span>Kelola Alamat</span>
                    </a>
                </div>

                @if ($primaryAddress)
                    <div class="text-xs space-y-1">
                        <p class="font-bold text-zinc-900">{{ $primaryAddress->recipient_name }}</p>
                        <p class="text-zinc-500">{{ $primaryAddress->recipient_phone }}</p>
                        <p class="text-zinc-600 leading-relaxed pt-1">
                            {{ $primaryAddress->address_line }}, {{ $primaryAddress->district_name }}, {{ $primaryAddress->city_name }}, {{ $primaryAddress->province_name }} {{ $primaryAddress->postal_code }}
                        </p>
                    </div>
                @else
                    <div class="py-4 text-center">
                        <p class="text-xs text-zinc-500">Belum ada alamat tersimpan.</p>
                        <a href="{{ route('account.addresses.index') }}" class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-brand-black hover:underline">
                            <span>+ Tambah Alamat Utama</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.customer>
