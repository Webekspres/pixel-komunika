<x-layouts.app :title="'Merek - Pixel Komunika'">
    <div class="space-y-5 p-6">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Merek</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Merek katalog dari POS. Status aktif mengontrol tampilnya merek di website.</p>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($brands->isEmpty())
            <div class="rounded-2xl border border-neutral-100 bg-white px-5 py-12 text-center text-sm text-zinc-400">
                Belum ada merek.
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($brands as $brand)
                    <div class="flex flex-col rounded-2xl border border-neutral-100 bg-white p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-yellow/20 text-base font-black text-brand-yellow-dark">
                                {{ strtoupper(substr($brand->name, 0, 1)) }}
                            </div>
                            <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-[10px] font-bold tracking-wide text-zinc-500 uppercase">Sinkron POS</span>
                        </div>

                        <h2 class="mt-3 text-sm font-bold text-zinc-900">{{ $brand->name }}</h2>
                        <p class="mt-0.5 text-xs text-zinc-500">{{ $brand->products_count }} produk</p>

                        <div class="mt-4 border-t border-neutral-100 pt-3">
                            <form method="POST" action="{{ route('admin.brands.toggle', $brand) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $brand->is_active ? '0' : '1' }}">
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl px-3 py-2 text-[11px] font-bold transition
                                        {{ $brand->is_active
                                            ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'
                                            : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200' }}"
                                >
                                    <span class="size-1.5 rounded-full {{ $brand->is_active ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                                    {{ $brand->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
