<?php

namespace App\Domains\Notifications;

use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

// ponytail: Fonnte = WA unofficial (risiko banned, OPN-023 sementara); ganti ke WA Business API bila klien siap.
class FonnteWhatsAppNotifier implements WhatsAppNotifierInterface
{
    public function send(string $phone, string $message): string
    {
        $token = config('store.whatsapp.fonnte_token');

        if (blank($token)) {
            throw new RuntimeException('FONNTE_TOKEN belum dikonfigurasi.');
        }

        // Fonnte membalas HTTP 200 juga saat gagal; status ada di body.
        $json = Http::withHeaders(['Authorization' => $token])
            ->asForm()
            ->timeout(15)
            ->post('https://api.fonnte.com/send', ['target' => $phone, 'message' => $message])
            ->throw()
            ->json();

        if (($json['status'] ?? false) !== true) {
            throw new RuntimeException('Fonnte: '.($json['reason'] ?? 'respons tidak dikenal'));
        }

        return 'fonnte-'.implode(',', (array) ($json['id'] ?? []));
    }
}
