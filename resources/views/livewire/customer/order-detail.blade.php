@php
    $isWaitingPayment = in_array($order->status, ['unpaid', 'payment_pending', 'payment_rejected'], true);
    $isPaymentSubmitted = $order->status === 'payment_pending';
    $isPaidOrLater = in_array($order->status, ['paid', 'processing', 'packed', 'shipped', 'completed'], true);
    $isCancelled = $order->status === 'cancelled';
    $isReturned = $order->status === 'returned';

    $stepStatuses = [
        'ordered' => ['label' => 'Dipesan', 'icon' => 'package'],
        'payment_verified' => ['label' => 'Pembayaran Diverifikasi', 'icon' => 'credit-card'],
        'processing' => ['label' => 'Diproses / Dikemas', 'icon' => 'settings'],
        'shipped' => ['label' => 'Dikirim', 'icon' => 'truck'],
        'completed' => ['label' => 'Selesai', 'icon' => 'check-circle'],
    ];

    $currentStep = match (true) {
        $isCancelled || $isReturned => 'cancelled',
        $order->status === 'completed' => 'completed',
        $order->status === 'shipped' => 'shipped',
        in_array($order->status, ['processing', 'packed', 'paid']) => 'processing',
        $order->status === 'payment_pending' => 'payment_verified',
        default => 'ordered',
    };

    $stepOrder = ['ordered', 'payment_verified', 'processing', 'shipped', 'completed'];
    $currentStepIndex = array_search($currentStep, $stepOrder);
@endphp

<div
    x-data="{
        copiedToast: false,
        toastMessage: '',
        copy(text, message = 'Tersalin ke clipboard!') {
            if (!text) return;
            const triggerToast = () => {
                this.toastMessage = message;
                this.copiedToast = true;
                setTimeout(() => { this.copiedToast = false; }, 2200);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(triggerToast).catch(() => {
                    this.fallbackCopy(text, triggerToast);
                });
            } else {
                this.fallbackCopy(text, triggerToast);
            }
        },
        fallbackCopy(text, callback) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                callback();
            } catch (err) {
                console.error('Fallback copy failed', err);
            }
            document.body.removeChild(textarea);
        }
    }"
    x-on:copy-to-clipboard.window="copy($event.detail.text || $event.detail)"
