<?php

namespace App\Console\Commands;

use App\Domains\Notifications\NotificationService;
use Illuminate\Console\Command;

class DispatchPendingNotifications extends Command
{
    protected $signature = 'notifications:dispatch-pending';

    protected $description = 'Send pending WhatsApp notifications via configured stub driver';

    public function handle(NotificationService $notifications): int
    {
        $count = $notifications->dispatchPendingWhatsApp();
        $this->info("Sent {$count} WhatsApp notification(s).");

        return self::SUCCESS;
    }
}
