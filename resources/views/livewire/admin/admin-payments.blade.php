<div class="space-y-5 p-6">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <h1 class="text-xl font-black text-zinc-900">Pembayaran</h1>
            <p class="mt-0.5 text-sm text-zinc-500">Verifikasi bukti transfer dan kelola riwayat pembayaran.</p>
        </div>
    </div>

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

    <div class="rounded-2xl border border-neutral-100 bg-white">
        <div class="flex flex-col gap-3 border-b border-neutral-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-1.5">
                @foreach ([
                    'all' => 'Semua',
                    'pending' => 'Menunggu Verifikasi',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                ] as $value => $label)
                    <button
                        type="button"
                        wire:click="$set('statusFilter', '{{ $value }}')"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition
                            {{ $statusFilter === $value ? 'bg-zinc-900 text-white' : 'text-zinc-600 hover:bg-neutral-100' }}"
                    >
                        {{ $label }}
                        @if ($value === 'pending' && $counts['pending'] > 0)
                            <span class="rounded-full px-1.5 py-0.5 text-[10px] font-black {{ $statusFilter === $value ? 'bg-amber-400 text-amber-950' : 'bg-amber-100 text-amber-800' }}">
                                {{ $counts['pending'] }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative w-full sm:w-64">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" />
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari No. Order / Pelanggan / Bank..."
                        class="w-full rounded-xl border border-neutral-200 py-2.5 pr-3 pl-10 text-sm focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/20 focus:outline-none"
                    >
                </div>
                <select
                    wire:model.live="bankFilter"
                    class="rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm text-zinc-700 focus:border-brand-yellow focus:outline-none"
                >
                    <option value="">Semua Bank Tujuan</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->bank_name }} — {{ $bank->account_number }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($proofs->isEmpty())
            <div class="px-5 py-12">
                <x-ui.empty-state
                    title="Tidak Ada Pembayaran"
                    description="Belum ada bukti pembayaran yang sesuai dengan filter."
                    icon="receipt"
                />
            </div>
        @else
            <flux:table :paginate="$proofs" container:class="[&_ui-table-scroll-area]:max-h-[70vh]">
                <flux:table.columns>
                    <flux:table.column align="center" class="w-12">No</flux:table.column>
                    <flux:table.column class="w-44">No. Order</flux:table.column>
                    <flux:table.column>Pelanggan</flux:table.column>
                    <flux:table.column>Bank Tujuan</flux:table.column>
                    <flux:table.column align="end" class="w-36">Nominal</flux:table.column>
                    <flux:table.column class="w-44">Waktu Pengajuan</flux:table.column>
                    <flux:table.column class="w-40">Status</flux:table.column>
                    <flux:table.column align="end" class="w-40">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($proofs as $proof)
                        <flux:table.row :key="$proof->id" class="transition-colors hover:bg-neutral-50">
                            <flux:table.cell align="center" class="w-12 text-xs text-zinc-400">{{ $proofs->firstItem() + $loop->index }}</flux:table.cell>
                            <flux:table.cell>
                                <a href="{{ route('admin.orders.show', $proof->order) }}" wire:navigate class="font-bold text-zinc-900 hover:underline">
                                    #{{ $proof->order->order_number }}
                                </a>
                            </flux:table.cell>
                            <flux:table.cell>
                                <p class="font-medium text-zinc-900">{{ $proof->order->user?->name ?? $proof->order->recipient_name }}</p>
                                <p class="text-xs text-zinc-500">{{ $proof->order->recipient_phone }}</p>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($proof->payment?->bankAccount)
                                    <p class="font-medium text-zinc-900">{{ $proof->payment->bankAccount->bank_name }}</p>
                                    <p class="text-xs text-zinc-500">a.n. {{ $proof->payment->bankAccount->account_holder }}</p>
                                @else
                                    <p class="text-xs text-zinc-500">—</p>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="end" class="font-bold text-zinc-900">
                                Rp {{ number_format($proof->amount, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell class="text-xs whitespace-nowrap text-zinc-500">
                                {{ $proof->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <x-ui.status-badge :status="$proof->status" />
                            </flux:table.cell>
                            <flux:table.cell align="end" class="w-40">
                                @if ($proof->status === 'pending')
                                    <flux:button variant="primary" color="amber" size="sm" wire:click="openReview({{ $proof->id }})">
                                        Periksa &amp; Verifikasi
                                    </flux:button>
                                @else
                                    <flux:button variant="ghost" size="sm" wire:click="openReview({{ $proof->id }})">
                                        Detail
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </div>

    @if ($showReviewModal && $reviewingProof)
        <flux:modal wire:model.self="showReviewModal" name="review-payment-modal" class="md:max-w-4xl" @close="resetReviewModal">
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">
                        Review Pembayaran — Order #{{ $reviewingProof->order->order_number }}
                    </flux:heading>
                    <flux:subheading class="mt-2">
                        {{ $reviewingProof->order->user?->name ?? $reviewingProof->order->recipient_name }}
                        ({{ $reviewingProof->order->recipient_phone }})
                    </flux:subheading>
                    <div class="mt-3">
                        <x-ui.status-badge :status="$reviewingProof->status" />
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <p class="mb-2 text-xs font-bold tracking-wide text-zinc-500 uppercase">Preview Bukti Transfer</p>
                        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-3">
                            @if ($this->isPdf($reviewingProof))
                                <iframe
                                    src="{{ $this->previewUrl($reviewingProof) }}"
                                    title="Bukti transfer PDF"
                                    class="h-80 w-full rounded-lg bg-white"
                                ></iframe>
                            @else
                                <img
                                    src="{{ $this->previewUrl($reviewingProof) }}"
                                    alt="Bukti transfer {{ $reviewingProof->bank_name }} a.n {{ $reviewingProof->account_name }}"
                                    class="max-h-80 w-full rounded-lg bg-white object-contain"
                                >
                            @endif
                        </div>
                        <div class="mt-2">
                            <flux:button variant="ghost" size="sm" href="{{ $this->previewUrl($reviewingProof) }}" target="_blank">
                                Buka Ukuran Asli
                            </flux:button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-xl bg-zinc-50 p-4 text-sm">
                            <p class="text-xs font-bold tracking-wide text-zinc-500 uppercase">Rekening Tujuan Toko</p>
                            @if ($reviewingProof->payment?->bankAccount)
                                <p class="mt-2 font-bold text-zinc-900">{{ $reviewingProof->payment->bankAccount->bank_name }}</p>
                                <p class="font-mono text-xs text-zinc-600">{{ $reviewingProof->payment->bankAccount->account_number }}</p>
                                <p class="text-xs text-zinc-600">a.n. {{ $reviewingProof->payment->bankAccount->account_holder }}</p>
                            @else
                                <p class="mt-2 text-xs text-zinc-500">Rekening tujuan tidak tersedia.</p>
                            @endif
                        </div>

                        <dl class="space-y-2.5 rounded-xl border border-neutral-200 p-4 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-zinc-500">Tagihan Order</dt>
                                <dd class="font-semibold text-zinc-800">Rp {{ number_format($reviewingProof->order->grand_total, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-zinc-500">Nominal Diajukan</dt>
                                <dd class="font-bold text-zinc-900">Rp {{ number_format($reviewingProof->amount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-zinc-500">Dari Bank</dt>
                                <dd class="font-semibold text-zinc-700">{{ $reviewingProof->bank_name }} a.n. {{ $reviewingProof->account_name }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-zinc-500">Diupload</dt>
                                <dd class="font-semibold text-zinc-700">
                                    {{ $reviewingProof->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                </dd>
                            </div>
                        </dl>

                        @if ($reviewingProof->reviewer)
                            <div class="rounded-xl border border-zinc-200 bg-amber-50 p-4 text-xs">
                                <p class="font-bold text-amber-900">Review Sebelumnya</p>
                                <p class="mt-1 text-amber-800">
                                    {{ $reviewingProof->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                    oleh {{ $reviewingProof->reviewer->name }}
                                    @if ($reviewingProof->reviewed_at)
                                        pada {{ $reviewingProof->reviewed_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                    @endif
                                </p>
                                @if ($reviewingProof->status === 'rejected' && $reviewingProof->rejection_reason)
                                    <p class="mt-1 text-amber-700">Alasan: {{ $reviewingProof->rejection_reason }}</p>
                                @endif
                            </div>
                        @endif

                        <div>
                            <flux:textarea
                                wire:model="adminNote"
                                label="Catatan Admin / Alasan Penolakan"
                                placeholder="{{ $reviewingProof->status === 'pending' ? 'Wajib diisi jika menolak (min. 5 karakter)' : '' }}"
                                rows="3"
                                :disabled="$reviewingProof->status !== 'pending'"
                            />
                            @error('adminNote')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-neutral-100 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal / Tutup</flux:button>
                    </flux:modal.close>

                    @if ($reviewingProof->status === 'pending')
                        <flux:button variant="danger" wire:click="rejectPayment">Tolak Pembayaran</flux:button>
                        <flux:button variant="primary" color="emerald" wire:click="approvePayment">Terima Pembayaran</flux:button>
                    @endif
                </div>
            </div>
        </flux:modal>
    @endif
</div>