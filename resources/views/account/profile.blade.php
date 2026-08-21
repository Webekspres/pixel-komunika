<x-layouts.customer :title="'Profil Usaha - Pixel Komunika'">
    <div class="max-w-3xl mx-auto">
        <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 sm:p-8 shadow-2xs">
            <div class="border-b border-zinc-100 pb-5 mb-6">
                <h2 class="text-lg sm:text-xl font-black text-zinc-900">Informasi Profil & Usaha</h2>
                <p class="text-xs sm:text-sm text-zinc-500 font-medium mt-1">Perbarui identitas akun dan data usaha untuk keperluan verifikasi dan faktur transaksi.</p>
            </div>

            <form method="POST" action="{{ route('account.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Nama lengkap Anda"
                            required
                            class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                        />
                    </div>

                    <div>
                        <label for="phone" class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                            Nomor Telepon (WhatsApp) <span class="text-red-500"></span>
                        </label>
                        <input
                            id="phone"
                            name="phone"
                            type="text"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="08xxxxxxxxxx"
                            required
                            class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                        />
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="business_name" class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                            Nama Usaha / Toko
                        </label>
                        <input
                            id="business_name"
                            name="business_name"
                            type="text"
                            value="{{ old('business_name', $user->customerProfile?->business_name) }}"
                            placeholder="Contoh: Toko Berkah Abadi"
                            class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
                        />
                    </div>

                    <div>
                        <label for="email" class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            value="{{ $user->email }}"
                            disabled
                            readonly
                            class="w-full rounded-xl border border-zinc-200/80 bg-zinc-100 px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-500 cursor-not-allowed shadow-2xs"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-zinc-100">
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
</x-layouts.customer>