>
    @if (session()->has('success') && request()->has('from_checkout'))
        <div class="container-2xl px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center gap-3 rounded-2xl bg-brand-yellow/10 border border-brand-yellow/30 p-4 text-sm" role="alert">
                <x-icon name="check-circle" class="size-5 text-brand-yellow shrink-0" />
                <div>
                    <p class="font-semibold text-brand-black">Pesanan berhasil dibuat!</p>
                    <p class="text-brand-black/70">Silakan selesaikan pembayaran sebelum batas waktu berakhir.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="container-2xl w-full px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6 sm:mb-8">
            <x-ui.page-header
                :back-href="route('orders.index')"
                :back-label="__('Kembali ke Pesanan Saya')"
                eyebrow="Pesanan #{{ $order->order_number }}"
                title="Detail Pesanan"
                description="{{ $order->created_at->format('d M Y') }} • Batas bayar: {{ $order->expires_at ? $order->expires_at->format('d M Y H:i') : '-' }}"
            >
                @if ($isPaidOrLater && $order->invoice)
                    <x-slot:actions>
                        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-50 shadow-xs">
                            <x-icon name="download" class="size-4" />
                            Unduh Invoice
                        </a>
                    </x-slot:actions>
                @endif
            </x-ui.page-header>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
            <!-- KOLOM KIRI: Status Alur, Rincian Produk & Biaya, Pengiriman -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">
            <!-- Card 1: Status Alur Pesanan (Order Stepper Vertikal) -->
            <x-ui.section-card title="Status Alur Pesanan" description="Lacak perkembangan pesanan Anda dari awal hingga selesai.">
                <div class="space-y-0">
                    @foreach ($stepStatuses as $stepKey => $step)
                        @php
                            $isCompleted = $stepKey !== 'completed' && $currentStepIndex !== false && array_search($stepKey, $stepOrder) < $currentStepIndex;
                            $isCurrent = $stepKey === $currentStep;
                            $isCancelledStep = $isCancelled || $isReturned;
                            $isDoneOrCurrent = ($isCompleted || ($isCurrent && !$isCancelledStep));
                        @endphp

                        <div class="relative flex gap-4 pb-6 last:pb-0 before:absolute before:top-4 before:bottom-0 before:left-5 before:-translate-x-1/2 before:w-0.5 before:bg-zinc-200 last:before:hidden">
                            <!-- Bullet Point -->
                            <div class="relative z-10 flex size-10 shrink-0 items-center justify-center rounded-full border-2 transition-all duration-300
                                {{ $isDoneOrCurrent
                                    ? 'bg-brand-yellow border-brand-yellow text-brand-black shadow-xs'
                                    : ($isCancelledStep ? 'bg-rose-100 border-rose-300 text-rose-600' : 'bg-zinc-100 border-zinc-200 text-zinc-400') }}
                            ">
                                @if ($isCompleted)
                                    <x-icon name="check" class="size-4 stroke-[3]" />
                                @elseif ($isCurrent && !$isCancelledStep)
                                    <x-icon name="{{ $step['icon'] }}" class="size-4.5" />
                                @elseif ($isCancelledStep)
                                    <x-icon name="x" class="size-4" />
                                @else
                                    <x-icon name="{{ $step['icon'] }}" class="size-4.5" />
                                @endif
                            </div>

                            <!-- Konten Step -->
                            <div class="flex-1 min-w-0 pt-1">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-sm {{ $isDoneOrCurrent ? 'text-zinc-900' : ($isCancelledStep ? 'text-rose-600' : 'text-zinc-500') }}">
                                        {{ $step['label'] }}
                                    </p>
                                    @if ($isCurrent && !$isCancelledStep && $order->status !== 'completed')
                                        <span class="inline-flex items-center rounded-full bg-brand-yellow/20 px-2 py-0.5 text-[10px] font-bold text-brand-black">Sedang Berjalan</span>
                                    @endif
                                </div>

                                @if ($stepKey === 'shipped' && $order->shipment && $order->shipment->tracking_number)
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <x-icon name="truck" class="size-4 text-zinc-400" />
                                        <span class="font-mono text-xs font-semibold text-zinc-700 bg-zinc-100 px-2 py-0.5 rounded-md">{{ $order->shipment->tracking_number }}</span>
                                        @if ($order->shipment->courier_code)
                                            <a href="{{ $order->shipment->courier_code === 'jne' ? 'https://www.jne.co.id/id/tracking/trace?awb=' . $order->shipment->tracking_number : ($order->shipment->courier_code === 'jnt' ? 'https://www.jtexpress.co.id/tracking/' . $order->shipment->tracking_number : '#') }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="text-xs font-semibold text-brand-yellow-dark hover:underline inline-flex items-center gap-1">
                                                Lacak Kiriman
                                                <x-icon name="external-link" class="size-3" />
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                @if ($stepKey === 'ordered')
                                    <p class="mt-0.5 text-xs text-zinc-500">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                                @elseif ($stepKey === 'payment_verified' && $latestProof)
                                    <p class="mt-0.5 text-xs text-zinc-500">Diverifikasi: {{ $latestProof->verified_at?->format('d M Y, H:i') ?? '-' }} WIB</p>
                                @elseif ($stepKey === 'processing' && in_array($order->status, ['paid', 'processing', 'packed'], true))
                                    <p class="mt-0.5 text-xs text-zinc-500">Diproses sejak: {{ $order->updated_at->format('d M Y, H:i') }} WIB</p>
                                @elseif ($stepKey === 'shipped' && $order->shipment && $order->shipment->shipped_at)
                                    <p class="mt-0.5 text-xs text-zinc-500">Dikirim: {{ $order->shipment->shipped_at->format('d M Y, H:i') }} WIB</p>
                                @elseif ($stepKey === 'completed' && $order->status === 'completed')
                                    <p class="mt-0.5 text-xs text-zinc-500">Selesai: {{ $order->updated_at->format('d M Y, H:i') }} WIB</p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if ($isCancelled || $isReturned)
                        <div class="relative flex gap-4 pt-0">
                            <div class="relative z-10 flex size-10 shrink-0 items-center justify-center rounded-full border-2 bg-rose-100 border-rose-300 text-rose-600">
                                <x-icon name="x-circle" class="size-5" />
                            </div>
                            <div class="flex-1 min-w-0 pt-1">
                                <p class="font-semibold text-sm text-rose-600">{{ $isCancelled ? 'Pesanan Dibatalkan' : 'Pesanan Diretur' }}</p>
                                <p class="mt-0.5 text-xs text-zinc-500">{{ $order->updated_at->format('d M Y, H:i') }} WIB</p>
                                @if ($order->cancellation_reason)
                                    <p class="mt-1 text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg p-2.5">{{ $order->cancellation_reason }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </x-ui.section-card>

            <!-- Card 2: Rincian Produk & Ringkasan Biaya (Card Terpadu) -->
            <x-ui.section-card title="Rincian Produk & Biaya" description="{{ $order->items->count() }} barang dalam pesanan ini">
                <!-- Bagian Atas: List Item Produk -->
                <div class="divide-y divide-zinc-100">
                    @foreach ($order->items as $item)
                        @php
                            $product = $item->product;
                            $primaryMedia = $product?->media?->firstWhere('is_primary', true) ?? $product?->media?->first();
                            $imageUrl = $primaryMedia?->url() ?? 'https://placehold.co/100x100/f3f4f6/9ca3af?text=No+Image';
                        @endphp

                        <div class="py-3.5 first:pt-0 flex gap-3.5 items-center">
                            <!-- Thumbnail -->
                            <div class="flex shrink-0 size-14 rounded-xl border border-zinc-200 bg-zinc-50 overflow-hidden shadow-2xs">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $item->product_name }}"
                                     class="size-full object-cover"
                                     loading="lazy"
                                     width="56"
                                     height="56">
                            </div>

                            <!-- Info Produk -->
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-zinc-900 truncate">{{ $item->product_name }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-zinc-500">
                                    <span class="inline-flex items-center rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-[11px] font-medium text-zinc-700">SKU: {{ $item->sku }}</span>
                                    @if ($item->price_type)
                                        <span class="inline-flex items-center rounded-md bg-amber-50 border border-amber-200 px-1.5 py-0.5 text-[10px] font-semibold text-amber-800 uppercase">{{ $item->price_type }}</span>
                                    @endif
                                    <span class="text-zinc-400">•</span>
                                    <span>{{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Subtotal Baris -->
                            <div class="flex shrink-0 items-center text-right">
                                <span class="font-bold text-sm text-zinc-900 whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Garis Pemisah -->
                <div class="border-t border-zinc-100 my-4"></div>

                <!-- Bagian Bawah: Rincian Biaya -->
                <div class="space-y-2.5">
                    <div class="flex justify-between text-sm text-zinc-600">
                        <span>Subtotal Produk</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-sm text-zinc-600">
                        <span>PPh 22</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($order->tax_pph22, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-sm text-zinc-600">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-zinc-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>

                    <div class="rounded-2xl bg-brand-yellow/5 border border-brand-yellow/30 p-4 flex justify-between items-center mt-3 shadow-2xs">
                        <div>
                            <span class="text-sm font-bold text-zinc-900">Total Tagihan</span>
                            <p class="text-[11px] text-zinc-500">Termasuk PPh 22 dan ongkos kirim</p>
                        </div>
                        <span class="text-base font-extrabold text-brand-black" id="total-amount">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-ui.section-card>

            <!-- Card 3: Informasi Pengiriman & Penerima -->
            <x-ui.section-card title="Informasi Pengiriman & Penerima">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide">Penerima</p>
                        <p class="mt-1 font-semibold text-zinc-900">{{ $order->recipient_name }}</p>
                        <p class="mt-0.5 text-sm text-zinc-600">{{ $order->recipient_phone }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide">Alamat Lengkap</p>
                        <p class="mt-1 text-sm text-zinc-700 whitespace-pre-line leading-relaxed">{{ $order->shipping_address_line }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide">Ekspedisi & Layanan</p>
                        <div class="mt-1 flex items-center gap-3">
                            <span class="inline-flex items-center rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-1.5 text-sm font-semibold text-zinc-800">
                                {{ strtoupper($order->courier_code ?? '-') }} - {{ $order->courier_service ?? '-' }}
                            </span>

                            @if ($order->shipment && $order->shipment->eta_snapshot)
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                    <x-icon name="clock" class="size-3 mr-1" />
                                    Est. {{ $order->shipment->eta_snapshot }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-ui.section-card>
        </div>

        <!-- KOLOM KANAN: Panel Pembayaran & Aksi (~40%) - Sticky on Desktop -->
        <div class="lg:col-span-5 xl:col-span-4 lg:sticky lg:top-24 space-y-6">
            <x-ui.section-card class="space-y-6" variant="storefront">

                @if ($isWaitingPayment)
                    <!-- Nominal Tagihan -->
                    <div class="rounded-2xl bg-brand-yellow/5 border border-brand-yellow/30 p-4 shadow-2xs">
                        <p class="text-xs font-semibold text-brand-black/60 uppercase tracking-wide">Nominal Tagihan</p>
                        <div class="mt-2 flex items-center justify-between gap-3">
                            <span class="text-2xl font-black text-brand-black" id="total-amount-display">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="xs"
                                @click="copy('{{ $order->grand_total }}', 'Nominal tagihan berhasil disalin!')"
                                wire:click="copyToClipboard('{{ $order->grand_total }}')"
                                class="shrink-0 text-zinc-600 hover:text-brand-black hover:bg-brand-yellow/20"
                                title="Salin nominal tagihan"
                            >
                                <x-icon name="copy" class="size-3.5 mr-1" />
                                Salin
                            </flux:button>
                        </div>
                        <p class="mt-2 text-xs text-brand-black/50">Nominal sudah termasuk PPh 22 & ongkir</p>
                    </div>

                    <!-- 2. Rekening Tujuan Toko -->
                    @if ($bankAccounts->isNotEmpty())
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide">Rekening Tujuan Pembayaran</p>
                                <span class="text-xs text-zinc-400">Transfer ke salah satu</span>
                            </div>

                            <div class="space-y-2.5">
                                @foreach ($bankAccounts as $account)
                                    @php
                                        $bankCode = strtoupper($account->bank_name);
                                        $bankTheme = match (true) {
                                            str_contains($bankCode, 'BCA') => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'tag' => 'BCA'],
                                            str_contains($bankCode, 'MANDIRI') => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'tag' => 'MANDIRI'],
                                            str_contains($bankCode, 'BNI') => ['bg' => 'bg-teal-50 text-teal-800 border-teal-200', 'tag' => 'BNI'],
                                            str_contains($bankCode, 'BRI') => ['bg' => 'bg-sky-50 text-sky-800 border-sky-200', 'tag' => 'BRI'],
                                            str_contains($bankCode, 'BSI') => ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'tag' => 'BSI'],
                                            default => ['bg' => 'bg-zinc-100 text-zinc-800 border-zinc-200', 'tag' => substr($account->bank_name, 0, 4)],
                                        };
                                    @endphp

                                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-zinc-200 bg-white p-3.5 shadow-2xs transition hover:border-brand-yellow/60">
                                        <!-- Sisi Kiri: Icon/Badge Bank + No. Rekening + Atas Nama -->
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl border font-black text-xs tracking-wider {{ $bankTheme['bg'] }}">
                                                {{ $bankTheme['tag'] }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-mono text-base font-bold text-zinc-900 tracking-wide select-all">{{ $account->account_number }}</p>
                                                <p class="text-xs text-zinc-500 truncate">a.n. {{ $account->account_holder }}</p>
                                            </div>
                                        </div>

                                        <!-- Sisi Kanan: Tombol Salin Interaktif -->
                                        <flux:button
                                            type="button"
                                            variant="ghost"
                                            size="xs"
                                            @click="copy('{{ $account->account_number }}', 'Nomor rekening {{ $account->bank_name }} berhasil disalin!')"
                                            wire:click="copyToClipboard('{{ $account->account_number }}')"
                                            class="shrink-0 text-zinc-600 hover:text-brand-black hover:bg-brand-yellow/10"
                                            title="Salin nomor rekening"
                                        >
                                            <x-icon name="copy" class="size-3.5 mr-1" />
                                            Salin
                                        </flux:button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 1. Form Upload Bukti Bayar (Custom Dropzone) -->
                    <div class="space-y-4">
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide">Upload Bukti Pembayaran</p>

                        @if (session()->has('success'))
                            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 flex items-center gap-2">
                                <x-icon name="check-circle" class="size-5 shrink-0 text-emerald-600" />
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800 flex items-center gap-2">
                                <x-icon name="alert-circle" class="size-5 shrink-0 text-red-600" />
                                {{ session('error') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="uploadPaymentProof" class="space-y-4">
                            <flux:input label="Nama Bank Pengirim" wire:model.live="bank_name" placeholder="contoh: BCA, Mandiri, BNI" required />

                            <flux:input label="Nama Pemilik Rekening" wire:model.live="account_name" placeholder="Nama sesuai rekening pengirim" required />

                            <flux:input label="Jumlah Transfer (Rp)" wire:model.live="amount" type="number" required />

                            <!-- Custom File Upload Dropzone -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label>File Bukti Pembayaran <span class="text-red-500">*</span></flux:label>
                                    @if ($proof_file)
                                        <label for="proof_file_input" class="text-xs font-semibold text-brand-yellow-dark hover:underline cursor-pointer">
                                            Ganti File
                                        </label>
                                    @endif
                                </div>

                                <!-- Hidden native file input -->
                                <input
                                    id="proof_file_input"
                                    type="file"
                                    wire:model="proof_file"
                                    accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf"
                                    class="sr-only"
                                />

                                @if ($proof_file)
                                    <!-- State: File Selected Preview -->
                                    @php
                                        $isPdf = false;
                                        if ($proof_file) {
                                            $ext = strtolower($proof_file->getClientOriginalExtension());
                                            $isPdf = $ext === 'pdf';
                                        }
                                    @endphp

                                    <div class="relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-3 shadow-2xs">
                                        <div class="flex items-center gap-3">
                                            @if ($isPdf)
                                                <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-red-50 border border-red-200 text-red-600">
                                                    <x-icon name="file-text" class="size-7" />
                                                </div>
                                            @elseif ($proofPreviewUrl)
                                                <div class="size-14 shrink-0 overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50">
                                                    <img src="{{ $proofPreviewUrl }}" alt="Preview bukti pembayaran" class="size-full object-cover" />
                                                </div>
                                            @else
                                                <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-brand-yellow/10 border border-brand-yellow/30 text-brand-yellow-dark">
                                                    <x-icon name="image" class="size-7" />
                                                </div>
                                            @endif

                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-semibold text-zinc-900">{{ $proof_file->getClientOriginalName() }}</p>
                                                <div class="mt-0.5 flex items-center gap-2 text-xs text-zinc-500">
                                                    <span class="font-mono">{{ number_format($proof_file->getSize() / 1024, 0) }} KB</span>
                                                    <span>•</span>
                                                    <span class="font-medium text-emerald-600">Siap dikirim</span>
                                                </div>
                                            </div>

                                            <button
                                                type="button"
                                                wire:click="$set('proof_file', null)"
                                                class="flex size-8 shrink-0 items-center justify-center rounded-xl text-zinc-400 hover:bg-red-50 hover:text-red-600 transition"
                                                title="Hapus file terpilih"
                                                aria-label="Hapus file"
                                            >
                                                <x-icon name="trash-2" class="size-4" />
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <!-- State: Empty Dropzone -->
                                    <div x-data="{ isDropping: false }">
                                        <label
                                            for="proof_file_input"
                                            @dragover.prevent="isDropping = true"
                                            @dragleave.prevent="isDropping = false"
                                            @drop="isDropping = false"
                                            :class="{ 'border-brand-yellow bg-brand-yellow/10 ring-2 ring-brand-yellow/20': isDropping, 'border-zinc-200 bg-zinc-50/60 hover:bg-brand-yellow/5 hover:border-brand-yellow/60': !isDropping }"
                                            class="group relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-6 text-center transition cursor-pointer"
                                        >
                                            <div wire:loading.remove wire:target="proof_file" class="flex flex-col items-center">
                                                <div class="flex size-12 items-center justify-center rounded-2xl bg-white border border-zinc-200/80 shadow-2xs text-zinc-500 group-hover:text-brand-yellow-dark group-hover:border-brand-yellow/40 transition">
                                                    <x-icon name="upload" class="size-6" />
                                                </div>
                                                <p class="mt-3 text-sm font-semibold text-zinc-800 group-hover:text-zinc-900">
                                                    Klik atau seret file bukti transfer ke sini
                                                </p>
                                                <p class="mt-1 text-xs text-zinc-400">
                                                    JPG, PNG, PDF maks 5MB
                                                </p>
                                            </div>

                                            <div wire:loading.flex wire:target="proof_file" class="flex-col items-center py-2" style="display: none;">
                                                <div class="size-8 animate-spin rounded-full border-2 border-brand-yellow border-t-transparent"></div>
                                                <p class="mt-2 text-xs font-semibold text-zinc-600">Mengunggah file bukti...</p>
                                            </div>
                                        </label>
                                    </div>
                                @endif

                                @error('proof_file')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                                Kirim Bukti Pembayaran
                            </flux:button>
                        </form>

                        <!-- 5. Tombol Batalkan Pesanan -->
                        <div class="pt-3 mt-3 border-t border-zinc-100">
                            <button
                                type="button"
                                wire:click="confirmCancelOrder"
                                class="group w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-zinc-500 hover:text-red-600 bg-transparent hover:bg-red-50 border border-transparent hover:border-red-200 transition-all duration-150"
                            >
                                <x-icon name="x-circle" class="size-4 text-zinc-400 group-hover:text-red-500 transition-colors" />
                                <span>Batalkan Pesanan Ini</span>
                            </button>
                        </div>
                    </div>

                @elseif ($isPaymentSubmitted && $latestProof)
                    <!-- Status Pending Review -->
                    <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5 text-center shadow-2xs">
                        <x-icon name="clock" class="size-12 mx-auto text-amber-500" />
                        <h3 class="mt-3 font-bold text-zinc-900">Menunggu Verifikasi Admin</h3>
                        <p class="mt-1 text-sm text-zinc-600 leading-relaxed">Bukti pembayaran Anda sedang direview. Proses ini biasanya memakan waktu <strong>1x24 jam</strong>.</p>

                        @if ($latestProof)
                            <div class="mt-4 rounded-xl border border-amber-200/80 bg-white p-3.5 text-left space-y-1">
                                <p class="text-xs text-zinc-500">Bukti terunggah: <span class="font-medium text-zinc-700">{{ $latestProof->created_at->format('d M Y, H:i') }} WIB</span></p>
                                <p class="text-xs text-zinc-500">Bank: <span class="font-medium text-zinc-700">{{ $latestProof->bank_name }}</span> • a.n. <span class="font-medium text-zinc-700">{{ $latestProof->account_name }}</span></p>
                                <p class="text-xs text-zinc-500">Jumlah: <span class="font-bold text-zinc-900">Rp {{ number_format($latestProof->amount, 0, ',', '.') }}</span></p>
                            </div>
                        @endif
                    </div>

                @elseif ($isPaidOrLater)
                    <!-- Ringkasan Pembayaran Terverifikasi -->
                    <div>
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-3">Pembayaran Terverifikasi</p>

                        @if ($latestProof && $latestProof->status === 'approved')
                            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 shadow-2xs">
                                <div class="flex items-center gap-3">
                                    <x-icon name="check-circle" class="size-6 text-emerald-600 shrink-0" />
                                    <div>
                                        <p class="font-semibold text-emerald-800">Pembayaran Diterima</p>
                                        <p class="text-xs text-emerald-700">Terverifikasi pada {{ $latestProof->verified_at?->format('d M Y, H:i') ?? '-' }} WIB</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-2.5 text-sm">
                                    <div class="rounded-xl bg-white p-3 border border-emerald-100">
                                        <p class="text-xs text-zinc-500">Bank Pengirim</p>
                                        <p class="font-semibold text-zinc-900">{{ $latestProof->bank_name }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3 border border-emerald-100">
                                        <p class="text-xs text-zinc-500">Nama Pemilik Rekening</p>
                                        <p class="font-semibold text-zinc-900">{{ $latestProof->account_name }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3 border border-emerald-100">
                                        <p class="text-xs text-zinc-500">Jumlah Transfer</p>
                                        <p class="font-bold text-zinc-900">Rp {{ number_format($latestProof->amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl bg-zinc-50 border border-zinc-200 p-4 text-center">
                                <x-icon name="info" class="size-8 mx-auto text-zinc-400" />
                                <p class="mt-2 text-sm text-zinc-600">Informasi pembayaran akan muncul setelah diverifikasi admin.</p>
                            </div>
                        @endif
                    </div>

                @else
                    <!-- Status lain (cancelled, returned) -->
                    <div class="rounded-2xl bg-zinc-50 border border-zinc-200 p-5 text-center">
                        @if ($isCancelled)
                            <x-icon name="x-circle" class="size-12 mx-auto text-rose-500" />
                            <h3 class="mt-3 font-bold text-rose-600">Pesanan Dibatalkan</h3>
                            <p class="mt-1 text-sm text-zinc-600">Pesanan ini telah dibatalkan pada {{ $order->updated_at->format('d M Y, H:i') }} WIB.</p>
                            @if ($order->cancellation_reason)
                                <p class="mt-2 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-lg p-2.5">{{ $order->cancellation_reason }}</p>
                            @endif
                        @elseif ($isReturned)
                            <x-icon name="undo-2" class="size-12 mx-auto text-amber-500" />
                            <h3 class="mt-3 font-bold text-amber-700">Pesanan Diretur</h3>
                            <p class="mt-1 text-sm text-zinc-600">Pesanan ini telah diretur pada {{ $order->updated_at->format('d M Y, H:i') }} WIB.</p>
                        @endif
                    </div>
                @endif
            </x-ui.section-card>
        </div>
    </div>

    <!-- Toast Notification for Clipboard Copy -->
    <div
        x-show="copiedToast"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-2xl bg-zinc-900/95 px-4 py-3 text-sm font-semibold text-white shadow-2xl backdrop-blur-xs border border-zinc-700/50"
        style="display: none;"
    >
        <x-icon name="check-circle" class="size-4.5 text-emerald-400 shrink-0" />
        <span x-text="toastMessage">Tersalin ke clipboard!</span>
    </div>

    <!-- Modal Konfirmasi Batal Pesanan (Alpine.js) -->
    <div
        x-data="{ open: @entangle('showCancelModal') }"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs"
        style="display: none;"
        @keydown.escape.window="open = false"
        @click.self="open = false"
    >
        <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden border border-zinc-200">
            <div class="flex items-start gap-4 p-5 border-b border-zinc-100 bg-zinc-50/50">
                <div class="inline-flex size-11 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 border border-rose-100">
                    <x-icon name="alert-triangle" class="size-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <flux:heading size="lg">Batalkan Pesanan?</flux:heading>
                    <flux:subheading>Pesanan #{{ $order->order_number }} akan dibatalkan dan stok produk dikembalikan. Tindakan ini tidak dapat dibatalkan.</flux:subheading>
                </div>
            </div>

            <form wire:submit.prevent="cancelOrder" class="p-5 space-y-4">
                <div>
                    <flux:label>Alasan Pembatalan <span class="text-red-500">*</span></flux:label>
                    <flux:textarea wire:model="cancelReason" placeholder="Tuliskan alasan pembatalan (min. 5 karakter)" rows="3" required />
                    @error('cancelReason')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-2 flex justify-end gap-2">
                    <flux:button type="button" wire:click="$set('showCancelModal', false)" variant="ghost">
                        Tidak, Kembali
                    </flux:button>
                    <flux:button type="submit" variant="danger" wire:loading.attr="disabled">
                        Ya, Batalkan Pesanan
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('copy-to-clipboard', (e) => {
            const text = typeof e.detail === 'object' && e.detail.text ? e.detail.text : e.detail;
            if (!text) return;

            const notifyToast = () => {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-2xl bg-zinc-900/95 px-4 py-3 text-sm font-semibold text-white shadow-2xl backdrop-blur-xs border border-zinc-700/50 transition-opacity duration-300';
                toast.innerHTML = '<span class="flex items-center gap-2"><svg class="size-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Tersalin ke clipboard!</span>';
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 2200);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(notifyToast).catch(() => {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        document.execCommand('copy');
                        notifyToast();
                    } catch (err) {}
                    document.body.removeChild(textarea);
                });
            }
        });
    </script>
@endpush