<?php

namespace App\Livewire\Storefront;

use App\Models\Address;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Shipping\ShippingCalculatorInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout - Pixel Komunika')]
class Checkout extends Component
{
    public ?int $selectedAddressId = null;

    public ?string $selectedCourierKey = null;

    public function mount(CartService $cartService, ShippingCalculatorInterface $shippingService)
    {
        $user = Auth::user();
        $defaultAddress = $user->addresses()->where('is_default', true)->first() ?? $user->addresses()->first();
        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
        }
    }

    public function placeOrder(
        CartService $cartService,
        OrderService $orderService,
        ShippingCalculatorInterface $shippingService
    ) {
        $user = Auth::user();
        $this->validate([
            'selectedAddressId' => ['required', 'exists:addresses,id'],
            'selectedCourierKey' => ['required', 'string'],
        ], [
            'selectedAddressId.required' => 'Pilih alamat pengiriman terlebih dahulu.',
            'selectedCourierKey.required' => 'Pilih opsi layanan ekspedisi.',
        ]);

        $address = Address::where('user_id', $user->id)->findOrFail($this->selectedAddressId);
        $cart = $cartService->getOrCreateCart($user, session()->getId());
        $summary = $cartService->getCartSummary($cart);

        $availableRates = $shippingService->calculateRates($address->city_name, $summary['total_weight_grams'], $address->district_name);
        $selectedRate = collect($availableRates)->first(function ($rate) {
            return ($rate['code'].':'.$rate['service']) === $this->selectedCourierKey;
        });

        if (! $selectedRate) {
            session()->flash('error', 'Pilihan ekspedisi tidak valid.');

            return;
        }

        try {
            $order = $orderService->createOrderFromCart($user, $cart, $address, $selectedRate);
            session()->flash('success', "Pesanan {$order->order_number} berhasil dibuat!");

            return redirect()->route('orders.show', $order);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat pesanan: '.$e->getMessage());
        }
    }

    public function render(CartService $cartService, ShippingCalculatorInterface $shippingService)
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        $cart = $cartService->getOrCreateCart($user, session()->getId());
        $summary = $cartService->getCartSummary($cart);

        $shippingRates = [];
        $storeRates = [];
        $biteshipRates = [];
        $selectedAddress = $addresses->firstWhere('id', $this->selectedAddressId);

        if ($selectedAddress) {
            $shippingRates = $shippingService->calculateRates(
                $selectedAddress->city_name,
                $summary['total_weight_grams'],
                $selectedAddress->district_name
            );

            $storeRates = collect($shippingRates)->where('provider', \App\Models\Shipment::PROVIDER_STORE)->values()->all();
            $biteshipRates = collect($shippingRates)->where('provider', \App\Models\Shipment::PROVIDER_BITESHIP)->values()->all();

            $validKeys = collect($shippingRates)->map(fn ($r) => $r['code'].':'.$r['service'])->all();
            if (empty($this->selectedCourierKey) || ! in_array($this->selectedCourierKey, $validKeys, true)) {
                if (! empty($shippingRates)) {
                    $first = $shippingRates[0];
                    $this->selectedCourierKey = $first['code'].':'.$first['service'];
                } else {
                    $this->selectedCourierKey = null;
                }
            }
        }

        $selectedRate = collect($shippingRates)->first(function ($rate) {
            return ($rate['code'].':'.$rate['service']) === $this->selectedCourierKey;
        });

        $shippingCost = $selectedRate ? (float) $selectedRate['cost'] : 0;
        $grandTotal = (float) $summary['subtotal'] + (float) $summary['pph22'] + $shippingCost;

        return view('livewire.storefront.checkout', [
            'addresses' => $addresses,
            'summary' => $summary,
            'shippingRates' => $shippingRates,
            'storeRates' => $storeRates,
            'biteshipRates' => $biteshipRates,
            'selectedAddress' => $selectedAddress,
            'shippingCost' => $shippingCost,
            'grandTotal' => $grandTotal,
        ])->layout('layouts.storefront');
    }
}
