<x-layouts.app :title="'Daftar - Pixel Komunika'">
    <section class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-brand-black/10 bg-brand-white p-6 shadow-sm sm:p-8">
            <h1 class="text-3xl font-bold">Daftar pelanggan</h1>
            <p class="mt-2 text-sm text-brand-black/70">
                Akun baru akan berstatus pending sampai direview admin.
            </p>

            <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold">Nama</label>
                    <input id="name" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                    @error('name') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="business_name" class="mb-2 block text-sm font-semibold">Nama usaha</label>
                    <input id="business_name" name="business_name" value="{{ old('business_name') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3">
                    @error('business_name') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold">Nomor telepon</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                        @error('phone') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                        @error('email') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold">Password</label>
                        <input id="password" name="password" type="password" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                        @error('password') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold">Konfirmasi password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                    </div>
                </div>

                <button type="submit" class="rounded-full bg-brand-yellow px-5 py-3 font-semibold text-brand-black">
                    Kirim pendaftaran
                </button>
            </form>
        </div>
    </section>
</x-layouts.app>
