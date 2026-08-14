<div class="rounded-2xl border border-neutral-100 bg-white p-5 sm:p-6" x-data>
    <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="text-sm font-bold text-zinc-900">Gambar produk</h2>
        <button
            type="button"
            wire:click="$set('pickerOpen', true)"
            class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-zinc-700"
        >
            <x-icon name="plus" class="size-3.5" />
            Tambah Gambar
        </button>
    </div>

    @if (session()->has('media_error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('media_error') }}
        </div>
    @endif

    @if ($usages->isEmpty())
        <p class="py-6 text-center text-xs text-zinc-400">
            Belum ada gambar. Tambahkan gambar baru atau pilih dari Media Library untuk thumbnail katalog.
        </p>
    @else
        <div class="space-y-2.5">
            @foreach ($usages as $usage)
                @php($media = $usage->library)
                <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-50/60 p-2.5 {{ $usage->is_primary ? 'ring-1 ring-brand-yellow/50' : '' }}">
                    <div class="size-12 shrink-0 overflow-hidden rounded-lg border border-neutral-100 bg-white">
                        <img src="{{ $media?->url() }}" alt="{{ $usage->alt_text }}" class="size-full object-cover" loading="lazy">
                    </div>

                    <div class="min-w-0 flex-1 space-y-1.5">
                        <div class="flex items-center gap-2">
                            <p class="min-w-0 truncate text-xs font-semibold text-zinc-800">{{ $media?->original_name }}</p>
                            @if ($usage->is_primary)
                                <span class="shrink-0 rounded-full bg-brand-yellow/20 px-2 py-0.5 text-[10px] font-bold text-brand-yellow-dark">★ Utama</span>
                            @endif
                        </div>
                        <input
                            type="text"
                            value="{{ $usage->alt_text }}"
                            placeholder="Alt text..."
                            wire:change="updateAltText({{ $usage->id }}, $event.target.value)"
                            class="w-full rounded-lg border border-neutral-200 bg-white px-2.5 py-1.5 text-xs text-zinc-700 focus:border-brand-yellow focus:outline-none"
                        >
                    </div>

                    <div class="flex shrink-0 items-center gap-0.5">
                        <button
                            type="button"
                            wire:click="moveUp({{ $usage->id }})"
                            @disabled($loop->first)
                            class="rounded-lg p-1.5 text-zinc-400 transition hover:bg-white hover:text-zinc-700 disabled:opacity-30"
                            title="Naikkan urutan"
                        >
                            <x-icon name="chevron-up" class="size-3.5" />
                        </button>
                        <button
                            type="button"
                            wire:click="moveDown({{ $usage->id }})"
                            @disabled($loop->last)
                            class="rounded-lg p-1.5 text-zinc-400 transition hover:bg-white hover:text-zinc-700 disabled:opacity-30"
                            title="Turunkan urutan"
                        >
                            <x-icon name="chevron-down" class="size-3.5" />
                        </button>
                        <button
                            type="button"
                            wire:click="setPrimary({{ $usage->id }})"
                            class="rounded-lg p-1.5 text-zinc-400 transition hover:bg-white hover:text-brand-yellow-dark"
                            title="Jadikan gambar utama"
                        >
                            <x-icon name="star" class="size-3.5" />
                        </button>
                        <button
                            type="button"
                            wire:click="detach({{ $usage->id }})"
                            wire:confirm="Lepas gambar ini dari produk? File di Media Library tidak ikut terhapus."
                            class="rounded-lg p-1.5 text-zinc-400 transition hover:bg-white hover:text-red-600"
                            title="Lepas dari produk"
                        >
                            <x-icon name="trash-2" class="size-3.5" />
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Picker modal --}}
    <div
        x-show="$wire.pickerOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/50 p-4"
        @keydown.escape.window="$wire.closePicker()"
        wire:click.self="closePicker"
    >
        <div class="flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-3">
                <h3 class="text-sm font-bold text-zinc-900">Pilih Media</h3>
                <button type="button" wire:click="closePicker" class="rounded-lg p-1 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700">
                    <x-icon name="x" class="size-4.5" />
                </button>
            </div>

            <div class="flex flex-col gap-3 border-b border-neutral-100 px-5 py-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" />
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari media..."
                        class="w-full rounded-xl border border-neutral-200 py-2.5 pr-3 pl-10 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                    >
                </div>
                <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-neutral-200 bg-white px-3.5 py-2.5 text-xs font-bold text-zinc-700 transition hover:bg-zinc-50">
                    <x-icon name="upload" class="size-3.5" />
                    <span>Unggah Baru</span>
                    <input type="file" wire:model="uploadFile" accept="image/jpeg,image/png,image/webp" class="sr-only">
                </label>
            </div>

            @error('uploadFile')
                <div class="px-5 pt-3 text-xs text-red-600">{{ $message }}</div>
            @enderror

            <div class="flex-1 overflow-y-auto p-4">
                @if ($library->isEmpty())
                    <p class="py-10 text-center text-xs text-zinc-400">Tidak ada media yang cocok. Unggah media baru untuk memulai.</p>
                @else
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($library as $item)
                            @php($isAttached = in_array($item->id, $attachedIds, true))
                            <label class="group relative block cursor-pointer overflow-hidden rounded-xl border-2 transition {{ in_array($item->id, $selectedIds, true) ? 'border-brand-yellow' : 'border-transparent' }} {{ $isAttached ? 'cursor-not-allowed opacity-40' : 'hover:border-zinc-300' }}">
                                <img src="{{ $item->url() }}" alt="{{ $item->original_name }}" class="aspect-square w-full object-cover" loading="lazy">
                                <input
                                    type="checkbox"
                                    wire:model.live="selected"
                                    value="{{ $item->id }}"
                                    @disabled($isAttached)
                                    class="absolute top-2 left-2 size-4 rounded border-zinc-300 accent-amber-400"
                                >
                                <span class="absolute inset-x-0 bottom-0 truncate bg-gradient-to-t from-black/60 to-transparent px-2 pt-4 pb-1 text-[10px] font-medium text-white">
                                    {{ $item->original_name }}
                                </span>
                                @if ($isAttached)
                                    <span class="absolute top-1.5 right-1.5 rounded-full bg-brand-black/80 px-1.5 py-0.5 text-[9px] font-bold text-white">Terpasang</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-neutral-100 px-5 py-3">
                <span class="text-xs text-zinc-500">{{ count($selectedIds) }} terpilih</span>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        wire:click="closePicker"
                        class="rounded-xl border border-neutral-200 px-4 py-2 text-xs font-bold text-zinc-700 transition hover:bg-zinc-50"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="attachSelected"
                        @disabled(empty($selectedIds))
                        class="rounded-xl bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-dark disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Pilih ({{ count($selectedIds) }})
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
