<x-layouts.app :title="'Pengaturan - Pixel Komunika'">
    <div class="space-y-6 p-6" x-data="{ tab: (location.hash || '#identitas').slice(1) }">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Pengaturan</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Identitas toko, rekening pembayaran, minimum partai, dan tarif kurir toko.</p>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap gap-1.5 border-b border-neutral-200 pb-px">
            @foreach ([
                'identitas' => ['icon' => 'store', 'label' => 'Identitas Toko'],
                'rekening' => ['icon' => 'landmark', 'label' => 'Rekening Bank'],
                'partai' => ['icon' => 'boxes', 'label' => 'Minimum Partai'],
                'kurir' => ['icon' => 'truck', 'label' => 'Kurir Toko'],
            ] as $key => $item)
                <a
                    href="#{{ $key }}"
                    @click="tab = '{{ $key }}'"
                    class="inline-flex items-center gap-1.5 rounded-t-xl border-b-2 px-3.5 py-2.5 text-xs font-bold transition"
                    :class="tab === '{{ $key }}' ? 'border-brand-yellow text-zinc-900' : 'border-transparent text-zinc-500 hover:text-zinc-700'"
                >
                    <x-icon :name="$item['icon']" class="size-4" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Identitas Toko --}}
        <section
            x-show="tab === 'identitas'"
            x-cloak
            x-data="{ editing: {{ old('tab') === 'identitas' ? 'true' : 'false' }} }"
            class="max-w-2xl rounded-2xl border border-neutral-100 bg-white p-5 sm:p-6"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900">Identitas Toko</h2>
                    <p class="mt-0.5 text-xs text-zinc-500">Data ini muncul di invoice dan alamat pengiriman.</p>
                </div>
                <button
                    type="button"
                    @click="editing = !editing"
                    x-show="!editing"
                    x-cloak
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-zinc-100 px-3 py-1.5 text-xs font-bold text-zinc-700 transition hover:bg-zinc-200"
                >
                    <x-icon name="pencil" class="size-3.5" />
                    Edit
                </button>
            </div>

            <dl x-show="!editing" x-cloak class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-[11px] font-semibold tracking-wide text-zinc-400 uppercase">Nama toko</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-zinc-900">{{ $store->store_name }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-semibold tracking-wide text-zinc-400 uppercase">Nomor kontak</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-zinc-900">{{ $store->contact_number }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-[11px] font-semibold tracking-wide text-zinc-400 uppercase">Alamat toko</dt>
                    <dd class="mt-0.5 text-sm leading-relaxed text-zinc-700">{{ $store->address }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-semibold tracking-wide text-zinc-400 uppercase">Nama perusahaan</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-zinc-900">{{ $store->company_name ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-semibold tracking-wide text-zinc-400 uppercase">NPWP</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-zinc-900">{{ $store->company_npwp }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-[11px] font-semibold tracking-wide text-zinc-400 uppercase">Kode pos asal</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-zinc-900">{{ $store->origin_postal_code ?: '—' }}</dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('admin.settings.store-profile.update') }}" class="mt-5 grid gap-4" x-show="editing" x-cloak>
                @csrf
                @method('PATCH')
                <input type="hidden" name="tab" value="identitas">

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="store_name" class="mb-1.5 block text-xs font-semibold text-zinc-700">Nama toko</label>
                        <input id="store_name" name="store_name" type="text" required maxlength="191" value="{{ old('store_name', $store->store_name) }}" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none">
                    </div>
                    <div>
                        <label for="contact_number" class="mb-1.5 block text-xs font-semibold text-zinc-700">Nomor kontak</label>
                        <input id="contact_number" name="contact_number" type="text" required maxlength="32" value="{{ old('contact_number', $store->contact_number) }}" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="address" class="mb-1.5 block text-xs font-semibold text-zinc-700">Alamat toko</label>
                    <textarea id="address" name="address" rows="3" required class="w-full rounded-xl border border-neutral-200 p-3 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none">{{ old('address', $store->address) }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="company_name" class="mb-1.5 block text-xs font-semibold text-zinc-700">Nama perusahaan</label>
                        <input id="company_name" name="company_name" type="text" maxlength="191" value="{{ old('company_name', $store->company_name) }}" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none">
                    </div>
                    <div>
                        <label for="company_npwp" class="mb-1.5 block text-xs font-semibold text-zinc-700">NPWP</label>
                        <input id="company_npwp" name="company_npwp" type="text" required maxlength="32" value="{{ old('company_npwp', $store->company_npwp) }}" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="origin_postal_code" class="mb-1.5 block text-xs font-semibold text-zinc-700">Kode pos asal</label>
                    <input id="origin_postal_code" name="origin_postal_code" type="text" maxlength="16" value="{{ old('origin_postal_code', $store->origin_postal_code) }}" placeholder="mis. 40111" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none">
                </div>

                <div class="flex justify-end gap-2 border-t border-neutral-100 pt-4">
                    <button type="button" @click="editing = false" class="rounded-xl border border-neutral-200 px-4 py-2.5 text-xs font-bold text-zinc-600 transition hover:bg-neutral-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-brand-yellow px-5 py-2.5 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-dark">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </section>

        {{-- Rekening Bank --}}
        <section x-show="tab === 'rekening'" x-cloak x-data="{ addOpen: false }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900">Rekening Bank</h2>
                    <p class="mt-0.5 text-xs text-zinc-500">Rekening tujuan transfer pelanggan. Rekening aktif dipakai pada checkout (FR-PAY-001).</p>
                </div>
                <button type="button" @click="addOpen = true" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-dark">
                    <x-icon name="plus" class="size-4" />
                    Tambah Rekening
                </button>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-neutral-100 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100">
                                <th class="w-12 px-5 py-3 text-center text-xs font-semibold tracking-wide text-zinc-400 uppercase">No</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Bank</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">No. Rekening</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Atas Nama</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            @forelse ($bankAccounts as $account)
                                <tr class="transition-colors hover:bg-neutral-50">
                                    <td class="w-12 px-5 py-3.5 text-center text-xs text-zinc-400">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-zinc-100">
                                                <x-icon name="landmark" class="size-4 text-zinc-500" />
                                            </div>
                                            <div>
                                                <p class="font-semibold text-zinc-900">{{ $account->bank_name }}</p>
                                                @if ($account->instructions)
                                                    <p class="text-[11px] leading-snug text-zinc-400">{{ $account->instructions }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-sm text-zinc-700">{{ $account->account_number }}</td>
                                    <td class="px-5 py-3.5 text-zinc-600">{{ $account->account_holder }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $account->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-500' }}">
                                            {{ $account->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-2" x-data="{ editing: false }">
                                            <button type="button" @click="editing = !editing" class="rounded-xl bg-zinc-100 px-3 py-1.5 text-xs font-bold text-zinc-700 transition hover:bg-zinc-200">
                                                Edit
                                            </button>

                                            <x-ui.confirm-dialog
                                                title="Hapus rekening"
                                                :description="'Yakin ingin menghapus rekening ' . $account->bank_name . ' (' . $account->account_number . ')? Tindakan ini tidak bisa dibatalkan.'"
                                                confirm-label="Ya, hapus"
                                                cancel-label="Batal"
                                                confirm-variant="danger"
                                                action="{{ route('admin.settings.bank-accounts.destroy', $account) }}"
                                                method="DELETE"
                                            >
                                                <x-slot:trigger>
                                                    <button type="button" class="inline-flex items-center gap-1 rounded-xl bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                                        <x-icon name="trash-2" class="size-3.5" />
                                                        Hapus
                                                    </button>
                                                </x-slot:trigger>
                                                <input type="hidden" name="tab" value="rekening">
                                            </x-ui.confirm-dialog>

                                            <div x-show="editing" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4 backdrop-blur-sm" @click.self="editing = false" @keydown.escape.window="editing = false">
                                                <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl">
                                                    <h3 class="text-sm font-bold text-zinc-900">Edit Rekening — {{ $account->bank_name }}</h3>
                                                    <form method="POST" action="{{ route('admin.settings.bank-accounts.update', $account) }}" class="mt-4 grid gap-3">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="tab" value="rekening">
                                                        <div class="grid gap-3 sm:grid-cols-2">
                                                            <div>
                                                                <label class="mb-1 block text-[11px] font-semibold text-zinc-500">Bank</label>
                                                                <input type="text" name="bank_name" value="{{ $account->bank_name }}" required maxlength="100" class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:border-brand-yellow focus:outline-none">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-[11px] font-semibold text-zinc-500">No. rekening</label>
                                                                <input type="text" name="account_number" value="{{ $account->account_number }}" required maxlength="64" class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:border-brand-yellow focus:outline-none">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-[11px] font-semibold text-zinc-500">Atas nama</label>
                                                            <input type="text" name="account_holder" value="{{ $account->account_holder }}" required maxlength="191" class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:border-brand-yellow focus:outline-none">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-[11px] font-semibold text-zinc-500">Instruksi transfer</label>
                                                            <input type="text" name="instructions" value="{{ $account->instructions }}" maxlength="255" class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:border-brand-yellow focus:outline-none">
                                                        </div>
                                                        <label class="flex cursor-pointer items-center gap-1.5 text-xs text-zinc-600">
                                                            <input type="checkbox" name="is_active" value="1" class="rounded border-zinc-300" @checked($account->is_active)>
                                                            Aktif
                                                        </label>
                                                        <div class="flex justify-end gap-2 border-t border-neutral-100 pt-3">
                                                            <button type="button" @click="editing = false" class="rounded-xl border border-neutral-200 px-3 py-1.5 text-xs font-bold text-zinc-600 hover:bg-neutral-50">Batal</button>
                                                            <button type="submit" class="rounded-xl bg-brand-yellow px-4 py-1.5 text-xs font-bold text-brand-black hover:bg-brand-yellow-dark">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-zinc-400">
                                        Belum ada rekening bank. Tambahkan rekening tujuan transfer.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="addOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4 backdrop-blur-sm" @click.self="addOpen = false" @keydown.escape.window="addOpen = false">
                <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Rekening</h3>
                    <form method="POST" action="{{ route('admin.settings.bank-accounts.store') }}" class="mt-4 grid gap-3">
                        @csrf
                        <input type="hidden" name="tab" value="rekening">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <input type="text" name="bank_name" placeholder="* Nama bank (mis. BCA)" required maxlength="100" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                            <input type="text" name="account_number" placeholder="* Nomor rekening" required maxlength="64" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                        </div>
                        <input type="text" name="account_holder" placeholder="* Atas nama" required maxlength="191" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                        <input type="text" name="instructions" placeholder="Instruksi transfer (opsional)" maxlength="255" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                        <label class="flex cursor-pointer items-center gap-1.5 text-xs text-zinc-600">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-zinc-300">
                            Jadikan aktif
                        </label>
                        <div class="flex justify-end gap-2 border-t border-neutral-100 pt-3">
                            <button type="button" @click="addOpen = false" class="rounded-xl border border-neutral-200 px-3 py-1.5 text-xs font-bold text-zinc-600 hover:bg-neutral-50">Batal</button>
                            <button type="submit" class="rounded-xl bg-brand-yellow px-4 py-1.5 text-xs font-bold text-brand-black hover:bg-brand-yellow-dark">Tambah Rekening</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        {{-- Minimum Partai --}}
        <section
            x-show="tab === 'partai'"
            x-cloak
            x-data="{ editing: {{ old('tab') === 'partai' ? 'true' : 'false' }} }"
            class="max-w-2xl rounded-2xl border border-neutral-100 bg-white p-5 sm:p-6"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900">Minimum Partai</h2>
                    <p class="mt-0.5 text-xs text-zinc-500">Jumlah minimal per SKU agar harga partai (PARTAI) berlaku di keranjang (FR-PRC-008).</p>
                </div>
                <button
                    type="button"
                    @click="editing = !editing"
                    x-show="!editing"
                    x-cloak
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-zinc-100 px-3 py-1.5 text-xs font-bold text-zinc-700 transition hover:bg-zinc-200"
                >
                    <x-icon name="pencil" class="size-3.5" />
                    Edit
                </button>
            </div>

            <div x-show="!editing" x-cloak class="mt-5">
                <p class="text-[11px] font-semibold tracking-wide text-zinc-400 uppercase">Minimum kuantitas partai</p>
                <p class="mt-1 text-2xl font-black text-zinc-900">
                    {{ $store->partai_minimum_quantity ?? 5 }}
                    <span class="text-sm font-semibold text-zinc-500">per SKU</span>
                </p>
                <p class="mt-1.5 text-xs text-zinc-400">Perubahan dicatat ke audit trail.</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.store-profile.update') }}" class="mt-5 grid gap-4" x-show="editing" x-cloak>
                @csrf
                @method('PATCH')
                <input type="hidden" name="tab" value="partai">

                <div>
                    <label for="partai_minimum_quantity" class="mb-1.5 block text-xs font-semibold text-zinc-700">Minimum kuantitas partai</label>
                    <input
                        id="partai_minimum_quantity"
                        name="partai_minimum_quantity"
                        type="number"
                        min="1"
                        max="100000"
                        required
                        value="{{ old('partai_minimum_quantity', $store->partai_minimum_quantity ?? 5) }}"
                        class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                    >
                    <p class="mt-1.5 text-xs text-zinc-400">Perubahan dicatat ke audit trail.</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-neutral-100 pt-4">
                    <button type="button" @click="editing = false" class="rounded-xl border border-neutral-200 px-4 py-2.5 text-xs font-bold text-zinc-600 transition hover:bg-neutral-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-brand-yellow px-5 py-2.5 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-dark">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

            <div class="mt-6 border-t border-neutral-100 pt-4">
                <h3 class="mb-3 text-xs font-bold tracking-wide text-zinc-500 uppercase">Audit Trail</h3>
                @if ($auditTrail->isEmpty())
                    <p class="text-sm text-zinc-400">Belum ada perubahan tercatat.</p>
                @else
                    <ol class="space-y-3">
                        @foreach ($auditTrail as $log)
                            @php
                                $newQty = data_get($log->new_values, 'partai_minimum_quantity');
                                $oldQty = data_get($log->old_values, 'partai_minimum_quantity');
                            @endphp
                            <li class="flex items-start gap-3 text-sm">
                                <div class="mt-0.5 inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-neutral-100">
                                    <x-icon name="history" class="size-3 text-zinc-500" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-zinc-800">
                                        Minimum partai diubah
                                        @if ($oldQty !== null)
                                            dari <span class="font-bold">{{ $oldQty }}</span>
                                        @endif
                                        ke <span class="font-bold">{{ $newQty }}</span>
                                    </p>
                                    <p class="text-xs text-zinc-400">
                                        {{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                        @if ($log->actor_user_id)
                                            · oleh {{ \App\Models\User::query()->whereKey($log->actor_user_id)->value('name') ?? 'Admin' }}
                                        @endif
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </section>

        {{-- Kurir Toko --}}
        <section x-show="tab === 'kurir'" x-cloak x-data="{ addOpen: false }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900">Kurir Toko</h2>
                    <p class="mt-0.5 text-xs text-zinc-500">Tarif tetap per kecamatan untuk opsi pengiriman "Kurir Toko" (pengganti checkbox kurir pada mock).</p>
                </div>
                <button type="button" @click="addOpen = true" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-brand-yellow px-4 py-2 text-xs font-bold text-brand-black transition hover:bg-brand-yellow-dark">
                    <x-icon name="plus" class="size-4" />
                    Tambah Tarif
                </button>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-neutral-100 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100">
                                <th class="w-12 px-5 py-3 text-center text-xs font-semibold tracking-wide text-zinc-400 uppercase">No</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Kecamatan</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Kode Area</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Tarif</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-zinc-400 uppercase">Estimasi</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-zinc-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            @forelse ($courierRates as $rate)
                                <tr class="transition-colors hover:bg-neutral-50">
                                    <td class="w-12 px-5 py-3.5 text-center text-xs text-zinc-400">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-zinc-900">
                                        {{ $rate->area_name }}
                                        <span class="ml-1 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $rate->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-500' }}">
                                            {{ $rate->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-zinc-500">{{ $rate->area_code ?: '—' }}</td>
                                    <td class="px-5 py-3.5 font-bold text-zinc-800">Rp {{ number_format($rate->rate_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 text-xs text-zinc-600">{{ $rate->eta_text ?: '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-2" x-data="{ editing: false }">
                                            <button type="button" @click="editing = !editing" class="rounded-xl bg-zinc-100 px-3 py-1.5 text-xs font-bold text-zinc-700 transition hover:bg-zinc-200">
                                                {{ 'Edit' }}
                                            </button>
                                            <form method="POST" action="{{ route('admin.settings.courier-rates.destroy', $rate) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="kurir">
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                                    <x-icon name="trash-2" class="size-3.5" />
                                                </button>
                                            </form>

                                            <div x-show="editing" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4 backdrop-blur-sm" @click.self="editing = false" @keydown.escape.window="editing = false">
                                                <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl">
                                                    <h3 class="text-sm font-bold text-zinc-900">Edit Tarif — {{ $rate->area_name }}</h3>
                                                    <form method="POST" action="{{ route('admin.settings.courier-rates.update', $rate) }}" class="mt-4 grid gap-3">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="tab" value="kurir">
                                                        <div class="grid gap-3 sm:grid-cols-2">
                                                            <input type="text" name="area_name" value="{{ $rate->area_name }}" required maxlength="191" placeholder="* Kecamatan" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                                                            <input type="text" name="area_code" value="{{ $rate->area_code }}" maxlength="32" placeholder="Kode area (opsional)" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                                                        </div>
                                                        <input type="number" name="rate_amount" value="{{ $rate->rate_amount }}" required min="0" step="1" placeholder="Tarif (Rp)" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                                                        <input type="text" name="eta_text" value="{{ $rate->eta_text }}" maxlength="100" placeholder="Estimasi (opsional)" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                                                        <label class="flex cursor-pointer items-center gap-1.5 text-xs text-zinc-600">
                                                            <input type="checkbox" name="is_active" value="1" class="rounded border-zinc-300" @checked($rate->is_active)>
                                                            Aktif
                                                        </label>
                                                        <div class="flex justify-end gap-2 border-t border-neutral-100 pt-3">
                                                            <button type="button" @click="editing = false" class="rounded-xl border border-neutral-200 px-3 py-1.5 text-xs font-bold text-zinc-600 hover:bg-neutral-50">Batal</button>
                                                            <button type="submit" class="rounded-xl bg-brand-yellow px-4 py-1.5 text-xs font-bold text-brand-black hover:bg-brand-yellow-dark">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-zinc-400">
                                        Belum ada tarif kurir toko.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="addOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4 backdrop-blur-sm" @click.self="addOpen = false" @keydown.escape.window="addOpen = false">
                <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl">
                    <h3 class="text-sm font-bold text-zinc-900">Tambah Tarif Kurir</h3>
                    <form method="POST" action="{{ route('admin.settings.courier-rates.store') }}" class="mt-4 grid gap-3">
                        @csrf
                        <input type="hidden" name="tab" value="kurir">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <input type="text" name="area_name" placeholder="* Kecamatan (mis. Coblong)" required maxlength="191" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                            <input type="text" name="area_code" placeholder="Kode area (opsional)" maxlength="32" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <input type="number" name="rate_amount" placeholder="* Tarif (Rp)" required min="0" step="1" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                            <input type="text" name="eta_text" placeholder="Estimasi (opsional)" maxlength="100" class="w-full rounded-xl border border-neutral-200 px-3 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                        </div>
                        <label class="flex cursor-pointer items-center gap-1.5 text-xs text-zinc-600">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-zinc-300">
                            Jadikan aktif
                        </label>
                        <div class="flex justify-end gap-2 border-t border-neutral-100 pt-3">
                            <button type="button" @click="addOpen = false" class="rounded-xl border border-neutral-200 px-3 py-1.5 text-xs font-bold text-zinc-600 hover:bg-neutral-50">Batal</button>
                            <button type="submit" class="rounded-xl bg-brand-yellow px-4 py-1.5 text-xs font-bold text-brand-black hover:bg-brand-yellow-dark">Tambah Tarif</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
