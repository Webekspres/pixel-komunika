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
                    <dt class="text-[11px] font-semibold tracking-wide text-zinc-400 uppercase">Lokasi Gudang</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-zinc-900">
                        @if($store->origin_biteship_label)
                            {{ $store->origin_biteship_label }}
                            <span class="ml-1 rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700">DB</span>
                            <span class="block mt-1 font-mono text-xs font-normal text-zinc-500">{{ $store->origin_biteship_area_id }} · {{ $store->origin_postal_code }}</span>
                        @elseif($store->origin_biteship_area_id)
                            <span class="font-mono">{{ $store->origin_biteship_area_id }}</span> <span class="ml-1 rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700">DB</span>
                            <span class="block mt-1 text-xs font-normal text-zinc-500">{{ $store->origin_postal_code }}</span>
                        @elseif(config('biteship.origin_area_id'))
                            <span class="font-mono">{{ config('biteship.origin_area_id') }}</span> <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-700">fallback .env</span>
                            <span class="block mt-1 text-xs font-normal text-zinc-500">Regol, Bandung, Jawa Barat (40252) — Jl. Sawahkurung</span>
                        @else
                            —
                        @endif
                    </dd>
                    <p class="mt-1 text-[11px] text-zinc-400">Dipakai untuk kalkulasi ongkir Biteship</p>
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

                <!-- Lokasi Gudang Biteship (origin) — DB primary, .env fallback -->
                <div
                    x-data="{
                        query: '{{ old('origin_biteship_label', $store->origin_biteship_label ?? '') }}',
                        results: [],
                        isLoading: false,
                        isOpen: false,
                        selectedId: '{{ old('origin_biteship_area_id', $store->origin_biteship_area_id ?? '') }}',
                        selectedLabel: '{{ addslashes(old('origin_biteship_label', $store->origin_biteship_label ?? '')) }}',
                        postalCode: '{{ old('origin_postal_code', $store->origin_postal_code ?? '') }}',
                        async search() {
                            if (this.query.length < 2) { this.results = []; this.isOpen = false; return; }
                            this.isLoading = true;
                            this.isOpen = true;
                            try {
                                const res = await fetch(`/api/areas/search?q=${encodeURIComponent(this.query)}`);
                                this.results = res.ok ? await res.json() : [];
                            } catch(e) { this.results = []; }
                            this.isLoading = false;
                        },
                        select(item) {
                            this.selectedId = item.biteship_area_id || item.id || '';
                            this.selectedLabel = item.label || '';
                            this.postalCode = item.postal_code || this.postalCode;
                            this.query = item.label || '';
                            this.results = [];
                            this.isOpen = false;
                            const idInput = document.getElementById('origin_biteship_area_id');
                            if (idInput) idInput.value = this.selectedId;
                            const labelInput = document.getElementById('origin_biteship_label');
                            if (labelInput) labelInput.value = this.selectedLabel;
                            const pcInput = document.getElementById('origin_postal_code');
                            if (pcInput && item.postal_code) pcInput.value = item.postal_code;
                        },
                        clear() {
                            this.selectedId = '';
                            this.selectedLabel = '';
                            this.postalCode = '';
                            this.query = '';
                            this.results = [];
                            this.isOpen = false;
                            const idInput = document.getElementById('origin_biteship_area_id');
                            if (idInput) idInput.value = '';
                            const labelInput = document.getElementById('origin_biteship_label');
                            if (labelInput) labelInput.value = '';
                            const pcInput = document.getElementById('origin_postal_code');
                            if (pcInput) pcInput.value = '';
                        }
                    }"
                    class="grid gap-3 rounded-xl border border-neutral-200 bg-neutral-50/50 p-3.5"
                    @click.away="isOpen = false"
                >
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-zinc-700">Lokasi Gudang (Biteship Area) <span class="text-zinc-400 font-normal">— untuk kalkulasi ongkir Biteship</span></label>
                        <input type="hidden" name="origin_biteship_area_id" id="origin_biteship_area_id" :value="selectedId" value="{{ old('origin_biteship_area_id', $store->origin_biteship_area_id ?? '') }}">
                        <input type="hidden" name="origin_biteship_label" id="origin_biteship_label" :value="selectedLabel" value="{{ old('origin_biteship_label', $store->origin_biteship_label ?? '') }}">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-400">
                                    <x-icon name="search" class="size-4" />
                                </div>
                                <input
                                    type="text"
                                    x-model="query"
                                    @input.debounce.300ms="search()"
                                    @focus="if(query.length>=2) isOpen = true"
                                    placeholder="Ketik kecamatan/kota/kode pos gudang (mis. Regol 40252)..."
                                    class="w-full rounded-xl border border-neutral-200 bg-white pl-9 pr-9 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                                >
                                <div x-show="isLoading" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="size-4 animate-spin text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                </div>
                            </div>
                            <button type="button" x-show="selectedId" @click="clear()" class="shrink-0 rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-xs font-bold text-zinc-600 hover:bg-neutral-50">Hapus</button>
                        </div>
                        <!-- Selected preview -->
                        <div x-show="selectedId" x-cloak class="mt-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs">
                            <p class="font-semibold text-emerald-900" x-text="selectedLabel || 'Lokasi terpilih'"></p>
                            <p class="mt-0.5 font-mono text-[11px] text-emerald-700" x-text="selectedId"></p>
                            <p class="mt-0.5 text-[11px] text-emerald-600">Kode pos: <span x-text="postalCode || '—'"></span></p>
                        </div>
                        <!-- Fallback info -->
                        <p class="mt-1.5 text-[11px] leading-snug text-zinc-500">
                            Efektif: <span class="font-semibold text-zinc-700">
                                @if($store->origin_biteship_label)
                                    {{ $store->origin_biteship_label }} ({{ $store->origin_biteship_area_id }})
                                @elseif($store->origin_biteship_area_id)
                                    {{ $store->origin_biteship_area_id }}
                                @elseif(config('biteship.origin_area_id'))
                                    Regol, Bandung, Jawa Barat ({{ config('biteship.origin_area_id') }}) <span class="text-amber-600">· fallback .env</span>
                                @else
                                    — (akan pakai .env)
                                @endif
                            </span>

                        </p>
                        <!-- Dropdown -->
                        <div x-show="isOpen && results.length > 0" x-cloak class="relative">
                            <div class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-neutral-200 bg-white py-1 shadow-lg">
                                <template x-for="item in results" :key="item.id">
                                    <button type="button" @click="select(item)" class="w-full text-left px-3.5 py-2.5 text-xs hover:bg-amber-50/80 transition border-b border-zinc-50 last:border-0">
                                        <p class="font-bold text-zinc-900" x-text="item.label"></p>
                                        <p class="text-[11px] text-zinc-500 mt-0.5"><span x-text="item.district_name"></span> • <span x-text="item.city_name"></span> • <span x-text="item.province_name"></span> <span x-show="item.postal_code" x-text="'('+item.postal_code+')'"></span></p>
                                        <p class="font-mono text-[10px] text-zinc-400" x-text="item.biteship_area_id || item.id"></p>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <p x-show="isOpen && !isLoading && results.length===0 && query.length>=2" x-cloak class="mt-1 text-xs text-zinc-500">Tidak ada hasil — coba kata kunci lain atau isi manual ID di bawah.</p>
                    </div>
                    <details class="group">
                        <summary class="cursor-pointer text-[11px] font-semibold text-zinc-500 hover:text-zinc-700">Isi manual</summary>
                        <div class="mt-2 grid gap-2">
                            <input type="text" placeholder="Label: Regol, Bandung, Jawa Barat (40252)" x-model="selectedLabel" @input="document.getElementById('origin_biteship_label').value = selectedLabel" class="w-full rounded-xl border border-neutral-200 bg-white px-3 py-2 text-xs text-zinc-700 focus:border-brand-yellow focus:outline-none">
                            <input type="text" placeholder="ID: IDNP9IDNC22IDND2043IDZ40132" x-model="selectedId" @input="document.getElementById('origin_biteship_area_id').value = selectedId" class="w-full rounded-xl border border-neutral-200 bg-white px-3 py-2 font-mono text-xs text-zinc-700 focus:border-brand-yellow focus:outline-none">
                        </div>
                        <p class="mt-1 text-[11px] text-zinc-400">Label readable untuk admin awam, ID dari Biteship Maps API (contoh Regol 40252).</p>
                    </details>
                </div>

                <input type="hidden" name="origin_postal_code" id="origin_postal_code" :value="postalCode" value="{{ old('origin_postal_code', $store->origin_postal_code) }}">

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
                                                    <form method="POST" action="{{ route('admin.settings.courier-rates.update', $rate) }}" class="mt-4 grid gap-3" x-data="{ query: '{{ addslashes($rate->area_name) }}', areaName: '{{ addslashes($rate->area_name) }}', areaCode: '{{ addslashes($rate->area_code ?? '') }}', results: [], loading: false, open: false, async search(){ if(this.query.length<2){this.results=[];this.open=false;return;} this.loading=true; this.open=true; try{ const r=await fetch(`/api/areas/search?q=${encodeURIComponent(this.query)}`); this.results=r.ok?await r.json():[] }catch(e){this.results=[]} this.loading=false; }, select(item){ this.areaName=item.district_name||''; this.areaCode=item.biteship_area_id||item.id||''; this.query=this.areaName; this.open=false; this.results=[]; } }" @click.away="open=false">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="tab" value="kurir">
                                                        <input type="hidden" name="area_name" :value="areaName">
                                                        <input type="hidden" name="area_code" :value="areaCode">
                                                        <div class="relative">
                                                            <input type="text" x-model="query" @input.debounce.300ms="areaName=query; search()" @focus="if(query.length>=2) open=true" placeholder="* Kecamatan — ketik & pilih dari Biteship" required maxlength="191" class="w-full rounded-xl border border-neutral-200 pl-3 pr-9 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                                                            <div x-show="loading" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"><svg class="size-4 animate-spin text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg></div>
                                                            <div x-show="open && results.length>0" x-cloak class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-zinc-200 bg-white py-1 shadow-lg">
                                                                <template x-for="item in results" :key="item.id">
                                                                    <button type="button" @click="select(item)" class="w-full text-left px-3.5 py-2.5 text-xs hover:bg-amber-50/80 transition border-b border-zinc-50 last:border-0">
                                                                        <p class="font-bold text-zinc-900" x-text="item.label"></p>
                                                                        <p class="text-[11px] text-zinc-500"><span x-text="item.district_name"></span> • <span x-text="item.city_name"></span> • <span x-text="item.province_name"></span> <span x-show="item.postal_code" x-text="'('+item.postal_code+')'"></span></p>
                                                                        <p class="font-mono text-[10px] text-zinc-400" x-text="item.biteship_area_id||item.id"></p>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="grid gap-2 sm:grid-cols-2">
                                                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs">
                                                                <span class="text-zinc-500">Kecamatan:</span> <span class="font-semibold text-zinc-900" x-text="areaName||'—'"></span>
                                                            </div>
                                                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-mono">
                                                                <span class="text-zinc-500">Kode:</span> <span class="text-zinc-700" x-text="areaCode||'—'"></span>
                                                            </div>
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
                    <form method="POST" action="{{ route('admin.settings.courier-rates.store') }}" class="mt-4 grid gap-3" x-data="{ query: '', areaName: '', areaCode: '', results: [], loading: false, open: false, async search(){ if(this.query.length<2){this.results=[];this.open=false;return;} this.loading=true; this.open=true; try{ const r=await fetch(`/api/areas/search?q=${encodeURIComponent(this.query)}`); this.results=r.ok?await r.json():[] }catch(e){this.results=[]} this.loading=false; }, select(item){ this.areaName=item.district_name||''; this.areaCode=item.biteship_area_id||item.id||''; this.query=this.areaName; this.open=false; this.results=[]; } }" @click.away="open=false">
                        @csrf
                        <input type="hidden" name="tab" value="kurir">
                        <input type="hidden" name="area_name" :value="areaName" :required="!areaName">
                        <input type="hidden" name="area_code" :value="areaCode">
                        <div class="relative">
                            <input type="text" x-model="query" @input.debounce.300ms="areaName=query; search()" @focus="if(query.length>=2) open=true" placeholder="* Kecamatan — ketik & pilih dari Biteship (mis. Coblong)" required maxlength="191" class="w-full rounded-xl border border-neutral-200 pl-3 pr-9 py-2.5 text-sm focus:border-brand-yellow focus:outline-none">
                            <div x-show="loading" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"><svg class="size-4 animate-spin text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg></div>
                            <div x-show="open && results.length>0" x-cloak class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-zinc-200 bg-white py-1 shadow-lg">
                                <template x-for="item in results" :key="item.id">
                                    <button type="button" @click="select(item)" class="w-full text-left px-3.5 py-2.5 text-xs hover:bg-amber-50/80 transition border-b border-zinc-50 last:border-0">
                                        <p class="font-bold text-zinc-900" x-text="item.label"></p>
                                        <p class="text-[11px] text-zinc-500"><span x-text="item.district_name"></span> • <span x-text="item.city_name"></span> • <span x-text="item.province_name"></span> <span x-show="item.postal_code" x-text="'('+item.postal_code+')'"></span></p>
                                        <p class="font-mono text-[10px] text-zinc-400" x-text="item.biteship_area_id||item.id"></p>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs">
                                <span class="text-zinc-500">Kecamatan terpilih:</span> <span class="font-semibold text-zinc-900" x-text="areaName||'—'"></span>
                            </div>
                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-mono">
                                <span class="text-zinc-500">Kode Biteship:</span> <span class="text-zinc-700" x-text="areaCode||'— (opsional)'"></span>
                            </div>
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
