<x-layouts.app :title="'Masuk - Pixel Komunika'">
    <x-layout.auth-shell
        title="Masuk ke akun Anda"
        description="Gunakan email dan password terdaftar untuk membuka akses area internal Pixel Komunika."
    >
        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <flux:input
                id="email"
                name="email"
                type="email"
                label="Email"
                value="{{ old('email') }}"
                placeholder="nama@usaha.com"
                required
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

            <div class="space-y-3">
                <flux:button type="submit" variant="primary" color="amber" class="w-full">
                    Masuk
                </flux:button>

                <flux:button href="{{ route('register') }}" variant="ghost" class="w-full">
                    Belum punya akun? Daftar
                </flux:button>
            </div>
        </form>
    </x-layout.auth-shell>
</x-layouts.app>
