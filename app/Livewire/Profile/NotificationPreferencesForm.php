<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationPreferencesForm extends Component
{
    public bool $email = false;
    public bool $follow_ups = true;
    public bool $interviews = true;
    public bool $actions = true;

    public function mount(): void
    {
        $user = Auth::user();
        $prefs = $user ? $user->getNotificationPreferences() : [];

        $this->email      = (bool) ($prefs['email'] ?? false);
        $this->follow_ups = (bool) ($prefs['follow_ups'] ?? true);
        $this->interviews = (bool) ($prefs['interviews'] ?? true);
        $this->actions    = (bool) ($prefs['actions'] ?? true);
    }

    public function updateNotificationPreferences(): void
    {
        $user = Auth::user();

        $preferences = [
            'email'      => $this->email,
            'follow_ups' => $this->follow_ups,
            'interviews' => $this->interviews,
            'actions'    => $this->actions,
        ];

        $user->notification_preferences = $preferences;
        $user->save();

        $this->dispatch('notification-preferences-updated');
    }

    public function render()
    {
        return view('livewire.profile.notification-preferences-form');
    }
}
