<x-layouts.app :title="'Detail Pelanggan - Pixel Komunika'">
    <div class="space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <a href="{{ route('admin.customers.index') }}" wire:navigate class="mb-2 inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-700">
                    <x-icon name="arrow-left" class="size-4" />
                    Kembali ke daftar
                </a>
                <h1 class="text-xl font-black text-zinc-900">{{ $customer->user->name }}</h1>
                <p class="mt-0.5 text-sm text-zinc-500">{{ $customer->business_name ?: 'Usaha belum diisi' }}</p>
            </div>
            <x-ui.status-badge :status="$customer->verification_status" />
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.stat-card label="Status" :value="$customer->verification_status === \App\Models\CustomerProfile::ACTIVE ? 'Aktif' : ($customer->verification_status === \App\Models\CustomerProfile::PENDING ? 'Menunggu' : $customer->verification_status)" icon="badge-check" variant="admin" />
            <x-ui.stat-card label="Alamat" :value="(string) $customer->user->addresses->count()" icon="map-pin" variant="admin" />
            <x-ui.stat-card label="Reviewer" :value="$customer->reviewer?->name ?: '-'" icon="user" variant="admin" />
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-2xl border border-neutral-100 bg-white p-5 sm:p-6">
                <h2 class="mb-4 text-sm font-bold text-zinc-900">Identitas & review</h2>

                @if ($customer->reviewed_at)
                    <p class="mb-4 text-sm text-zinc-500">Direview pada {{ $customer->reviewed_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</p>
                @endif

                <dl class="mb-6 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Email</dt>
                        <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $customer->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Telepon</dt>
                        <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $customer->user->phone ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Nama usaha</dt>
                        <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $customer->business_name ?: 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Direview oleh</dt>
                        <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $customer->reviewer?->name ?: '—' }}</dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-4 border-t border-neutral-100 pt-5">
                    @csrf
                    @method('PATCH')

                    <flux:textarea
                        name="reason"
                        label="Catatan admin"
                        rows="3"
                        placeholder="Alasan penolakan / penangguhan"
                    >{{ $customer->rejection_reason }}</flux:textarea>

                    <div class="flex flex-wrap gap-2">
                        <flux:button type="submit" name="action" value="approve" variant="primary" color="amber">
                            Setujui
                        </flux:button>
                        <flux:button type="submit" name="action" value="reactivate" variant="filled">
                            Aktifkan
                        </flux:button>
                        <flux:button type="submit" name="action" value="reject" variant="danger">
                            Tolak
                        </flux:button>
                        <flux:button type="submit" name="action" value="suspend" variant="subtle">
                            Tangguhkan
                        </flux:button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-neutral-100 bg-white p-5 sm:p-6">
                <h2 class="mb-4 text-sm font-bold text-zinc-900">Alamat pelanggan</h2>
                <div class="grid gap-3">
                    @forelse ($customer->user->addresses as $address)
                        <div class="space-y-2 rounded-2xl border border-zinc-100 bg-zinc-50/80 p-4">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-bold text-zinc-900">{{ $address->label }}</p>
                                @if ($address->is_default)
                                    <span class="rounded-full bg-brand-yellow/20 px-2 py-0.5 text-[10px] font-bold text-brand-yellow-dark">Default</span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-600">{{ $address->recipient_name }} · {{ $address->recipient_phone }}</p>
                            <p class="text-xs leading-relaxed text-zinc-500">
                                {{ $address->address_line }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }}
                                @if ($address->postal_code) {{ $address->postal_code }} @endif
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-400">Belum ada alamat tersimpan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
