<x-layouts.customer :title="'Akun - Pixel Komunika'">
    @php($status = $user->customerStatus())

    <x-layout.app-page>
        <x-storefront.breadcrumb :items="[['label' => 'Akun Saya', 'href' => null]]" />

        <x-ui.page-header
            eyebrow="Akun"
            :title="$user->name"
            :description="$user->email.' • '.($user->phone ?? 'No phone')"
        />

        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.stat-card label="Role" :value="$user->isAdmin() ? 'Admin' : 'Customer'" icon="shield-check" />
            <x-ui.stat-card label="Status" :value="$user->isAdmin() ? 'ADMIN_ACCESS' : ($status ?: 'PENDING_VERIFICATION')" icon="badge-check" />
            <x-ui.stat-card label="Alamat" :value="(string) $user->addresses->count()" description="Default address dipakai lebih dulu saat checkout." icon="map-pin" />
        </div>

        <x-ui.section-card class="overflow-hidden">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start">
                <div class="space-y-4">
                    <div class="flex flex-wrap gap-2">
                        <span class="storefront-pill">
                            <x-icon name="user-circle" class="size-3.5" />
                            <span>Portal pelanggan</span>
                        </span>
                        @if (! $user->isAdmin())
                            <x-ui.status-badge :status="$status ?: \App\Models\CustomerProfile::PENDING" />
                        @endif
                    </div>

                    <h2 class="text-xl font-bold tracking-tight text-brand-black">
                        @if ($user->isAdmin())
                            Akses admin aktif
                        @elseif ($status === \App\Models\CustomerProfile::ACTIVE)
                            Akun aktif
                        @elseif ($status === \App\Models\CustomerProfile::REJECTED)
                            Pendaftaran ditolak
                        @elseif ($status === \App\Models\CustomerProfile::SUSPENDED)
                            Akun ditangguhkan
                        @else
                            Menunggu verifikasi
                        @endif
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-brand-black/60">
                        @if ($user->isAdmin())
                            Gunakan halaman customer management untuk approval dan perubahan status akun pelanggan.
                        @elseif ($status === \App\Models\CustomerProfile::ACTIVE)
                            Anda dapat melihat harga, membuka checkout, dan mengakses riwayat order.
                        @elseif ($status === \App\Models\CustomerProfile::REJECTED)
                            {{ $user->customerProfile?->rejection_reason ?: 'Silakan hubungi admin untuk informasi lebih lanjut.' }}
                        @elseif ($status === \App\Models\CustomerProfile::SUSPENDED)
                            {{ $user->customerProfile?->rejection_reason ?: 'Akses checkout dan harga ditutup sementara.' }}
                        @else
                            Anda tetap dapat melihat katalog publik, tetapi harga, checkout, dan riwayat order belum tersedia.
                        @endif
                    </p>
                </div>

                <div class="grid gap-2 sm:grid-cols-3 lg:grid-cols-1">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full bg-brand-black px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-black/88">
                        Kembali ke Storefront
                    </a>
                    @if ($status === \App\Models\CustomerProfile::ACTIVE)
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center justify-center rounded-full bg-brand-yellow px-4 py-3 text-sm font-semibold text-brand-black transition hover:bg-brand-yellow-soft">
                            Riwayat Pesanan
                        </a>
                        <a href="{{ route('checkout.index') }}" class="inline-flex items-center justify-center rounded-full border border-brand-black/10 bg-white px-4 py-3 text-sm font-semibold text-brand-black transition hover:border-brand-black/20">
                            Checkout
                        </a>
                    @endif
                </div>
            </div>
        </x-ui.section-card>

        @if (! $user->isAdmin())
            <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <x-ui.section-card
                    title="Langkah berikutnya"
                    description="Pisahkan aktivitas akun berdasarkan context agar portal pelanggan tetap terasa seperti storefront."
                >
                    <div class="grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('account.profile') }}" class="storefront-panel-soft rounded-[1.5rem] p-4 transition hover:-translate-y-0.5">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-black/35">Profil</p>
                            <h3 class="mt-2 text-base font-bold text-brand-black">Kelola identitas akun</h3>
                            <p class="mt-1 text-sm text-brand-black/55">Perbarui nama, telepon, dan nama usaha untuk kebutuhan order.</p>
                        </a>

                        <a href="{{ route('account.addresses.index') }}" class="storefront-panel-soft rounded-[1.5rem] p-4 transition hover:-translate-y-0.5">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-black/35">Alamat</p>
                            <h3 class="mt-2 text-base font-bold text-brand-black">Rapikan address book</h3>
                            <p class="mt-1 text-sm text-brand-black/55">Simpan alamat default agar checkout berikutnya lebih cepat.</p>
                        </a>

                        @if ($status === \App\Models\CustomerProfile::ACTIVE)
                            <a href="{{ route('orders.index') }}" class="storefront-panel-soft rounded-[1.5rem] p-4 transition hover:-translate-y-0.5">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-black/35">Pesanan</p>
                                <h3 class="mt-2 text-base font-bold text-brand-black">Pantau order aktif</h3>
                                <p class="mt-1 text-sm text-brand-black/55">Lihat status pembayaran dan histori transaksi Anda.</p>
                            </a>

                            <a href="{{ route('checkout.index') }}" class="storefront-panel-soft rounded-[1.5rem] p-4 transition hover:-translate-y-0.5">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-black/35">Checkout</p>
                                <h3 class="mt-2 text-base font-bold text-brand-black">Lanjutkan pembelian</h3>
                                <p class="mt-1 text-sm text-brand-black/55">Tetap gunakan flow ecommerce tanpa keluar dari shell storefront.</p>
                            </a>
                        @endif
                    </div>
                </x-ui.section-card>

                <x-ui.section-card
                    title="Address snapshot"
                    description="Ringkasan alamat terbaru untuk memastikan default shipping tidak salah context."
                >
                    <div class="grid gap-4">
                        @forelse ($user->addresses->take(2) as $address)
                            <div class="rounded-[1.5rem] border border-brand-black/8 bg-zinc-50/80 p-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-brand-black">{{ $address->label ?: 'Alamat pelanggan' }}</h3>
                                    @if ($address->is_default)
                                        <span class="inline-flex rounded-full bg-brand-yellow px-2.5 py-1 text-[10px] font-bold text-brand-black">Default</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm font-medium text-zinc-800">{{ $address->recipient_name }} • {{ $address->recipient_phone }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-zinc-600">
                                    {{ $address->address_line }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} {{ $address->postal_code }}
                                </p>
                            </div>
                        @empty
                            <x-ui.empty-state
                                title="Belum ada alamat pelanggan"
                                description="Tambahkan minimal satu alamat untuk mempersiapkan checkout."
                                icon="map-pin"
                            />
                        @endforelse
                    </div>
                </x-ui.section-card>
            </div>
        @endif
    </x-layout.app-page>
</x-layouts.customer>
