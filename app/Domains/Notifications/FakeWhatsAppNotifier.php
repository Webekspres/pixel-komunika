<?php

namespace App\Domains\Notifications;

use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;
use Illuminate\Support\Str;

class FakeWhatsAppNotifier implements WhatsAppNotifierInterface
{
    /** @var list<array{phone: string, message: string, id: string}> */
    public array $sent = [];

    public function send(string $phone, string $message): string
    {
        $id = 'wa-fake-'.Str::uuid();
        $this->sent[] = compact('phone', 'message') + ['id' => $id];

        return $id;
    }
}
