<x-layouts.app :title="'Detail Customer - Pixel Komunika'">
    <x-layout.admin-page
        title="Detail pendaftar"
        description="Pola detail ini menjadi baseline untuk future admin module dengan meta card, status, dan related records."
    >
        <x-slot name="actions">
            <flux:button href="{{ route('admin.customers.index') }}" variant="ghost" size="sm" icon="arrow-left">
                Kembali
            </flux:button>
        </x-slot>

        <div class="grid gap-6 md:grid-cols-3">
            <x-ui.stat-card label="Status" :value="$customer->verification_status" icon="badge-check" variant="admin" />
            <x-ui.stat-card label="Alamat" :value="(string) $customer->user->addresses->count()" icon="map-pin" variant="admin" />
            <x-ui.stat-card label="Reviewer" :value="$customer->reviewer?->name ?: '-'" icon="user-circle" variant="admin" />
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <x-ui.section-card :title="$customer->user->name" description="Ringkasan identitas customer untuk proses review admin." variant="admin">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-ui.status-badge :status="$customer->verification_status" />
                        @if ($customer->reviewed_at)
                            <p class="text-sm text-zinc-500">Direview pada {{ $customer->reviewed_at->format('Y-m-d H:i') }}</p>
                        @endif
                    </div>

                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Email</dt>
                            <dd class="mt-1 text-sm">{{ $customer->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Phone</dt>
                            <dd class="mt-1 text-sm">{{ $customer->user->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Nama usaha</dt>
                            <dd class="mt-1 text-sm">{{ $customer->business_name ?: 'Belum diisi' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Direview oleh</dt>
                            <dd class="mt-1 text-sm">{{ $customer->reviewer?->name ?: '-' }}</dd>
                        </div>
                    </dl>

                    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-4">
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
            </x-ui.section-card>

            <x-ui.section-card title="Alamat pelanggan" description="Related record pattern untuk entity admin yang punya child data." variant="admin">
                <div class="grid gap-4">
                    @forelse ($customer->user->addresses as $address)
                        <div class="space-y-3 rounded-[1.6rem] border border-zinc-200 bg-zinc-50/75 p-4">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-zinc-900">{{ $address->label ?: 'Alamat pelanggan' }}</h3>
                                @if ($address->is_default)
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">Default</span>
                                @endif
                            </div>

                            <p class="text-sm text-zinc-700">{{ $address->recipient_name }} • {{ $address->recipient_phone }}</p>
                            <p class="text-sm leading-relaxed text-zinc-600">
                                {{ $address->address_line }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }} {{ $address->postal_code }}
                            </p>
                        </div>
                    @empty
                        <x-ui.empty-state title="Belum ada alamat" description="Customer ini belum menyimpan alamat pengiriman." icon="map-pin" />
                    @endforelse
                </div>
            </x-ui.section-card>
        </div>
    </x-layout.admin-page>
</x-layouts.app>
