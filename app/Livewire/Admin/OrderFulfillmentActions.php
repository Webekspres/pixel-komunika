<?php

namespace App\Livewire\Admin;

use App\Domains\Order\FulfillmentService;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use InvalidArgumentException;
use Livewire\Component;

class OrderFulfillmentActions extends Component
{
    public Order $order;

    public bool $showShipModal = false;

    public string $trackingNumber = '';

    public bool $showRejectModal = false;

    public string $rejectionReason = '';

    public bool $showCancelModal = false;

    public string $cancelReason = '';

    public bool $showTerkendalaModal = false;

    public string $terkendalaReason = '';

    public function mount(Order $order): void
    {
        $this->order = $order->load([
            'user',
            'items',
            'invoice',
            'latestPaymentProof',
            'paymentProofs',
            'shipment',
        ]);

        $this->trackingNumber = $order->shipment?->tracking_number ?? '';
    }

    // FR-ORD-006: pembatalan manual oleh admin hanya pada hari yang sama dengan
    // tanggal transaksi, untuk order yang belum dibayar atau menunggu verifikasi.
    public function canCancelToday(): bool
    {
        return in_array($this->order->status, ['unpaid', 'payment_pending'], true)
            && $this->order->order_date_local?->format('Y-m-d') === now('Asia/Jakarta')->toDateString();
    }

    public function approvePayment(PaymentService $paymentService)
    {
        $proof = $this->order->latestPaymentProof;

        if (! $proof || $proof->status !== 'pending') {
            return;
        }

        $paymentService->approvePayment($proof, auth()->user());

        $this->order->refresh();
        session()->flash('success', "Pembayaran untuk Order #{$this->order->order_number} telah disetujui.");
    }

    public function openRejectModal(): void
    {
        if ($this->order->latestPaymentProof?->status !== 'pending') {
            return;
        }

        $this->showRejectModal = true;
    }

    public function resetRejectModal(): void
    {
        $this->reset(['rejectionReason', 'showRejectModal']);
    }

    public function confirmRejectPayment(PaymentService $paymentService)
    {
        $proof = $this->order->latestPaymentProof;

        if (! $proof || $proof->status !== 'pending') {
            return;
        }

        $this->validate([
            'rejectionReason' => 'required|string|min:5',
        ]);

        $paymentService->rejectPayment($proof, $this->rejectionReason, auth()->user());

        $this->resetRejectModal();
        $this->order->refresh();
        session()->flash('success', "Pembayaran untuk Order #{$this->order->order_number} telah ditolak.");
    }

    public function transitionStatus(string $status, FulfillmentService $fulfillment)
    {
        try {
            $fulfillment->transition($this->order, $status, auth()->user());
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        $this->order->refresh();
        session()->flash('success', "Status pesanan {$this->order->order_number} diperbarui menjadi {$status}.");
    }

    public function openShipModal(): void
    {
        $this->trackingNumber = $this->order->shipment?->tracking_number ?? '';
        $this->showShipModal = true;
    }

    public function resetShipModal(): void
    {
        $this->reset(['showShipModal']);
    }

    public function confirmShip(FulfillmentService $fulfillment)
    {
        $this->validate([
            'trackingNumber' => 'nullable|string|max:100',
        ]);

        try {
            $fulfillment->transition($this->order, 'shipped', auth()->user(), $this->trackingNumber ?: null);
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        $this->resetShipModal();
        $this->order->refresh();
        session()->flash('success', "Order #{$this->order->order_number} ditandai Dikirim.");
    }

    public function openCancelModal(): void
    {
        $this->showCancelModal = true;
    }

    public function resetCancelModal(): void
    {
        $this->reset(['cancelReason', 'showCancelModal']);
    }

    public function confirmCancelOrder(OrderService $orderService)
    {
        if (! $this->canCancelToday()) {
            $this->resetCancelModal();
            session()->flash('error', 'Pembatalan manual hanya bisa dilakukan pada hari yang sama dengan tanggal transaksi.');

            return;
        }

        $this->validate([
            'cancelReason' => 'required|string|min:5',
        ]);

        $orderService->cancelOrder($this->order, $this->cancelReason, 'ADMIN', auth()->user());

        $this->resetCancelModal();
        $this->order->refresh();
        session()->flash('success', "Order #{$this->order->order_number} berhasil dibatalkan dan stok telah direstore.");
    }

    public function openTerkendalaModal(): void
    {
        $this->showTerkendalaModal = true;
    }

    public function resetTerkendalaModal(): void
    {
        $this->reset(['terkendalaReason', 'showTerkendalaModal']);
    }

    public function confirmTerkendala(FulfillmentService $fulfillment)
    {
        if ($this->order->status !== 'shipped') {
            return;
        }

        $this->validate([
            'terkendalaReason' => 'required|string|min:5',
        ]);

        try {
            $fulfillment->markTerkendala($this->order, $this->terkendalaReason, auth()->user());
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        $this->resetTerkendalaModal();
        $this->order->refresh();
        session()->flash('success', "Order #{$this->order->order_number} ditandai terkendala pengiriman.");
    }

    public function resolveTerkendala(FulfillmentService $fulfillment)
    {
        $fulfillment->resolveTerkendala($this->order, auth()->user());

        $this->order->refresh();
        session()->flash('success', "Kendala pengiriman Order #{$this->order->order_number} telah diselesaikan.");
    }

    public function render()
    {
        return view('livewire.admin.order-fulfillment-actions');
    }
}
