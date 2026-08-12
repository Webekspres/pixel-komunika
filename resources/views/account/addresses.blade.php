<x-layouts.customer :title="'Alamat Akun - Pixel Komunika'">
    <x-layout.app-page>
        <x-storefront.breadcrumb
            :items="[
                ['label' => 'Akun Saya', 'href' => route('account.dashboard')],
                ['label' => 'Alamat', 'href' => null],
            ]"
        />

        <x-ui.page-header
            eyebrow="Alamat"
            title="Address book"
            description="Kelola alamat pengiriman agar checkout tetap cepat dan akurat."
        />

        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
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

            <x-ui.section-card
                title="Daftar alamat"
                description="Hanya pemilik akun yang bisa mengubah alamatnya sendiri."
            >
                <div class="grid gap-4">
                    @forelse ($user->addresses as $address)
                        <div class="rounded-[1.75rem] border border-brand-black/8 bg-zinc-50/80 p-5 shadow-card">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-lg font-bold text-brand-black">{{ $address->label ?: 'Alamat pelanggan' }}</h3>
                                        @if ($address->is_default)
                                            <span class="inline-flex rounded-full bg-brand-yellow px-2.5 py-1 text-xs font-bold text-brand-black">Default</span>
                                        @endif
                                    </div>

                                    <p class="mt-2 text-sm font-medium text-zinc-800">{{ $address->recipient_name }} • {{ $address->recipient_phone }}</p>
                                    <p class="mt-1 text-sm leading-relaxed text-zinc-600">
                                        {{ $address->address_line }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} {{ $address->postal_code }}
                                    </p>
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
    </x-layout.app-page>
</x-layouts.customer>
