<x-layouts.app :title="'Akun - Pixel Komunika'">
    @php($status = $user->customerStatus())

    <x-layout.app-page>
        <x-ui.page-header
            eyebrow="Akun"
            :title="$user->name"
            :description="$user->email.' • '.($user->phone ?? 'No phone')"
        />

        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.stat-card label="Role" :value="$user->isAdmin() ? 'Admin' : 'Customer'" />
            <x-ui.stat-card label="Status" :value="$user->isAdmin() ? 'ADMIN_ACCESS' : ($status ?: 'PENDING_VERIFICATION')" />
            <x-ui.stat-card label="Alamat" :value="(string) $user->addresses->count()" description="Default address dipakai lebih dulu saat checkout." />
        </div>

        <x-ui.section-card>
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <flux:heading size="lg">
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
                    </flux:heading>

                    <flux:text class="mt-2">
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
                    </flux:text>
                </div>

                @if (! $user->isAdmin())
                    <x-ui.status-badge :status="$status ?: \App\Models\CustomerProfile::PENDING" />
                @endif
            </div>
        </x-ui.section-card>

        @if (! $user->isAdmin())
            <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <x-ui.section-card
                    title="Profil pelanggan"
                    description="Data ini dipakai untuk identitas akun dan persiapan checkout."
                >
                    <form method="POST" action="{{ route('account.update') }}" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <flux:input
                            id="name"
                            name="name"
                            label="Nama"
                            value="{{ old('name', $user->name) }}"
                            required
                        />

                        <flux:input
                            id="phone"
                            name="phone"
                            label="Nomor telepon"
                            value="{{ old('phone', $user->phone) }}"
                            required
                        />

                        <flux:input
                            id="business_name"
                            name="business_name"
                            label="Nama usaha"
                            value="{{ old('business_name', $user->customerProfile?->business_name) }}"
                        />

                        <flux:button type="submit" variant="primary" color="amber">
                            Simpan profil
                        </flux:button>
                    </form>
                </x-ui.section-card>

                <x-ui.section-card
                    title="Tambah alamat"
                    description="Alamat default akan dipakai lebih dulu saat checkout."
                >
                    <form method="POST" action="{{ route('account.addresses.store') }}" class="space-y-4">
                        @csrf

                        <flux:input
                            name="label"
                            label="Label alamat"
                            value="{{ old('label') }}"
                            placeholder="Mis. Toko Utama"
                        />

                        <flux:input
                            name="recipient_name"
                            label="Nama penerima"
                            value="{{ old('recipient_name', $user->name) }}"
                            required
                        />

                        <flux:input
                            name="recipient_phone"
                            label="Nomor penerima"
                            value="{{ old('recipient_phone', $user->phone) }}"
                            required
                        />

                        <flux:textarea
                            name="address_line"
                            label="Alamat lengkap"
                            rows="3"
                            placeholder="Jalan, nomor, patokan, dan detail lain"
                            required
                        >{{ old('address_line') }}</flux:textarea>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:input name="province_name" label="Provinsi" value="{{ old('province_name') }}" required />
                            <flux:input name="city_name" label="Kota / Kabupaten" value="{{ old('city_name') }}" required />
                            <flux:input name="district_name" label="Kecamatan" value="{{ old('district_name') }}" required />
                            <flux:input name="postal_code" label="Kode pos" value="{{ old('postal_code') }}" />
                        </div>

                        <flux:field variant="inline">
                            <flux:checkbox name="is_default" value="1" label="Jadikan alamat default" />
                        </flux:field>

                        <flux:button type="submit" variant="filled">
                            Simpan alamat
                        </flux:button>
                    </form>
                </x-ui.section-card>
            </div>

            <x-ui.section-card
                title="Address book"
                description="Hanya pemilik akun yang bisa mengubah alamatnya sendiri."
            >
                <div class="grid gap-4">
                    @forelse ($user->addresses as $address)
                        <flux:card class="space-y-5 bg-zinc-50">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <flux:heading size="lg">{{ $address->label ?: 'Alamat pelanggan' }}</flux:heading>
                                        @if ($address->is_default)
                                            <flux:badge color="amber" rounded size="sm">Default</flux:badge>
                                        @endif
                                    </div>

                                    <flux:text class="mt-2">{{ $address->recipient_name }} • {{ $address->recipient_phone }}</flux:text>
                                    <flux:text class="mt-1">
                                        {{ $address->address_line }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} {{ $address->postal_code }}
                                    </flux:text>
                                </div>

                                <form method="POST" action="{{ route('account.addresses.destroy', $address) }}">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="danger" size="sm">
                                        Hapus
                                    </flux:button>
                                </form>
                            </div>

                            <form method="POST" action="{{ route('account.addresses.update', $address) }}" class="grid gap-4 md:grid-cols-2">
                                @csrf
                                @method('PATCH')

                                <flux:input name="label" label="Label" value="{{ $address->label }}" />
                                <flux:input name="recipient_name" label="Nama penerima" value="{{ $address->recipient_name }}" required />
                                <flux:input name="recipient_phone" label="Nomor penerima" value="{{ $address->recipient_phone }}" required />
                                <flux:input name="province_name" label="Provinsi" value="{{ $address->province_name }}" required />
                                <flux:input name="city_name" label="Kota / Kabupaten" value="{{ $address->city_name }}" required />
                                <flux:input name="district_name" label="Kecamatan" value="{{ $address->district_name }}" required />
                                <flux:input name="postal_code" label="Kode pos" value="{{ $address->postal_code }}" />

                                <div class="md:col-span-2">
                                    <flux:textarea name="address_line" label="Alamat lengkap" rows="3" required>{{ $address->address_line }}</flux:textarea>
                                </div>

                                <flux:field variant="inline">
                                    <flux:checkbox name="is_default" value="1" label="Jadikan default" :checked="$address->is_default" />
                                </flux:field>

                                <div class="md:col-span-2">
                                    <flux:button type="submit" variant="primary" color="amber">
                                        Update alamat
                                    </flux:button>
                                </div>
                            </form>
                        </flux:card>
                    @empty
                        <x-ui.empty-state
                            title="Belum ada alamat pelanggan"
                            description="Tambahkan minimal satu alamat untuk mempersiapkan checkout."
                            icon="map-pin"
                        />
                    @endforelse
                </div>
            </x-ui.section-card>
        @endif
    </x-layout.app-page>
</x-layouts.app>
