<x-layouts.auth title="Daftar - Pixel Komunika">
    <x-layout.auth-shell
        title="Daftar pelanggan"
        description="Akun baru menunggu verifikasi admin sebelum bisa melihat harga dan berbelanja."
        panel-title="Gabung jadi reseller terverifikasi"
        panel-description="Daftarkan usaha Anda. Setelah admin menyetujui akun, harga partai dan checkout terbuka."
    >
        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <flux:input
                id="name"
                name="name"
                label="Nama"
                value="{{ old('name') }}"
                placeholder="Nama lengkap"
                required
                autofocus
            />

            <flux:input
                id="business_name"
                name="business_name"
                label="Nama usaha"
                value="{{ old('business_name') }}"
                placeholder="Opsional"
            />

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input
                    id="phone"
                    name="phone"
                    label="Nomor telepon"
                    value="{{ old('phone') }}"
                    placeholder="08xxxxxxxxxx"
                    required
                />

                <flux:input
                    id="email"
                    name="email"
                    type="email"
                    label="Email"
                    value="{{ old('email') }}"
                    placeholder="nama@usaha.com"
                    required
                />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input
                    id="password"
                    name="password"
                    type="password"
                    label="Password"
                    viewable
                    required
                />

                <flux:input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    label="Konfirmasi password"
                    viewable
                    required
                />
            </div>

            <div class="space-y-3 pt-1">
                <p class="text-xs leading-relaxed text-zinc-600">
                    Dengan mendaftar, Anda menyetujui
                    <a href="{{ route('legal.terms') }}" target="_blank" class="font-semibold text-zinc-900 underline underline-offset-2">Syarat dan Ketentuan</a>
                    dan
                    <a href="{{ route('legal.privacy') }}" target="_blank" class="font-semibold text-zinc-900 underline underline-offset-2">Kebijakan Privasi</a>.
                </p>
                <flux:button type="submit" variant="primary" color="amber" class="w-full">
                    Kirim pendaftaran
                </flux:button>

                <flux:button href="{{ route('login') }}" variant="ghost" class="w-full text-zinc-500">
                    Sudah punya akun? Masuk
                </flux:button>
            </div>
        </form>
    </x-layout.auth-shell>
</x-layouts.auth>
