<x-layouts.auth title="Masuk - Pixel Komunika">
    <x-layout.auth-shell
        title="Masuk ke akun Anda"
        description="Gunakan email dan password terdaftar untuk membuka akses area pelanggan Pixel Komunika."
        eyebrow="Masuk akun"
        panel-title="Akses harga grosir eksklusif"
        panel-description="Masuk untuk belanja partai sebagai pelanggan terverifikasi."
    >
        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <flux:input
                id="email"
                name="email"
                type="email"
                label="Email"
                value="{{ old('email') }}"
                placeholder="nama@usaha.com"
                required
                autofocus
            />

            <flux:input
                id="password"
                name="password"
                type="password"
                label="Password"
                viewable
                required
            />

            <flux:field variant="inline">
                <flux:checkbox name="remember" value="1" label="Ingat saya" />
            </flux:field>

            <div class="space-y-3 pt-1">
                <flux:button type="submit" variant="primary" color="amber" class="w-full">
                    Masuk
                </flux:button>

                <flux:button href="{{ route('register') }}" variant="ghost" class="w-full text-zinc-500">
                    Belum punya akun? Daftar
                </flux:button>
            </div>
        </form>
    </x-layout.auth-shell>
</x-layouts.auth>
