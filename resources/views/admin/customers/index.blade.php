<x-layouts.app :title="'Customer Management - Pixel Komunika'">
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-brand-red">Admin</p>
                <h1 class="mt-2 text-3xl font-bold">Customer management</h1>
            </div>

            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari nama, email, phone, usaha"
                    class="rounded-full border border-brand-black/10 bg-brand-white px-4 py-2 text-sm"
                >
                <label for="status" class="text-sm font-medium">Status</label>
                <select id="status" name="status" class="rounded-full border border-brand-black/10 bg-brand-white px-4 py-2 text-sm">
                    <option value="">Semua</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black">
                    Filter
                </button>
            </form>
        </div>

        <div class="mt-8 grid gap-4">
            @forelse ($customers as $customer)
                <article class="rounded-3xl border border-brand-black/10 bg-brand-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold">{{ $customer->user->name }}</h2>
                            <p class="mt-1 text-sm text-brand-black/70">
                                {{ $customer->business_name ?: 'Usaha belum diisi' }} • {{ $customer->user->email }} • {{ $customer->user->phone }}
                            </p>
                            <p class="mt-3 inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-brand-black">
                                {{ $customer->verification_status }}
                            </p>
                            @if ($customer->rejection_reason)
                                <p class="mt-3 text-sm text-brand-black/70">{{ $customer->rejection_reason }}</p>
                            @endif
                            <p class="mt-3">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="text-sm font-semibold text-brand-red">
                                    Lihat detail
                                </a>
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="grid gap-3 lg:w-80">
                            @csrf
                            @method('PATCH')

                            <textarea
                                name="reason"
                                rows="3"
                                class="rounded-2xl border border-brand-black/10 px-4 py-3 text-sm"
                                placeholder="Alasan penolakan / penangguhan"
                            ></textarea>

                            <div class="flex flex-wrap gap-2">
                                <button type="submit" name="action" value="approve" class="rounded-full bg-brand-yellow px-4 py-2 text-sm font-semibold text-brand-black">
                                    Setujui
                                </button>
                                <button type="submit" name="action" value="reactivate" class="rounded-full bg-brand-black px-4 py-2 text-sm font-semibold text-brand-white">
                                    Aktifkan
                                </button>
                                <button type="submit" name="action" value="reject" class="rounded-full border border-brand-red px-4 py-2 text-sm font-semibold text-brand-red">
                                    Tolak
                                </button>
                                <button type="submit" name="action" value="suspend" class="rounded-full border border-brand-black/15 px-4 py-2 text-sm font-semibold text-brand-black">
                                    Tangguhkan
                                </button>
                            </div>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-brand-black/15 bg-brand-white p-8 text-center text-brand-black/60">
                    Belum ada customer sesuai filter.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $customers->links() }}
        </div>
    </section>
</x-layouts.app>
