<x-layouts.app :title="'Kategori - Pixel Komunika'">
    <div class="space-y-5 p-6">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Kategori</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Klasifikasi katalog dari POS. Status aktif mengontrol tampilnya kategori di website.</p>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-neutral-100 bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100">
                            <th class="w-12 px-5 py-3 text-center text-xs font-semibold tracking-wide text-zinc-400 uppercase">No</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Kategori</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Jumlah Produk</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Sinkron POS</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse ($categories as $category)
                            <tr class="transition-colors hover:bg-neutral-50">
                                <td class="w-12 px-5 py-3.5 text-center text-xs text-zinc-400">{{ $loop->iteration }}</td>
                                <td class="px-5 py-3.5 font-semibold text-zinc-900">{{ $category->name }}</td>
                                <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $category->products_count }} produk</td>
                                <td class="px-5 py-3.5">
                                    @if ($category->synced_at)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-bold text-sky-700">
                                            <x-icon name="refresh-cw" class="size-3" />
                                            Sinkron POS
                                        </span>
                                        <span class="mt-0.5 block text-[10px] text-zinc-400">{{ $category->synced_at->timezone('Asia/Jakarta')->translatedFormat('d M Y') }}</span>
                                    @else
                                        <span class="text-xs text-zinc-400">Belum sinkron</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <form method="POST" action="{{ route('admin.categories.toggle', $category) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $category->is_active ? '0' : '1' }}">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[11px] font-bold transition
                                                {{ $category->is_active
                                                    ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'
                                                    : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200' }}"
                                        >
                                            <span class="size-1.5 rounded-full {{ $category->is_active ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                                            {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-zinc-400">
                                    Belum ada kategori.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
