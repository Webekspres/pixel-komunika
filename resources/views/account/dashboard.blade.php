<x-layouts.customer :title="'Ringkasan Akun - Pixel Komunika'">
    @php
        $status = $user->customerStatus();
        $isActive = $user->isActiveCustomer();
    @endphp

    @unless ($isActive)
        {{-- Non-active accounts cannot open orders or checkout; tell them where they stand instead. --}}
        <section class="rounded-2xl border border-amber-300 bg-amber-50 p-5 sm:p-6" aria-labelledby="account-status-title">
            <h2 id="account-status-title" class="text-base font-bold text-zinc-900">
                @switch($status)
                    @case(\App\Models\CustomerProfile::REJECTED) Pendaftaran akun belum disetujui @break
                    @case(\App\Models\CustomerProfile::SUSPENDED) Akun sedang ditangguhkan @break
                    @default Akun sedang ditinjau admin
                @endswitch
            </h2>
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-zinc-700">
                @if ($status === \App\Models\CustomerProfile::PENDING || ! $status)
                    Harga, checkout, dan riwayat pesanan terbuka setelah admin menyetujui akun ini. Pastikan profil usaha sudah lengkap agar peninjauan tidak tertunda.
                @else
                    Harga, checkout, dan riwayat pesanan tidak tersedia untuk akun ini. Hubungi admin untuk informasi lebih lanjut.
                @endif
            </p>
            @if (in_array($status, [\App\Models\CustomerProfile::REJECTED, \App\Models\CustomerProfile::SUSPENDED], true) && $user->customerProfile?->rejection_reason)
                <p class="mt-2 text-sm text-zinc-700"><span class="font-semibold">Catatan admin:</span> {{ $user->customerProfile->rejection_reason }}</p>
            @endif
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('account.profile') }}" class="inline-flex min-h-11 items-center rounded-xl bg-brand-black px-4 text-xs font-bold text-white hover:bg-brand-black/85">
                    Periksa Profil Usaha
                </a>
                <a href="https://wa.me/6281546407702" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-zinc-300 bg-white px-4 text-xs font-bold text-zinc-800 hover:bg-zinc-50">
                    <x-icon name="message-circle" class="size-4" />
                    Hubungi admin via WhatsApp
                </a>
            </div>
        </section>
    @else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('orders.index', ['status' => 'unpaid']) }}" class="group block rounded-2xl border p-5 transition-colors {{ $needsUploadCount > 0 ? 'border-amber-300 bg-amber-50 hover:border-amber-400' : 'border-zinc-200/80 bg-white hover:border-zinc-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-zinc-700">Menunggu Pembayaran</span>
                <x-icon name="credit-card" class="size-5 {{ $needsUploadCount > 0 ? 'text-amber-800' : 'text-zinc-500' }}" />
            </div>
            <p class="mt-3 text-2xl font-black text-zinc-900">{{ $waitingPaymentCount }}</p>
            <p class="mt-1 text-xs font-medium {{ $needsUploadCount > 0 ? 'font-semibold text-amber-800' : 'text-zinc-600' }}">
                @if ($needsUploadCount > 0)
                    {{ $needsUploadCount }} perlu unggah bukti bayar
                @elseif ($waitingPaymentCount > 0)
                    Bukti terkirim, menunggu verifikasi admin
                @else
                    Tidak ada tagihan aktif
                @endif
            </p>
        </a>

        <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="group block rounded-2xl border border-zinc-200/80 bg-white p-5 transition-colors hover:border-zinc-300">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-zinc-700">Pesanan Diproses</span>
                <x-icon name="package" class="size-5 text-zinc-500" />
            </div>
            <p class="mt-3 text-2xl font-black text-zinc-900">{{ $processingCount }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-600">Dalam penyiapan toko</p>
        </a>

        <a href="{{ route('orders.index', ['status' => 'shipped']) }}" class="group block rounded-2xl border border-zinc-200/80 bg-white p-5 transition-colors hover:border-zinc-300">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-zinc-700">Sedang Dikirim</span>
                <x-icon name="truck" class="size-5 text-zinc-500" />
            </div>
            <p class="mt-3 text-2xl font-black text-zinc-900">{{ $shippedCount }}</p>
            <p class="mt-1 text-xs font-medium text-zinc-600">
                {{ $shippedCount > 0 ? 'Lacak resi pengiriman' : 'Belum ada pengiriman' }}
            </p>
        </a>

        <a href="{{ route('account.addresses.index') }}" class="group block rounded-2xl border border-zinc-200/80 bg-white p-5 transition-colors hover:border-zinc-300">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-zinc-700">Alamat Tersimpan</span>
                <x-icon name="map-pin" class="size-5 text-zinc-500" />
            </div>
            <p class="mt-3 text-2xl font-black text-zinc-900">{{ $user->addresses->count() }} <span class="text-sm font-semibold text-zinc-600">Alamat</span></p>
            <p class="mt-1 text-xs font-medium text-zinc-600 truncate">
                {{ $primaryAddress ? 'Utama: ' . ($primaryAddress->label ?: $primaryAddress->recipient_name) : 'Belum ada alamat default' }}
            </p>
        </a>
    </div>
    @endunless

    <!-- 2. Layout 2 Kolom (Pesanan Terbaru & Ringkasan Akun) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- KOLOM KIRI (8 Kolom / ~65%): Card Pesanan Terkini -->
        @if ($isActive)
        <div class="lg:col-span-8 space-y-6">
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 sm:p-6 shadow-2xs">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-4 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-zinc-900">Pesanan Terbaru</h2>
                        <p class="text-xs text-zinc-500">Transaksi belanja terkini Anda di Pixel Komunika</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="inline-flex min-h-11 items-center text-xs font-bold text-brand-black underline-offset-4 hover:underline">
                        Lihat semua pesanan
                    </a>
                </div>

                @forelse ($recentOrders as $order)
                    @php
                        $firstItem = $order->items->first();
                        $isWaiting = in_array($order->status, ['unpaid', 'payment_rejected'], true);
                    @endphp
                    <div class="py-4 first:pt-0 last:pb-0 border-b border-zinc-100 last:border-b-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-zinc-900">#{{ $order->order_number }}</span>
                                    <span class="text-xs text-zinc-500" aria-hidden="true">•</span>
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
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex min-h-11 items-center gap-1 rounded-xl bg-brand-yellow px-3.5 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs">
                                        <x-icon name="upload" class="size-3.5" />
                                        <span>Unggah Bukti Bayar</span>
                                    </a>
                                @else
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex min-h-11 items-center gap-1 rounded-xl bg-zinc-100 border border-zinc-200/80 px-3.5 text-xs font-semibold text-zinc-800 hover:bg-zinc-200 transition">
                                        <span>Detail Pesanan</span>
                                        <x-icon name="chevron-right" class="size-3.5" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <x-icon name="shopping-bag" class="size-12 mx-auto text-zinc-500" />
                        <h3 class="mt-3 text-sm font-bold text-zinc-800">Belum Ada Transaksi</h3>
                        <p class="mt-1 text-xs text-zinc-500 max-w-sm mx-auto">Jelajahi katalog produk Pixel Komunika dan mulai berbelanja kebutuhan toko Anda.</p>
                        <a href="{{ route('products.index') }}" class="min-h-11 mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-yellow px-4.5 text-xs font-bold text-brand-black hover:bg-brand-yellow-soft transition">
                            <span>Lihat Katalog Produk</span>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
        @endif

        <!-- KOLOM KANAN (4 Kolom / ~35%): Profil & Alamat Utama -->
        <div class="{{ $isActive ? 'lg:col-span-4 space-y-6' : 'grid gap-6 lg:col-span-12 lg:grid-cols-2' }}">
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
