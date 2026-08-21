<?php

namespace Database\Seeders;

use App\Domains\Order\FulfillmentService;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\StoreCourierRate;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function __construct(
        private readonly SampleCatalogImporter $sampleCatalogImporter,
    ) {}

    public function run(): void
    {
        $adminRoleId = Role::query()->where('code', Role::ADMIN)->value('id');
        $customerRoleId = Role::query()->where('code', Role::CUSTOMER)->value('id');

        $store = StoreProfile::query()->updateOrCreate(
            ['store_name' => 'Pixel Komunika'],
            [
                'address' => config('store.address'),
                'contact_number' => config('store.phone'),
                'company_name' => config('store.company_name'),
                'company_npwp' => config('store.npwp'),
                'partai_minimum_quantity' => 5,
                'origin_biteship_area_id' => config('biteship.origin_area_id') ?: 'IDNP9IDNC22IDND2043IDZ40132', // Coblong 40132 (Sawahkurung) fallback
                'origin_biteship_label' => 'Coblong, Bandung, Jawa Barat (40132)',
                'origin_postal_code' => '40132',
                'is_active' => true,
            ],
        );

        // ensure only one active store in seed
        StoreProfile::query()->where('id', '!=', $store->id)->update(['is_active' => false]);

        BankAccount::query()->updateOrCreate(
            ['account_number' => '1234567890'],
            [
                'bank_name' => 'BCA',
                'account_holder' => 'Pixel Komunika',
                'instructions' => 'Transfer ke rekening BCA a/n Pixel Komunika',
                'is_active' => true,
            ],
        );

        foreach ([
            ['area_code' => 'BDG-COBLONG', 'area_name' => 'Coblong', 'rate_amount' => 10000, 'eta_text' => 'H+1 hari kerja'],
            ['area_code' => 'BDG-CICENDO', 'area_name' => 'Cicendo', 'rate_amount' => 12000, 'eta_text' => 'H+1 hari kerja'],
            ['area_code' => 'BDG-KAB-LEMBANG', 'area_name' => 'Lembang', 'rate_amount' => 15000, 'eta_text' => 'H+1/H+2 hari kerja'],
        ] as $rate) {
            StoreCourierRate::query()->updateOrCreate(
                ['area_code' => $rate['area_code']],
                [...$rate, 'is_active' => true],
            );
        }

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@pixelkomunika.test'],
            [
                'role_id' => $adminRoleId,
                'name' => 'Admin Pixel Komunika',
                'phone' => '081111111111',
                'password' => 'password',
            ],
        );

        $customers = [
            ['email' => 'test@example.com', 'name' => 'Test User', 'phone' => '082222222222', 'status' => CustomerProfile::ACTIVE, 'business' => 'Toko Test'],
            ['email' => 'pending@example.com', 'name' => 'Pending User', 'phone' => '083333333333', 'status' => CustomerProfile::PENDING, 'business' => 'Toko Pending'],
            ['email' => 'rejected@example.com', 'name' => 'Rejected User', 'phone' => '084444444444', 'status' => CustomerProfile::REJECTED, 'business' => 'Toko Reject'],
            ['email' => 'suspended@example.com', 'name' => 'Suspended User', 'phone' => '085555555555', 'status' => CustomerProfile::SUSPENDED, 'business' => 'Toko Suspend'],
        ];

        $activeCustomer = null;
        foreach ($customers as $row) {
            $user = User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'role_id' => $customerRoleId,
                    'name' => $row['name'],
                    'phone' => $row['phone'],
                    'password' => 'password',
                ],
            );

            $profile = $user->customerProfile()->updateOrCreate(
                [],
                [
                    'business_name' => $row['business'],
                    'verification_status' => $row['status'],
                    'reviewed_by' => $row['status'] === CustomerProfile::PENDING ? null : $admin->id,
                    'reviewed_at' => $row['status'] === CustomerProfile::PENDING ? null : now(),
                    'reseller_account_number' => $row['status'] === CustomerProfile::ACTIVE
                        ? sprintf('%s-%06d', config('store.reseller_account_prefix', 'PKR'), $user->id)
                        : null,
                ],
            );

            if ($row['status'] === CustomerProfile::ACTIVE) {
                $activeCustomer = $user;
                Address::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'address_line' => 'Jl. Merdeka No. 1',
                    ],
                    [
                        'recipient_name' => $user->name,
                        'recipient_phone' => $user->phone,
                        'province_name' => 'Jawa Barat',
                        'city_name' => 'Bandung',
                        'district_name' => 'Coblong',
                        'postal_code' => '40135',
                        'is_default' => true,
                    ],
                );
            }
        }

        $this->sampleCatalogImporter->import();

        if ($activeCustomer) {
            $this->seedSampleOrders($activeCustomer, $admin);
        }
    }

    protected function seedSampleOrders(User $customer, User $admin): void
    {
        $address = $customer->addresses()->first();
        $product = Product::query()->where('is_active', true)->first();
        if (! $address || ! $product) {
            return;
        }

        // Avoid duplicating demo orders on re-seed
        if (Order::query()->where('user_id', $customer->id)->exists()) {
            return;
        }

        $cartService = app(CartService::class);
        $orderService = app(OrderService::class);
        $paymentService = app(PaymentService::class);

        $makeOrder = function (int $qty) use ($cartService, $orderService, $customer, $address, $product) {
            $cart = $cartService->getOrCreateCart($customer);
            $cartService->clearCart($cart);
            $cartService->addItem($cart, $product->id, $qty);

            return $orderService->createOrderFromCart($customer, $cart, $address, [
                'provider' => 'STORE_COURIER',
                'code' => 'store',
                'service' => 'Kurir Toko',
                'name' => 'Kurir Toko — Coblong',
                'cost' => 10000,
                'etd' => 'H+1 hari kerja',
            ]);
        };

        $unpaid = $makeOrder(1);

        $pending = $makeOrder(1);
        $file = UploadedFile::fake()->image('proof.jpg');
        $paymentService->uploadPaymentProof($pending, $customer, [
            'bank_name' => 'BCA',
            'account_name' => $customer->name,
            'amount' => $pending->grand_total,
        ], $file);

        $paid = $makeOrder(2);
        $file2 = UploadedFile::fake()->image('proof2.jpg');
        $proof = $paymentService->uploadPaymentProof($paid, $customer, [
            'bank_name' => 'BCA',
            'account_name' => $customer->name,
            'amount' => $paid->grand_total,
        ], $file2);
        $paymentService->approvePayment($proof, $admin);
        app(FulfillmentService::class)->transition($paid->fresh(), 'processing', $admin);

        $shipped = $makeOrder(1);
        $file3 = UploadedFile::fake()->image('proof3.jpg');
        $proof3 = $paymentService->uploadPaymentProof($shipped, $customer, [
            'bank_name' => 'BCA',
            'account_name' => $customer->name,
            'amount' => $shipped->grand_total,
        ], $file3);
        $paymentService->approvePayment($proof3, $admin);
        $fulfillment = app(FulfillmentService::class);
        $fulfillment->transition($shipped->fresh(), 'processing', $admin);
        $fulfillment->transition($shipped->fresh(), 'packed', $admin);
        $fulfillment->transition($shipped->fresh(), 'shipped', $admin, 'RESI-SEED-001');

        $cancelled = $makeOrder(1);
        $orderService->cancelOrder($cancelled, 'Contoh order dibatalkan', 'ADMIN', $admin);

        unset($unpaid);
    }
}
