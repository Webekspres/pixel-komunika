<?php

namespace App\Domains\Notifications;

use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogWhatsAppNotifier implements WhatsAppNotifierInterface
{
    public function send(string $phone, string $message): string
    {
        $id = 'wa-log-'.Str::uuid();

        Log::info('whatsapp.stub', [
            'id' => $id,
            'phone' => $phone,
            'message' => $message,
        ]);

        return $id;
    }
}
