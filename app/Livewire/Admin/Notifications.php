<?php

namespace App\Livewire\Admin;

use App\Domains\Notifications\NotificationService;
use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Notifications extends Component
{
    #[On('notifications-updated')]
    public function refresh(): void
    {
        // Re-render memuat ulang jumlah belum dibaca (mis. setelah admin membuka daftar pesanan).
    }

    public function markRead(int $notificationId, NotificationService $notifications): void
    {
        $notifications->markRead(AppNotification::query()->findOrFail($notificationId), Auth::user());
    }

    public function markAllRead(NotificationService $notifications): void
    {
        $notifications->markAllRead(Auth::user());
    }

    public function openNotification(int $notificationId, NotificationService $notifications): void
    {
        $notification = AppNotification::query()->findOrFail($notificationId);
        $notifications->markRead($notification, Auth::user());

        $this->redirect(route('admin.orders.show', $notification->order_id), navigate: true);
    }

    public function render(NotificationService $notifications)
    {
        $user = Auth::user();

        if (! $user || ! $user->isAdmin()) {
            return view('livewire.admin.notifications', [
                'notifications' => collect(),
                'unreadCount' => 0,
            ]);
        }

        return view('livewire.admin.notifications', [
            'notifications' => $notifications->recentFor($user, 20),
            'unreadCount' => $notifications->unreadCount($user),
        ]);
    }
}
