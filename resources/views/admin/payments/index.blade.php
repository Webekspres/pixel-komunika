<x-layouts.app :title="'Pembayaran - Pixel Komunika'">
    <div class="space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h1 class="text-xl font-black text-zinc-900">Pembayaran</h1>
                <p class="mt-0.5 text-sm text-zinc-500">Verifikasi bukti transfer dan kelola riwayat pembayaran.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                <span class="size-1.5 rounded-full bg-orange-500"></span>
                {{ $pending->count() }} menunggu verifikasi
            </span>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <section aria-labelledby="antrian-heading">
            <div class="mb-3 flex items-center justify-between">
                <h2 id="antrian-heading" class="text-sm font-bold text-zinc-900">Antrian Verifikasi</h2>
                <span class="text-xs text-zinc-400">Bukti terbaru di atas</span>
            </div>

            @if ($pending->isEmpty())
                <div class="flex flex-col items-center rounded-2xl border border-neutral-100 bg-white px-5 py-12 text-center">
                    <x-icon name="circle-check" class="mb-3 size-8 text-emerald-400" />
                    <p class="text-sm font-semibold text-zinc-800">Tidak ada antrian</p>
                    <p class="mt-1 text-xs text-zinc-500">Semua bukti pembayaran sudah diverifikasi.</p>
                </div>
            @else
                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach ($pending as $proof)
                        <article
                            class="flex flex-col rounded-2xl border border-neutral-100 bg-white p-5"
                            x-data="{ preview: false, reject: false }"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-zinc-900">
                                        <a href="{{ route('admin.orders.show', $proof->order) }}" wire:navigate class="hover:underline">
                                            Order #{{ $proof->order->order_number }}
                                        </a>
                                    </p>
                                    <p class="mt-0.5 truncate text-xs text-zinc-500">
                                        {{ $proof->user?->name ?? $proof->order->recipient_name }}
                                        · {{ $proof->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                    </p>
                                </div>
                                <span class="shrink-0 rounded-full bg-orange-100 px-2.5 py-1 text-[11px] font-bold text-orange-700">PENDING</span>
                            </div>

                            <dl class="mt-4 space-y-2 rounded-xl bg-neutral-50 p-4 text-sm">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-zinc-500">Bank</dt>
                                    <dd class="font-semibold text-zinc-800">{{ $proof->bank_name }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-zinc-500">Atas nama</dt>
                                    <dd class="truncate font-semibold text-zinc-800">{{ $proof->account_name }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-zinc-500">Nominal</dt>
                                    <dd class="font-bold text-zinc-900">Rp {{ number_format($proof->amount, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-zinc-500">Tagihan order</dt>
                                    <dd class="font-semibold text-zinc-800">Rp {{ number_format($proof->order->grand_total, 0, ',', '.') }}</dd>
                                </div>
                            </dl>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    @click="preview = true"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-neutral-200 px-3 py-1.5 text-xs font-bold text-zinc-700 transition hover:bg-neutral-50"
                                >
                                    <x-icon name="eye" class="size-3.5" />
                                    Lihat Bukti
                                </button>

                                <form method="POST" action="{{ route('admin.payments.update', $proof) }}" class="ml-auto">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                                        Terima
                                    </button>
                                </form>

                                <button
                                    type="button"
                                    @click="reject = true"
                                    class="rounded-xl bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-200"
                                >
                                    Tolak
                                </button>
                            </div>

                            {{-- Modal preview bukti --}}
                            <div
                                x-show="preview"
                                x-cloak
                                x-transition.opacity
                                class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4 backdrop-blur-sm"
                                @click.self="preview = false"
                                @keydown.escape.window="preview = false"
                            >
                                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
                                    <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-3">
                                        <p class="text-sm font-bold text-zinc-900">Bukti Transfer — Order #{{ $proof->order->order_number }}</p>
                                        <button type="button" @click="preview = false" class="rounded-lg p-1 text-zinc-400 transition hover:bg-neutral-100 hover:text-zinc-700" aria-label="Tutup">
                                            <x-icon name="x" class="size-4" />
                                        </button>
                                    </div>
                                    <div class="max-h-[70vh] overflow-auto bg-neutral-100 p-4">
                                        <img
                                            src="{{ route('admin.payments.show', $proof) }}"
                                            alt="Bukti transfer {{ $proof->bank_name }} a/n {{ $proof->account_name }}"
                                            class="mx-auto w-full rounded-xl object-contain"
                                        >
                                    </div>
                                </div>
                            </div>

                            {{-- Modal alasan tolak --}}
                            <div
                                x-show="reject"
                                x-cloak
                                x-transition.opacity
                                class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4 backdrop-blur-sm"
                                @click.self="reject = false"
                                @keydown.escape.window="reject = false"
                            >
                                <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl">
                                    <h3 class="text-sm font-bold text-zinc-900">Tolak Pembayaran</h3>
                                    <p class="mt-1 text-xs text-zinc-500">Order #{{ $proof->order->order_number }} akan kembali ke status belum dibayar. Alasan wajib diisi.</p>

                                    <form method="POST" action="{{ route('admin.payments.update', $proof) }}" class="mt-4">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="reject">
                                        <label for="reason-{{ $proof->id }}" class="mb-1.5 block text-xs font-semibold text-zinc-700">Alasan penolakan</label>
                                        <textarea
                                            id="reason-{{ $proof->id }}"
                                            name="reason"
                                            rows="3"
                                            required
                                            minlength="3"
                                            placeholder="Misal: nominal tidak sesuai, bukti buram..."
                                            class="w-full rounded-xl border border-neutral-200 p-3 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                                        ></textarea>
                                        <div class="mt-4 flex justify-end gap-2">
                                            <button type="button" @click="reject = false" class="rounded-xl border border-neutral-200 px-3 py-1.5 text-xs font-bold text-zinc-600 transition hover:bg-neutral-50">
                                                Batal
                                            </button>
                                            <button type="submit" class="rounded-xl bg-red-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-red-700">
                                                Tolak Pembayaran
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section aria-labelledby="riwayat-heading">
            <div class="mb-3">
                <h2 id="riwayat-heading" class="text-sm font-bold text-zinc-900">Riwayat Pembayaran</h2>
            </div>

            <div class="overflow-hidden rounded-2xl border border-neutral-100 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100">
                                <th class="w-12 px-5 py-3 text-center text-xs font-semibold tracking-wide text-zinc-400 uppercase">No</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Order</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Pelanggan</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Bank</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Nominal</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Status</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Direview</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            @forelse ($history as $proof)
                                <tr class="transition-colors hover:bg-neutral-50">
                                    <td class="w-12 px-5 py-3.5 text-center text-xs text-zinc-400">{{ $history->firstItem() + $loop->index }}</td>
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('admin.orders.show', $proof->order) }}" wire:navigate class="font-bold text-zinc-900 hover:underline">
                                            #{{ $proof->order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $proof->user?->name ?? $proof->order->recipient_name }}</td>
                                    <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $proof->bank_name }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-zinc-800">Rp {{ number_format($proof->amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5">
                                        @if ($proof->status === 'approved')
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-800">DISETUJUI</span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-700">DITOLAK</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-xs whitespace-nowrap text-zinc-500">
                                        @if ($proof->reviewed_at)
                                            {{ $proof->reviewed_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                        @else
                                            —
                                        @endif
                                        @if ($proof->reviewer)
                                            <span class="block text-zinc-400">oleh {{ $proof->reviewer->name }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-zinc-400">
                                        Belum ada riwayat pembayaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($history->hasPages())
                    <div class="border-t border-neutral-100 px-5 py-4">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-layouts.app>
