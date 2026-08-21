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
                    <flux:input
                        id="name"
                        name="name"
                        label="Nama Lengkap"
                        value="{{ old('name', $user->name) }}"
                        required
                    />

                    <flux:input
                        id="phone"
                        name="phone"
                        label="Nomor Telepon (WhatsApp)"
                        value="{{ old('phone', $user->phone) }}"
                        required
                    />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <flux:input
                        id="business_name"
                        name="business_name"
                        label="Nama Usaha / Toko"
                        placeholder="Misal: Toko Berkah Abadi"
                        value="{{ old('business_name', $user->customerProfile?->business_name) }}"
                    />

                    <flux:input
                        id="email"
                        label="Alamat Email"
                        value="{{ $user->email }}"
                        disabled
                        readonly
                    />
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-zinc-100">
                    <flux:button type="submit" variant="primary" class="rounded-xl">
                        Simpan Perubahan
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.customer>
