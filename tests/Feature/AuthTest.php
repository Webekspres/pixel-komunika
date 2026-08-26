<?php

use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('registers a customer as pending verification', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Toko Baru',
        'business_name' => 'Toko Sumber Jaya',
        'phone' => '081234567890',
        'email' => 'baru@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('account.dashboard'));

    $user = User::query()->where('email', 'baru@example.com')->firstOrFail();

    expect($user->role->code)->toBe(Role::CUSTOMER)
        ->and($user->customerProfile->verification_status)->toBe(CustomerProfile::PENDING)
        ->and($user->customerProfile->business_name)->toBe('Toko Sumber Jaya');

    $this->assertAuthenticatedAs($user);
});

it('logs in with valid credentials and logs out safely', function () {
    $user = User::factory()->create([
        'password' => Hash::make('secret123'),
    ]);

    $user->customerProfile()->create([
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'secret123',
    ])->assertRedirect(route('account.dashboard'));

    $this->assertAuthenticatedAs($user->fresh());
    expect($user->fresh()->last_login_at)->not->toBeNull();

    $this->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});

it('rate limits repeated failed logins', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), [
            'email' => 'nobody@example.com',
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('login.store'), [
        'email' => 'nobody@example.com',
        'password' => 'wrong-password',
    ])->assertStatus(429);
});

it('rejects invalid login with a generic message', function () {
    $user = User::factory()->create([
        'password' => Hash::make('secret123'),
    ]);

    $user->customerProfile()->create([
        'verification_status' => CustomerProfile::PENDING,
    ]);

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});
