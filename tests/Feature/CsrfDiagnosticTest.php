<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can get a page with CSRF token', function () {
    $response = $this->get('/');

    $response->assertOk();

    // Verify session has CSRF token
    $this->assertNotEmpty($this->app['session.store']->token());
});

it('can submit a form with CSRF token', function () {
    // The Laravel test helper automatically includes the CSRF token
    $response = $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    // Should NOT be 419 - should be a redirect with validation error
    $this->assertNotEquals(419, $response->getStatusCode(), 'Got 419 CSRF token mismatch');
});

it('debugs session driver in test', function () {
    $driver = config('session.driver');
    expect($driver)->toBe('array');
});
