<x-layouts.customer :title="'Buku Alamat - Pixel Komunika'">
    <div x-data="{ addModalOpen: false, editAddressId: null }">
        <!-- Top Action Bar -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pb-2">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-zinc-900">Daftar Alamat Pengiriman</h2>
                <p class="text-xs sm:text-sm text-zinc-500 font-medium mt-0.5">Kelola alamat tujuan pengiriman barang untuk kemudahan proses checkout.</p>
            </div>
            <div>
                <button
                    type="button"
                    @click="addModalOpen = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-yellow px-4 py-2.5 text-xs sm:text-sm font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs"
                >
                    <x-icon name="plus" class="size-4" />
                    <span>Tambah Alamat Baru</span>
                </button>
            </div>
        </div>

        <!-- 2-Column Grid of Saved Addresses -->
        @if ($user->addresses->isEmpty())
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-12 text-center shadow-2xs">
                <x-icon name="map-pin" class="size-12 mx-auto text-zinc-300" />
                <h3 class="mt-3 text-base font-bold text-zinc-800">Belum Ada Alamat Tersimpan</h3>
                <p class="mt-1 text-xs sm:text-sm text-zinc-500 max-w-sm mx-auto">Tambahkan minimal satu alamat tujuan pengiriman untuk mempermudah transaksi belanja Anda.</p>
                <div class="mt-5">
                    <button
                        type="button"
                        @click="addModalOpen = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-yellow px-4 py-2.5 text-xs sm:text-sm font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs"
                    >
                        <x-icon name="plus" class="size-4" />
                        <span>Tambah Alamat Sekarang</span>
                    </button>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($user->addresses as $address)
                    <div class="rounded-2xl border transition-all duration-200 p-5 flex flex-col justify-between shadow-2xs {{ $address->is_default ? 'bg-amber-50/20 border-amber-300/80 ring-1 ring-amber-300/50' : 'bg-white border-zinc-200/80 hover:border-zinc-300' }}">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-center justify-between gap-2 border-b border-zinc-100 pb-3 mb-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <h3 class="font-bold text-sm text-zinc-900 truncate">{{ $address->label ?: 'Alamat Pengiriman' }}</h3>
                                    @if ($address->is_default)
                                        <span class="inline-flex rounded-md bg-brand-yellow px-2 py-0.5 text-[10px] font-black text-brand-black shadow-2xs shrink-0">
                                            Utama
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="space-y-1.5 text-xs sm:text-sm">
                                <p class="font-bold text-zinc-900">{{ $address->recipient_name }}</p>
                                <p class="text-zinc-600 font-medium">{{ $address->recipient_phone }}</p>
                                <p class="text-zinc-600 leading-relaxed pt-1">
                                    {{ $address->address_line }}<br>
                                    {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} {{ $address->postal_code }}
                                </p>
                            </div>
                        </div>

                        <!-- Card Actions Footer -->
                        <div class="flex items-center justify-between border-t border-zinc-100 pt-3 mt-4 text-xs sm:text-sm">
                            <div>
                                @if (! $address->is_default)
                                    <form method="POST" action="{{ route('account.addresses.default', $address) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="font-bold text-amber-800 hover:text-amber-900 hover:underline">
                                            Jadikan Utama
                                        </button>
                                    </form>
                                @else
                                    <span class="text-zinc-400 font-medium">Alamat Default</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="editAddressId = {{ $address->id }}"
                                    class="inline-flex items-center gap-1 font-semibold text-zinc-700 hover:text-zinc-950 px-2.5 py-1 rounded-lg hover:bg-zinc-100 transition"
                                >
                                    <x-icon name="pencil" class="size-3.5" />
                                    <span>Ubah</span>
                                </button>

                                @php
                                    $deleteDescription = $address->label
                                        ? "Yakin ingin menghapus alamat \"{$address->label}\"? Tindakan ini tidak bisa dibatalkan."
                                        : 'Yakin ingin menghapus alamat ini? Tindakan ini tidak bisa dibatalkan.';
                                @endphp
                                <x-ui.confirm-dialog
                                    title="Hapus Alamat"
                                    :description="$deleteDescription"
                                    confirm-label="Ya, Hapus Alamat"
                                    cancel-label="Batal"
                                    confirm-variant="danger"
                                    action="{{ route('account.addresses.destroy', $address) }}"
                                    method="DELETE"
                                >
                                    <x-slot:trigger>
                                        <button type="button" class="inline-flex items-center gap-1 font-semibold text-red-600 hover:text-red-700 px-2.5 py-1 rounded-lg hover:bg-red-50 transition">
                                            <x-icon name="trash-2" class="size-3.5" />
                                            <span>Hapus</span>
                                        </button>
                                    </x-slot:trigger>
                                </x-ui.confirm-dialog>
                            </div>
                        </div>

                        <!-- Modal Edit Alamat -->
                        <div
                            x-show="editAddressId === {{ $address->id }}"
                            x-cloak
                            class="fixed inset-0 z-50 overflow-y-auto"
                            aria-labelledby="modal-title-{{ $address->id }}"
                            role="dialog"
                            aria-modal="true"
                        >
                            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                                <div
                                    x-show="editAddressId === {{ $address->id }}"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 bg-zinc-950/50 backdrop-blur-xs transition-opacity"
                                    @click="editAddressId = null"
                                ></div>

                                <div
                                    x-show="editAddressId === {{ $address->id }}"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                                >
                                    <div class="flex items-center justify-between border-b border-zinc-100 px-6 py-4">
                                        <h3 class="text-base font-bold text-zinc-900" id="modal-title-{{ $address->id }}">Ubah Alamat Pengiriman</h3>
                                        <button @click="editAddressId = null" class="text-zinc-400 hover:text-zinc-700 p-1">
                                            <x-icon name="x" class="size-5" />
                                        </button>
                                    </div>

                                    <form method="POST" action="{{ route('account.addresses.update', $address) }}" class="p-6 space-y-4">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">Label Alamat</label>
                                            <input
                                                type="text"
                                                name="label"
                                                value="{{ $address->label }}"
                                                placeholder="Contoh: Toko Utama / Rumah / Gudang"
                                                class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                                            />
                                        </div>
                                        
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                                                    Nama Penerima <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="recipient_name"
                                                    value="{{ $address->recipient_name }}"
                                                    placeholder="Nama lengkap penerima"
                                                    required
                                                    class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                                                    Nomor Telepon <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="recipient_phone"
                                                    value="{{ $address->recipient_phone }}"
                                                    placeholder="08xxxxxxxxxx"
                                                    required
                                                    class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                                                />
                                            </div>
                                        </div>

                                        <!-- Dropdown Bertingkat Wilayah Indonesia (Provinsi -> Kota/Kabupaten -> Kecamatan -> Kode Pos) -->
                                        <x-storefront.region-select
                                            :province="$address->province_name"
                                            :city="$address->city_name"
                                            :district="$address->district_name"
                                            :postal-code="$address->postal_code"
                                        />

                                        <div>
                                            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                                                Alamat Lengkap <span class="text-red-500">*</span>
                                            </label>
                                            <textarea
                                                name="address_line"
                                                rows="3"
                                                placeholder="Nama jalan, gedung, RT/RW, nomor rumah, patokan"
                                                required
                                                class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                                            >{{ $address->address_line }}</textarea>
                                        </div>

                                        <label class="inline-flex items-center gap-2 text-xs sm:text-sm text-zinc-700 font-medium cursor-pointer">
                                            <input type="checkbox" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }} class="size-4 rounded-md border-zinc-300 text-brand-black focus:ring-zinc-900">
                                            <span>Jadikan sebagai alamat utama</span>
                                        </label>

                                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                                            <button
                                                type="button"
                                                @click="editAddressId = null"
                                                class="rounded-xl border border-zinc-200/80 px-4 py-2.5 text-xs sm:text-sm font-semibold text-zinc-700 hover:bg-zinc-50 transition"
                                            >
                                                Batal
                                            </button>
                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-brand-yellow px-5 py-2.5 text-xs sm:text-sm font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs"
                                            >
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Modal Tambah Alamat Baru -->
        <div
            x-show="addModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title-add"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="addModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-zinc-950/50 backdrop-blur-xs transition-opacity"
                    @click="addModalOpen = false"
                ></div>

                <div
                    x-show="addModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                >
                    <div class="flex items-center justify-between border-b border-zinc-100 px-6 py-4">
                        <h3 class="text-base font-bold text-zinc-900" id="modal-title-add">Tambah Alamat Pengiriman Baru</h3>
                        <button @click="addModalOpen = false" class="text-zinc-400 hover:text-zinc-700 p-1">
                            <x-icon name="x" class="size-5" />
                        </button>
                    </div>

                    <form method="POST" action="{{ route('account.addresses.store') }}" class="p-6 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">Label Alamat</label>
                            <input
                                type="text"
                                name="label"
                                value="{{ old('label') }}"
                                placeholder="Contoh: Toko Utama / Rumah / Gudang"
                                class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                            />
                        </div>
                        
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                                    Nama Penerima <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="recipient_name"
                                    value="{{ old('recipient_name', $user->name) }}"
                                    placeholder="Nama lengkap penerima"
                                    required
                                    class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                                />
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="recipient_phone"
                                    value="{{ old('recipient_phone', $user->phone) }}"
                                    placeholder="08xxxxxxxxxx"
                                    required
                                    class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                                />
                            </div>
                        </div>

                        <!-- Dropdown Bertingkat Wilayah Indonesia (Provinsi -> Kota/Kabupaten -> Kecamatan -> Kode Pos) -->
                        <x-storefront.region-select
                            :province="old('province_name', '')"
                            :city="old('city_name', '')"
                            :district="old('district_name', '')"
                            :postal-code="old('postal_code', '')"
                        />

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="address_line"
                                rows="3"
                                placeholder="Nama jalan, gedung, RT/RW, nomor rumah, patokan"
                                required
                                class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                            >{{ old('address_line') }}</textarea>
                        </div>

                        <label class="inline-flex items-center gap-2 text-xs sm:text-sm text-zinc-700 font-medium cursor-pointer">
                            <input type="checkbox" name="is_default" value="1" class="size-4 rounded-md border-zinc-300 text-brand-black focus:ring-zinc-900">
                            <span>Jadikan sebagai alamat utama</span>
                        </label>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                            <button
                                type="button"
                                @click="addModalOpen = false"
                                class="rounded-xl border border-zinc-200/80 px-4 py-2.5 text-xs sm:text-sm font-semibold text-zinc-700 hover:bg-zinc-50 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-brand-yellow px-5 py-2.5 text-xs sm:text-sm font-bold text-brand-black hover:bg-brand-yellow-soft transition shadow-2xs"
                            >
                                Simpan Alamat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
