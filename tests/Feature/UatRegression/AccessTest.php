<?php

use App\Domains\CustomerManagement\CustomerVerificationService;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\ProductIndex;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use Livewire\Livewire;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
    Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    Role::firstOrCreate(['code' => Role::ADMIN], ['name' => Role::ADMIN]);
});

function accessCustomer(string $status): User
{
    $user = User::factory()->create(['role_id' => Role::where('code', Role::CUSTOMER)->value('id')]);
    $user->customerProfile()->create(['verification_status' => $status]);

    return $user->fresh();
}

function accessAddress(User $user): Address
{
    return Address::create([
        'user_id' => $user->id,
        'recipient_name' => 'Toko',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka No. 10',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'district_name' => 'Kebayoran Baru',
        'postal_code' => '12110',
        'is_default' => true,
    ]);
}

function accessRegister($test, array $overrides = [])
{
    return $test->post(route('register.store'), array_merge([
        'name' => 'Budi',
        'business_name' => 'Toko Budi',
        'phone' => '081234567890',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides));
}

// REG-04 / FR-CART-001: only ACTIVE customers may add to cart.
it('blocks pending customer from adding to cart', function () {
    $this->actingAs(accessCustomer(CustomerProfile::PENDING));

    Livewire::test(ProductIndex::class)->call('addToCart', Product::query()->firstOrFail()->id);

    expect(CartItem::count())->toBe(0);

    // Temuan L: tombol yang pasti ditolak tidak ditampilkan; pengunjung tetap melihatnya (diarahkan ke login).
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    $this->get(route('products.index'))->assertDontSee('+ Keranjang');
    $this->get(route('products.show', $product))->assertSee('Akun sedang diverifikasi')->assertDontSee('Tambah ke Keranjang');

    auth()->logout();
    $this->get(route('products.show', $product))->assertSee('Tambah ke Keranjang');
});

// REG-04 / FR-AUTH-002: pending customer must never see prices, cart included.
it('hides prices in cart page from a customer suspended after filling the cart', function () {
    $user = accessCustomer(CustomerProfile::SUSPENDED);
    $cart = app(CartService::class)->getOrCreateCart($user);
    // Item dari saat akun masih aktif; addItem sendiri sudah menolak akun nonaktif.
    $cart->items()->create(['product_id' => Product::where('sku', 'PB-10000')->firstOrFail()->id, 'quantity' => 1]);

    $this->actingAs($user)->get(route('cart.index'))->assertOk()->assertDontSee('Rp ');
});

// REG-07 / FR-AUTH-009: suspended customer must not be able to place an order from an already-open checkout tab.
it('blocks suspended customer from placing order via open checkout component', function () {
    $user = accessCustomer(CustomerProfile::ACTIVE);
    accessAddress($user);
    $cartService = app(CartService::class);
    $cartService->addItem($cartService->getOrCreateCart($user), Product::where('sku', 'PB-10000')->firstOrFail()->id, 1);

    $this->actingAs($user);
    $component = Livewire::test(Checkout::class);
    expect($component->get('selectedCourierKey'))->not->toBeNull();

    // Admin suspends the account while the checkout tab stays open.
    $user->customerProfile->update(['verification_status' => CustomerProfile::SUSPENDED]);
    $this->actingAs($user->fresh());

    $component->call('placeOrder');

    expect(Order::count())->toBe(0);
});

// REG-02 / FR-AUTH-001: same phone in another format must be rejected as duplicate.
it('rejects duplicate phone written as +62', function () {
    accessRegister($this)->assertRedirect();
    auth()->logout();

    accessRegister($this, ['email' => 'lain@example.com', 'phone' => '+6281234567890'])
        ->assertSessionHasErrors('phone');
});

it('rejects duplicate phone written with separators', function () {
    accessRegister($this)->assertRedirect();
    auth()->logout();

    accessRegister($this, ['email' => 'lain@example.com', 'phone' => '0812-3456-7890'])
        ->assertSessionHasErrors('phone');
});

it('rejects a phone that is not a phone number', function () {
    accessRegister($this, ['phone' => 'bukan nomor'])->assertSessionHasErrors('phone');
});

// REG-02: "pesan yang jelas" — app locale is id; message should not be English framework text.
it('shows Indonesian message for duplicate email', function () {
    accessRegister($this)->assertRedirect();
    auth()->logout();

    accessRegister($this, ['phone' => '089999999999'])->assertSessionHasErrors('email');

    expect(app()->getLocale())->toBe('id')
        ->and(__('validation.unique', ['attribute' => 'email']))->not->toContain('has already been taken');
});

// REG-03 / FR-AUTH-002: guest must not be able to infer price through the price filter.
it('ignores price filter for guests', function () {
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    $price = (float) $product->prices()->where('price_type', ProductPrice::WHOLESALE)->value('amount');

    $total = fn (?string $min) => Livewire::test(ProductIndex::class)
        ->set('search', 'PB-10000')
        ->set('minPrice', $min)
        ->viewData('products')->total();

    expect($price)->toBeGreaterThan(0)
        ->and($total(null))->toBe(1)
        ->and($total((string) ($price + 1)))->toBe(1);
});

// SPEC GAP: FRD 3.1 state machine has no REJECTED -> ACTIVE or PENDING -> SUSPENDED edge.
it('refuses reactivate on a rejected profile', function () {
    $admin = User::factory()->create(['role_id' => Role::where('code', Role::ADMIN)->value('id')]);
    $profile = accessCustomer(CustomerProfile::REJECTED)->customerProfile;

    expect(fn () => app(CustomerVerificationService::class)->transition($profile, 'reactivate', $admin))
        ->toThrow(InvalidArgumentException::class);
});

it('refuses suspend on a pending profile', function () {
    $admin = User::factory()->create(['role_id' => Role::where('code', Role::ADMIN)->value('id')]);
    $profile = accessCustomer(CustomerProfile::PENDING)->customerProfile;

    expect(fn () => app(CustomerVerificationService::class)->transition($profile, 'suspend', $admin))
        ->toThrow(InvalidArgumentException::class);
});

// Sanity: things that should already hold (expected to pass).
it('denies customer access to admin and to other customers orders/invoices', function () {
    $owner = accessCustomer(CustomerProfile::ACTIVE);
    $other = accessCustomer(CustomerProfile::ACTIVE);
    $order = Order::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)->get('/admin')->assertForbidden();
    $this->actingAs($other)->get(route('orders.show', $order))->assertForbidden();
    $this->actingAs($other)->get(route('orders.invoice', $order))->assertForbidden();
    $this->actingAs($other)->get(route('orders.invoice.download', $order))->assertForbidden();
});

