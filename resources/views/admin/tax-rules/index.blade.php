<x-layouts.app :title="'PPh 22 - Pixel Komunika'">
    <div class="space-y-5 p-6">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Konfigurasi PPh 22</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Kelola klasifikasi kategori, ambang nilai belanja, dan tarif PPh 22 per kategori.</p>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-neutral-100 bg-white">
            <div class="border-b border-neutral-100 px-5 py-4">
                <h2 class="text-sm font-bold text-zinc-900">Klasifikasi & tarif</h2>
                <p class="mt-0.5 text-xs text-zinc-500">Tarif 0% diperbolehkan untuk menonaktifkan PPh 22 pada kategori tertentu.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100 text-left">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Kategori</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Ambang belanja</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Tarif PPh 22</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @foreach ($categories as $category)
                            @php $rule = $category->taxRule; @endphp
                            <tr class="hover:bg-neutral-50">
                                <td class="px-5 py-3.5 font-semibold text-zinc-900">{{ $category->name }}</td>
                                <td class="px-5 py-3.5 text-zinc-700">
                                    @if ($rule)
                                        Rp {{ number_format($rule->threshold_amount, 0, ',', '.') }}
                                    @else
                                        <span class="text-zinc-400">Belum dikonfigurasi</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-zinc-700">
                                    @if ($rule)
                                        {{ number_format($rule->rate_percent, 4) }}%
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($rule?->is_active)
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">AKTIF</span>
                                    @else
                                        <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-600">NONAKTIF</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('admin.tax-rules.edit', $category) }}" class="text-xs font-semibold text-zinc-500 hover:text-brand-black">
                                        Kelola →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
