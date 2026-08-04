<x-layouts.app :title="'Detail Customer - Pixel Komunika'">
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-brand-red">Admin</p>
                <h1 class="mt-2 text-3xl font-bold">Detail pendaftar</h1>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="rounded-full border border-brand-black/10 px-4 py-2 text-sm font-semibold text-brand-black">
                Kembali
            </a>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-3xl border border-brand-black/10 bg-brand-white p-6 shadow-sm sm:p-8">
                <h2 class="text-2xl font-bold">{{ $customer->user->name }}</h2>
                <div class="mt-4 space-y-2 text-sm text-brand-black/70">
                    <p>Email: {{ $customer->user->email }}</p>
                    <p>Phone: {{ $customer->user->phone }}</p>
                    <p>Nama usaha: {{ $customer->business_name ?: 'Belum diisi' }}</p>
                    <p>Status: {{ $customer->verification_status }}</p>
                    <p>Direview oleh: {{ $customer->reviewer?->name ?: '-' }}</p>
                    <p>Waktu review: {{ $customer->reviewed_at?->format('Y-m-d H:i') ?: '-' }}</p>
                </div>

                @if ($customer->rejection_reason)
                    <div class="mt-5 rounded-2xl bg-gray-50 p-4 text-sm text-brand-black/70">
                        {{ $customer->rejection_reason }}
                    </div>
                @endif
            </div>

            <div class="rounded-3xl border border-brand-black/10 bg-brand-white p-6 shadow-sm sm:p-8">
                <h2 class="text-2xl font-bold">Alamat pelanggan</h2>
                <div class="mt-5 grid gap-4">
                    @forelse ($customer->user->addresses as $address)
                        <article class="rounded-2xl border border-brand-black/10 bg-gray-50 p-4">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold">{{ $address->label ?: 'Alamat pelanggan' }}</h3>
                                @if ($address->is_default)
                                    <span class="rounded-full bg-brand-yellow px-2 py-1 text-xs font-semibold text-brand-black">Default</span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-brand-black/70">{{ $address->recipient_name }} • {{ $address->recipient_phone }}</p>
                            <p class="mt-1 text-sm text-brand-black/70">
                                {{ $address->address_line }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} {{ $address->postal_code }}
                            </p>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-brand-black/15 bg-gray-50 p-6 text-center text-brand-black/60">
                            Belum ada alamat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
