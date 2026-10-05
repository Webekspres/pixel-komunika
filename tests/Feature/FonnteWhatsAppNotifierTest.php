<?php

use App\Domains\Notifications\FonnteWhatsAppNotifier;
use Illuminate\Support\Facades\Http;

beforeEach(fn () => config(['store.whatsapp.fonnte_token' => 'test-token']));

it('sends via fonnte and returns message id', function () {
    Http::fake(['api.fonnte.com/*' => Http::response(['status' => true, 'id' => ['80367170']])]);

    expect((new FonnteWhatsAppNotifier)->send('081546407702', 'Cek Order masuk'))->toBe('fonnte-80367170');

    Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'test-token')
        && $request['target'] === '081546407702'
        && $request['message'] === 'Cek Order masuk');
});

it('throws when fonnte reports failure with http 200', function () {
    Http::fake(['api.fonnte.com/*' => Http::response(['status' => false, 'reason' => 'device disconnected'])]);

    (new FonnteWhatsAppNotifier)->send('081546407702', 'Cek Order masuk');
})->throws(RuntimeException::class, 'device disconnected');

it('throws when token is missing', function () {
    config(['store.whatsapp.fonnte_token' => null]);
    Http::fake();

    (new FonnteWhatsAppNotifier)->send('081546407702', 'Cek Order masuk');
})->throws(RuntimeException::class, 'FONNTE_TOKEN');
