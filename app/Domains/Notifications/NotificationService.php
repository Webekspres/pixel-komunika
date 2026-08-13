<?php

namespace App\Domains\Notifications;

use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;

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
            $externalId = $this->whatsApp->send($phone, $message);

            $notification->update([
                'status' => AppNotification::SENT,
                'external_message_id' => $externalId,
                'sent_at' => now(),
            ]);
            $sent++;
        }

        return $sent;
    }
}
