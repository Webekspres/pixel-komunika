<x-layouts.app :title="'Masuk - Pixel Komunika'">
    <section class="mx-auto max-w-xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-brand-black/10 bg-brand-white p-6 shadow-sm sm:p-8">
            <h1 class="text-3xl font-bold">Masuk</h1>
            <p class="mt-2 text-sm text-brand-black/70">
                Gunakan email dan password yang terdaftar.
            </p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                    @error('email') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold">Password</label>
                    <input id="password" name="password" type="password" class="w-full rounded-2xl border border-brand-black/10 px-4 py-3" required>
                    @error('password') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-brand-black/70">
                    <input type="checkbox" name="remember" value="1" class="rounded border-brand-black/20">
                    <span>Ingat saya</span>
                </label>

                <button type="submit" class="rounded-full bg-brand-yellow px-5 py-3 font-semibold text-brand-black">
                    Masuk
                </button>
            </form>
        </div>
    </section>
</x-layouts.app>
