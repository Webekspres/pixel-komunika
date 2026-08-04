<x-layouts.app :title="'Daftar - Pixel Komunika'">
    <x-layout.auth-shell
        title="Daftar pelanggan"
        description="Akun baru akan masuk status pending verification sampai direview admin."
        eyebrow="Pendaftaran customer"
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

            <div class="space-y-3">
                <flux:button type="submit" variant="primary" color="amber" class="w-full">
                    Kirim pendaftaran
                </flux:button>

                <flux:button href="{{ route('login') }}" variant="ghost" class="w-full">
                    Sudah punya akun? Masuk
                </flux:button>
            </div>
        </form>
    </x-layout.auth-shell>
</x-layouts.app>
