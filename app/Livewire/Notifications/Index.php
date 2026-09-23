<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

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
        $notifications = $user
            ? $user->notifications()->paginate(15)
            : collect();

        return view('livewire.notifications.index', [
            'notifications' => $notifications,
        ])->layout('layouts.app', ['title' => 'Notifications']);
    }
}
