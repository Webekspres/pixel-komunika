<x-layouts.app :title="'PPh 22 - Pixel Komunika'">
    <x-layout.admin-page
        title="Konfigurasi PPh 22"
        description="Kelola klasifikasi kategori, ambang nilai belanja, dan tarif PPh 22 per kategori."
    >
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <x-ui.section-card title="Klasifikasi & tarif" description="Tarif 0% diperbolehkan untuk menonaktifkan PPh 22 pada kategori tertentu.">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-3 py-3">Kategori</th>
                            <th class="px-3 py-3">Ambang belanja</th>
                            <th class="px-3 py-3">Tarif PPh 22</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($categories as $category)
                            @php $rule = $category->taxRule; @endphp
                            <tr>
                                <td class="px-3 py-3 font-semibold text-zinc-900">{{ $category->name }}</td>
                                <td class="px-3 py-3">
                                    @if ($rule)
                                        Rp {{ number_format($rule->threshold_amount, 0, ',', '.') }}
                                    @else
                                        <span class="text-zinc-400">Belum dikonfigurasi</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    @if ($rule)
                                        {{ number_format($rule->rate_percent, 4) }}%
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    @if ($rule?->is_active)
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">AKTIF</span>
                                    @else
                                        <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-600">NONAKTIF</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <flux:button href="{{ route('admin.tax-rules.edit', $category) }}" variant="ghost" size="sm">
                                        Kelola
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.section-card>
    </x-layout.admin-page>
</x-layouts.app>
