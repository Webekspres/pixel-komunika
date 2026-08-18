@php
    $isPending = $customer->verification_status === \App\Models\CustomerProfile::PENDING;
    $isActive = $customer->verification_status === \App\Models\CustomerProfile::ACTIVE;
    $isRejected = $customer->verification_status === \App\Models\CustomerProfile::REJECTED;
    $isSuspended = $customer->verification_status === \App\Models\CustomerProfile::SUSPENDED;

    $headerDescription = trim(
        ($customer->business_name ?: 'Usaha belum diisi')
        . ($customer->reseller_account_number ? ' · Akun '.$customer->reseller_account_number : '')
    );

    $lastReviewed = $customer->reviewed_at?->timezone('Asia/Jakarta')->format('d M Y H:i');
@endphp

<x-layout.admin-page
    :title="$customer->user->name"
    :description="$headerDescription"
    :back-href="route('admin.customers.index')"
    :back-label="'Kembali ke daftar'"
>
    <x-slot name="actions">
        <x-ui.status-badge :status="$customer->verification_status" />

        @if ($isPending)
            <flux:button wire:click="approve" variant="primary" color="amber" wire:loading.attr="disabled">
                Setujui Pendaftaran
            </flux:button>
            <flux:modal.trigger :name="'reject-modal'">
                <flux:button variant="danger">Tolak Pendaftaran</flux:button>
            </flux:modal.trigger>
        @elseif ($isActive)
            <flux:modal.trigger :name="'suspend-modal'">
                <flux:button variant="primary" color="amber">Tangguhkan Akun</flux:button>
            </flux:modal.trigger>
        @elseif ($isSuspended)
            <flux:button wire:click="reactivate" variant="primary" color="emerald" wire:loading.attr="disabled">
                Aktifkan Kembali
            </flux:button>
        @elseif ($isRejected)
            <flux:button wire:click="approve" variant="outline" wire:loading.attr="disabled">
                Tinjau & Setujui
            </flux:button>
        @endif
    </x-slot>

    {{-- Flash feedback --}}
    @if (session()->has('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Alert banner untuk status ditolak / ditangguhkan --}}
    @if ($isRejected || $isSuspended)
        <div class="rounded-2xl border p-5 {{ $isRejected ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50' }}">
            <div class="flex items-start gap-3">
                <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl {{ $isRejected ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700' }}">
                    <x-icon name="circle-alert" class="size-5" />
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold {{ $isRejected ? 'text-red-800' : 'text-amber-800' }}">
                        {{ $isRejected ? 'Pendaftaran ditolak' : 'Akun ditangguhkan' }}
                    </p>
                    <p class="mt-1 text-sm leading-relaxed text-zinc-700">{{ $customer->rejection_reason }}</p>
                    <p class="mt-1.5 text-xs text-zinc-500">
                        Direview oleh {{ $customer->reviewer?->name ?: 'Admin' }}
                        @if ($lastReviewed) · {{ $lastReviewed }} @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid gap-5 md:grid-cols-3">
        <x-ui.stat-card label="Alamat Terdaftar" :value="(string) $customer->user->addresses->count()" icon="map-pin" variant="admin" />
        <x-ui.stat-card label="Tanggal Terdaftar" :value="$customer->created_at->timezone('Asia/Jakarta')->format('d M Y')" icon="calendar" variant="admin" />
        <x-ui.stat-card label="Review Terakhir" :value="$lastReviewed ?? 'Belum direview'" icon="badge-check" variant="admin" />
    </div>

    {{-- Main content: 60% kiri, 40% kanan --}}
    <div class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">
        <div class="space-y-6">
            <x-ui.section-card title="Informasi Usaha & Kontak" description="Data registrasi dan kontak utama pelanggan." variant="admin">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Nama PIC</dt>
                        <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $customer->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Nama Usaha</dt>
                        <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $customer->business_name ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Email</dt>
                        <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $customer->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Telepon / WhatsApp</dt>
                        <dd class="mt-1 text-sm font-semibold text-zinc-900">{{ $customer->user->phone ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500">Nomor Reseller</dt>
                        <dd class="mt-1 font-mono text-sm font-semibold text-zinc-900">{{ $customer->reseller_account_number ?: '—' }}</dd>
                    </div>
                </dl>
            </x-ui.section-card>

            <x-ui.section-card title="Riwayat & Audit Verifikasi" description="Status terkini dan jejak review admin." variant="admin">
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-zinc-50 px-4 py-3">
                        <span class="text-zinc-500">Status terkini</span>
                        <x-ui.status-badge :status="$customer->verification_status" />
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-zinc-50 px-4 py-3">
                        <span class="text-zinc-500">Diverifikasi oleh</span>
                        <span class="font-semibold text-zinc-900">{{ $customer->reviewer?->name ?: '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-zinc-50 px-4 py-3">
                        <span class="text-zinc-500">Waktu verifikasi</span>
                        <span class="font-semibold text-zinc-900">{{ $lastReviewed ?? 'Belum pernah' }}</span>
                    </div>
                </div>
            </x-ui.section-card>
        </div>

        <x-ui.section-card
            title="Daftar Alamat Pengiriman"
            :description="$customer->user->addresses->count().' alamat tersimpan.'"
            variant="admin"
        >
            <div class="grid gap-3">
                @forelse ($customer->user->addresses as $address)
                    <div class="rounded-xl border border-zinc-100 bg-zinc-50/80 p-4">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-bold text-zinc-900">{{ $address->label ?: 'Alamat' }}</p>
                            @if ($address->is_default)
                                <span class="rounded-full bg-brand-yellow/20 px-2 py-0.5 text-[10px] font-bold text-brand-yellow-dark">Default</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-zinc-600">{{ $address->recipient_name }} · {{ $address->recipient_phone }}</p>
                        <p class="mt-1 text-xs leading-relaxed text-zinc-500">
                            {{ $address->address_line }}{{ $address->district_name ? ', '.$address->district_name : '' }}, {{ $address->city_name }}, {{ $address->province_name }}
                            @if ($address->postal_code) {{ $address->postal_code }} @endif
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400">Belum ada alamat tersimpan.</p>
                @endforelse
            </div>
        </x-ui.section-card>
    </div>

    {{-- Modal: alasan penolakan --}}
    <flux:modal name="reject-modal">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">Tolak Pendaftaran</flux:heading>
                <flux:subheading>Alasan penolakan akan ditampilkan kepada pelanggan dan dicatat pada riwayat verifikasi.</flux:subheading>
            </div>

            <flux:textarea
                wire:model="rejectionReason"
                label="Alasan penolakan"
                rows="4"
                placeholder="Tulis alasan penolakan (minimal 5 karakter)..."
            />

            @error('rejectionReason')
                <p class="text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="reject" wire:loading.attr="disabled">
                    Tolak Pendaftaran
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: alasan penangguhan --}}
    <flux:modal name="suspend-modal">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">Tangguhkan Akun</flux:heading>
                <flux:subheading>Alasan penangguhan akan ditampilkan kepada pelanggan dan dicatat pada riwayat verifikasi.</flux:subheading>
            </div>

            <flux:textarea
                wire:model="rejectionReason"
                label="Alasan penangguhan"
                rows="4"
                placeholder="Tulis alasan penangguhan (minimal 5 karakter)..."
            />

            @error('rejectionReason')
                <p class="text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="amber" wire:click="suspend" wire:loading.attr="disabled">
                    Tangguhkan Akun
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-layout.admin-page>
