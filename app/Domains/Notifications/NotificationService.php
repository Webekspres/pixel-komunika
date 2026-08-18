<?php

namespace App\Domains\Notifications;

use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;
use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        protected WhatsAppNotifierInterface $whatsApp,
    ) {}

    public function notifyNewOrder(Order $order): void
    {
        $admins = User::query()
            ->where('role_id', Role::query()->where('code', Role::ADMIN)->value('id'))
            ->get();

        foreach ($admins as $admin) {
            AppNotification::query()->create([
                'user_id' => $admin->id,
                'order_id' => $order->id,
                'channel' => AppNotification::CHANNEL_DATABASE,
                'type' => AppNotification::TYPE_NEW_ORDER,
                'data' => [
                    'order_number' => $order->order_number,
                    'message' => 'Order baru masuk',
                    'customer' => $order->recipient_name,
                    'grand_total' => (float) $order->grand_total,
                ],
                'status' => AppNotification::SENT,
                'sent_at' => now(),
            ]);
        }

        AppNotification::query()->create([
            'user_id' => $admins->first()?->id ?? $order->user_id,
            'order_id' => $order->id,
            'channel' => AppNotification::CHANNEL_WHATSAPP,
            'type' => AppNotification::TYPE_NEW_ORDER,
            'data' => [
                'phone' => config('store.whatsapp.admin_order_phone'),
                'message' => config('store.whatsapp.new_order_message'),
                'order_number' => $order->order_number,
            ],
            'status' => AppNotification::PENDING,
        ]);
    }

    public function markOrderNotificationsRead(User $admin): int
    {
        return AppNotification::query()
            ->where('user_id', $admin->id)
            ->where('channel', AppNotification::CHANNEL_DATABASE)
            ->where('type', AppNotification::TYPE_NEW_ORDER)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Jumlah notifikasi website (DATABASE) yang belum dibaca admin.
     */
    public function unreadCount(User $user): int
    {
        return AppNotification::query()
            ->where('user_id', $user->id)
            ->where('channel', AppNotification::CHANNEL_DATABASE)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Notifikasi website terbaru untuk panel admin (FR-NTF-001).
     */
    public function recentFor(User $user, int $limit = 20): Collection
    {
        return AppNotification::query()
            ->with('order')
            ->where('user_id', $user->id)
            ->where('channel', AppNotification::CHANNEL_DATABASE)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function markRead(AppNotification $notification, User $user): bool
    {
        if ($notification->user_id !== $user->id) {
            return false;
        }

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return true;
    }

    public function markAllRead(User $user): int
    {
        return AppNotification::query()
            ->where('user_id', $user->id)
            ->where('channel', AppNotification::CHANNEL_DATABASE)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function dispatchPendingWhatsApp(int $limit = 50): int
    {
        $pending = AppNotification::query()
            ->where('channel', AppNotification::CHANNEL_WHATSAPP)
            ->where('status', AppNotification::PENDING)
            ->limit($limit)
            ->get();

        $sent = 0;
        foreach ($pending as $notification) {
            $phone = $notification->data['phone'] ?? config('store.whatsapp.admin_order_phone');
            $message = $notification->data['message'] ?? config('store.whatsapp.new_order_message');

            try {
                $externalId = $this->whatsApp->send($phone, $message);

                $notification->update([
                    'status' => AppNotification::SENT,
                    'external_message_id' => $externalId,
                    'sent_at' => now(),
                ]);
                $sent++;
            } catch (\Throwable $e) {
                // Kegagalan dicatat (FR-NTF-002); status tetap PENDING agar scheduler
                // mencoba lagi tanpa menggandakan pesan. Retry/fallback provider
                // menyusul keputusan OPN-023.
                Log::warning('whatsapp.send.failed', [
                    'notification_id' => $notification->id,
                    'phone' => $phone,
                    'error' => $e->getMessage(),
                ]);

                $notification->update([
                    'data' => array_merge($notification->data ?? [], ['last_error' => $e->getMessage()]),
                ]);
            }
        }

        return $sent;
    }
}
