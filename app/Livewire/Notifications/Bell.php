<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Bell extends Component
{
    protected $listeners = [
        'notifications-updated' => '$refresh',
    ];

    public function markAsRead(string $notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $url = $notification->data['url'] ?? (
                isset($notification->data['application_id'])
                    ? route('applications.show', $notification->data['application_id'])
                    : route('notifications.index')
            );

            return redirect()->to($url);
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $this->dispatch('notifications-updated');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
        $notifications = $user ? $user->notifications()->take(10)->get() : collect();

        return view('livewire.notifications.bell', [
            'unreadCount'   => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