it('denies editing or deleting another customers address', function () {
    $owner = accessCustomer(CustomerProfile::ACTIVE);
    $address = accessAddress($owner);
    $other = accessCustomer(CustomerProfile::ACTIVE);

    $this->actingAs($other)->delete(route('account.addresses.destroy', $address))->assertForbidden();
    $this->actingAs($other)->patch(route('account.addresses.default', $address))->assertForbidden();
    expect(Address::find($address->id))->not->toBeNull();
});

it('ignores mass-assigned verification_status on register and profile update', function () {
    accessRegister($this, ['verification_status' => CustomerProfile::ACTIVE, 'role_id' => 999])->assertRedirect();
    $user = User::where('email', 'budi@example.com')->firstOrFail();
    expect($user->customerStatus())->toBe(CustomerProfile::PENDING)
        ->and($user->role->code)->toBe(Role::CUSTOMER);

    $this->actingAs($user)->patch(route('account.update'), [
        'name' => 'Budi', 'phone' => '081234567890', 'verification_status' => CustomerProfile::ACTIVE, 'email' => 'x@y.z',
    ]);
    expect($user->fresh()->customerStatus())->toBe(CustomerProfile::PENDING)
        ->and($user->fresh()->email)->toBe('budi@example.com');
});

// Temuan K: pesan konfirmasi kata sandi tampil di field konfirmasi, bukan di field kata sandi.
it('reports password confirmation mismatch on the confirmation field', function () {
    accessRegister($this, ['password_confirmation' => 'beda12345'])
        ->assertSessionHasErrors(['password_confirmation' => 'Konfirmasi kata sandi tidak cocok.'])
        ->assertSessionDoesntHaveErrors('password');
});
