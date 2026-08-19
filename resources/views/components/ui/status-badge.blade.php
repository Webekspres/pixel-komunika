@props([
    'status',
])

@php
    $label = match ($status) {
        \App\Models\CustomerProfile::ACTIVE => 'Aktif',
        \App\Models\CustomerProfile::REJECTED => 'Ditolak',
        \App\Models\CustomerProfile::SUSPENDED => 'Ditangguhkan',
        \App\Models\CustomerProfile::PENDING => 'Menunggu Verifikasi',
        'unpaid' => 'Menunggu Pembayaran',
        'payment_pending' => 'Pembayaran Diajukan',
        'paid' => 'Dibayar',
        'processing' => 'Diproses',
        'packed' => 'Dikemas',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'pending' => 'Menunggu Verifikasi',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'payment_rejected' => 'Pembayaran Ditolak',
        default => is_string($status) ? str_replace('_', ' ', $status) : (string) $status,
    };

    $classes = match ($status) {
        \App\Models\CustomerProfile::ACTIVE => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        \App\Models\CustomerProfile::REJECTED => 'bg-red-50 text-red-600 border-red-200',
        \App\Models\CustomerProfile::SUSPENDED => 'bg-zinc-100 text-zinc-600 border-zinc-200',
        \App\Models\CustomerProfile::PENDING => 'bg-orange-50 text-orange-700 border-orange-200',
        'unpaid' => 'bg-zinc-50 text-zinc-600 border-zinc-200',
        'payment_pending' => 'bg-amber-50 text-amber-800 border-amber-200',
        'paid', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'packed' => 'bg-sky-50 text-sky-700 border-sky-200',
        'shipped' => 'bg-violet-50 text-violet-700 border-violet-200',
        'cancelled' => 'bg-rose-50 text-rose-600 border-rose-200',
        'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected', 'payment_rejected' => 'bg-rose-50 text-rose-600 border-rose-200',
        default => 'bg-zinc-100 text-zinc-700 border-zinc-200',
    };
@endphp

<span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $classes }}">{{ $label }}</span>
