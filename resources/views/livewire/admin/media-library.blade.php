<div class="space-y-5 p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Media Library</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Unggah media sekali, lalu pakai ulang di banyak produk.</p>
        </div>
        <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-brand-yellow px-4 py-2.5 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-dark">
            <x-icon name="upload" class="size-4" />
            <span>Unggah Media</span>
            <input type="file" wire:model="uploadFile" accept="image/jpeg,image/png,image/webp" class="sr-only">
        </label>
    </div>

    @if (session()->has('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @error('uploadFile')
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
    @enderror

    <div class="rounded-2xl border border-neutral-100 bg-white">
        <div class="flex flex-col gap-3 border-b border-neutral-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-sm">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" />
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama file..."
                    class="w-full rounded-xl border border-neutral-200 py-2.5 pr-3 pl-10 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                >
            </div>
            <span class="text-xs text-zinc-500">
                <strong class="text-zinc-900">{{ $items->total() }}</strong> media
            </span>
        </div>

        @if ($items->isEmpty())
            <div class="px-5 py-16 text-center">
                <div class="mx-auto mb-3 flex size-12 items-center justify-center rounded-2xl bg-zinc-50">
                    <x-icon name="image" class="size-6 text-zinc-300" />
                </div>
                <p class="text-sm font-semibold text-zinc-700">Belum ada media</p>
                <p class="mt-1 text-xs text-zinc-400">Unggah gambar pertama untuk mulai membangun perpustakaan media.</p>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 p-4 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6">
                @foreach ($items as $item)
                    <div class="group overflow-hidden rounded-xl border border-neutral-100 bg-white transition hover:border-neutral-200 hover:shadow-sm">
                        <button
                            type="button"
                            wire:click="$set('previewId', {{ $item->id }})"
                            class="relative block aspect-square w-full overflow-hidden bg-zinc-50"
                        >
                            <img
                                src="{{ $item->url() }}"
                                alt="{{ $item->original_name }}"
                                class="size-full object-cover transition duration-300 group-hover:scale-105"
                                loading="lazy"
                            >
                            @if ($item->product_usages_count > 0)
                                <span class="absolute right-2 bottom-2 inline-flex items-center gap-1 rounded-full bg-brand-black/85 px-2 py-0.5 text-[10px] font-bold text-white">
                                    <x-icon name="link" class="size-3" />
                                    {{ $item->product_usages_count }} produk
                                </span>
                            @endif
                        </button>

                        <div class="flex items-center justify-between gap-1 border-t border-neutral-100 px-2.5 py-2">
                            <p class="min-w-0 truncate text-[11px] font-medium text-zinc-600" title="{{ $item->original_name }}">
                                {{ $item->original_name }}
                            </p>
                            @if ($item->product_usages_count === 0)
                                <button
                                    type="button"
                                    wire:click="delete({{ $item->id }})"
                                    wire:confirm="Hapus media ini? File akan dihapus permanen."
                                    class="rounded-lg p-1 text-zinc-400 transition hover:bg-red-50 hover:text-red-600"
                                    title="Hapus media"
                                >
                                    <x-icon name="trash-2" class="size-3.5" />
                                </button>
                            @else
                                <span class="rounded-lg p-1 text-zinc-300" title="Media dipakai produk — hapus setelah tidak digunakan">
                                    <x-icon name="lock" class="size-3.5" />
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($items->hasPages())
                <div class="border-t border-neutral-100 px-5 py-4">
                    {{ $items->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Preview modal --}}
    @if ($previewId)
        @php($preview = $items->firstWhere('id', $previewId) ?? \App\Models\MediaLibrary::query()->withCount('productUsages')->find($previewId))
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/50 p-4" wire:click.self="$set('previewId', null)">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-3">
                    <h3 class="truncate text-sm font-bold text-zinc-900">{{ $preview->original_name }}</h3>
                    <button type="button" wire:click="$set('previewId', null)" class="rounded-lg p-1 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700">
                        <x-icon name="x" class="size-4.5" />
                    </button>
                </div>
                <div class="bg-zinc-50 p-5">
                    <img src="{{ $preview->url() }}" alt="{{ $preview->original_name }}" class="mx-auto max-h-80 rounded-xl object-contain">
                </div>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-2 px-5 py-4 text-xs">
                    <div>
                        <dt class="font-semibold text-zinc-500">Ukuran file</dt>
                        <dd class="mt-0.5 text-zinc-800">{{ number_format($preview->file_size / 1024, 1) }} KB</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-zinc-500">Dimensi</dt>
                        <dd class="mt-0.5 text-zinc-800">
                            {{ $preview->width ? $preview->width.' × '.$preview->height.' px' : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-zinc-500">Tipe</dt>
                        <dd class="mt-0.5 text-zinc-800">{{ $preview->mime_type }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-zinc-500">Dipakai produk</dt>
                        <dd class="mt-0.5 text-zinc-800">{{ $preview->product_usages_count }} produk</dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif
</div>
