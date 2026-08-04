<x-layouts.app :title="'Akun - Pixel Komunika'">
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-brand-black/10 bg-brand-white p-6 shadow-sm sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-brand-red">
                Akun
            </p>
            <h1 class="mt-2 text-3xl font-bold">{{ $user->name }}</h1>
            <p class="mt-2 text-brand-black/70">{{ $user->email }} • {{ $user->phone ?? 'No phone' }}</p>

            @php($status = $user->customerStatus())

            <div class="mt-8 rounded-2xl bg-gray-50 p-5">
                @if ($user->isAdmin())
                    <h2 class="text-xl font-semibold">Akses admin aktif</h2>
                    <p class="mt-2 text-sm text-brand-black/70">
                        Gunakan halaman customer management untuk approval dan perubahan status akun pelanggan.
                    </p>
                @elseif ($status === \App\Models\CustomerProfile::ACTIVE)
                    <h2 class="text-xl font-semibold">Akun aktif</h2>
                    <p class="mt-2 text-sm text-brand-black/70">
                        Anda dapat melihat harga, membuka checkout, dan mengakses riwayat order.
                    </p>
                @elseif ($status === \App\Models\CustomerProfile::REJECTED)
                    <h2 class="text-xl font-semibold">Pendaftaran ditolak</h2>
                    <p class="mt-2 text-sm text-brand-black/70">
                        {{ $user->customerProfile?->rejection_reason ?: 'Silakan hubungi admin untuk informasi lebih lanjut.' }}
                    </p>
                @elseif ($status === \App\Models\CustomerProfile::SUSPENDED)
                    <h2 class="text-xl font-semibold">Akun ditangguhkan</h2>
                    <p class="mt-2 text-sm text-brand-black/70">
                        {{ $user->customerProfile?->rejection_reason ?: 'Akses checkout dan harga ditutup sementara.' }}
                    </p>
                @else
                    <h2 class="text-xl font-semibold">Menunggu verifikasi</h2>
                    <p class="mt-2 text-sm text-brand-black/70">
                        Anda tetap dapat melihat katalog publik, tetapi harga, checkout, dan riwayat order belum tersedia.
                    </p>
                @endif
            </div>
        </div>

        @if (! $user->isAdmin())
            <div class="mt-8 grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-3xl border border-brand-black/10 bg-brand-white p-6 shadow-sm sm:p-8">
                    <h2 class="text-2xl font-bold">Profil pelanggan</h2>
                    <p class="mt-2 text-sm text-brand-black/70">
                        Data ini dipakai untuk identitas akun dan persiapan checkout.
                    </p>

                    <form method="POST" action="{{ route('account.update') }}" class="mt-6 space-y-5">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold">Nama</label>
                            <input id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                            @error('name') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-2 block text-sm font-semibold">Nomor telepon</label>
                            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                            @error('phone') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="business_name" class="mb-2 block text-sm font-semibold">Nama usaha</label>
                            <input id="business_name" name="business_name" value="{{ old('business_name', $user->customerProfile?->business_name) }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3">
                            @error('business_name') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="rounded-full bg-brand-yellow px-5 py-3 font-semibold text-brand-black">
                            Simpan profil
                        </button>
                    </form>
                </div>

                <div class="rounded-3xl border border-brand-black/10 bg-brand-white p-6 shadow-sm sm:p-8">
                    <h2 class="text-2xl font-bold">Tambah alamat</h2>
                    <p class="mt-2 text-sm text-brand-black/70">
                        Alamat default akan dipakai lebih dulu saat checkout.
                    </p>

                    <form method="POST" action="{{ route('account.addresses.store') }}" class="mt-6 space-y-4">
                        @csrf

                        <input name="label" value="{{ old('label') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Label alamat, mis. Toko Utama">
                        <input name="recipient_name" value="{{ old('recipient_name', $user->name) }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Nama penerima" required>
                        <input name="recipient_phone" value="{{ old('recipient_phone', $user->phone) }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Nomor penerima" required>
                        <textarea name="address_line" rows="3" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Alamat lengkap" required>{{ old('address_line') }}</textarea>
                        <input name="province_name" value="{{ old('province_name') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Provinsi" required>
                        <input name="city_name" value="{{ old('city_name') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Kota / Kabupaten" required>
                        <input name="district_name" value="{{ old('district_name') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Kecamatan" required>
                        <input name="postal_code" value="{{ old('postal_code') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Kode pos">

                        <label class="flex items-center gap-2 text-sm text-brand-black/70">
                            <input type="checkbox" name="is_default" value="1" class="rounded border-brand-black/20">
                            <span>Jadikan alamat default</span>
                        </label>

                        <button type="submit" class="rounded-full bg-brand-black px-5 py-3 font-semibold text-brand-white">
                            Simpan alamat
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-8 rounded-3xl border border-brand-black/10 bg-brand-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold">Address book</h2>
                        <p class="mt-2 text-sm text-brand-black/70">
                            Hanya pemilik akun yang bisa mengubah alamatnya sendiri.
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4">
                    @forelse ($user->addresses as $address)
                        <article class="rounded-2xl border border-brand-black/10 bg-gray-50 p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-semibold">{{ $address->label ?: 'Alamat pelanggan' }}</h3>
                                        @if ($address->is_default)
                                            <span class="rounded-full bg-brand-yellow px-2.5 py-1 text-xs font-semibold text-brand-black">Default</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm text-brand-black/70">
                                        {{ $address->recipient_name }} • {{ $address->recipient_phone }}
                                    </p>
                                    <p class="mt-1 text-sm text-brand-black/70">
                                        {{ $address->address_line }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} {{ $address->postal_code }}
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('account.addresses.destroy', $address) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-brand-red px-4 py-2 text-sm font-semibold text-brand-red">
                                        Hapus
                                    </button>
                                </form>
                            </div>

                            <form method="POST" action="{{ route('account.addresses.update', $address) }}" class="mt-5 grid gap-3 md:grid-cols-2">
                                @csrf
                                @method('PATCH')
                                <input name="label" value="{{ $address->label }}" class="rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Label">
                                <input name="recipient_name" value="{{ $address->recipient_name }}" class="rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Nama penerima" required>
                                <input name="recipient_phone" value="{{ $address->recipient_phone }}" class="rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Nomor penerima" required>
                                <input name="province_name" value="{{ $address->province_name }}" class="rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Provinsi" required>
                                <input name="city_name" value="{{ $address->city_name }}" class="rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Kota / Kabupaten" required>
                                <input name="district_name" value="{{ $address->district_name }}" class="rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Kecamatan" required>
                                <input name="postal_code" value="{{ $address->postal_code }}" class="rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Kode pos">
                                <textarea name="address_line" rows="3" class="md:col-span-2 rounded-2xl border border-brand-black/10 px-4 py-3" placeholder="Alamat lengkap" required>{{ $address->address_line }}</textarea>
                                <label class="flex items-center gap-2 text-sm text-brand-black/70">
                                    <input type="checkbox" name="is_default" value="1" class="rounded border-brand-black/20" @checked($address->is_default)>
                                    <span>Jadikan default</span>
                                </label>
                                <div>
                                    <button type="submit" class="rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black">
                                        Update alamat
                                    </button>
                                </div>
                            </form>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-brand-black/15 bg-gray-50 p-8 text-center text-brand-black/60">
                            Belum ada alamat pelanggan.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>
