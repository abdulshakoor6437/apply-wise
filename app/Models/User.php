<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'notification_preferences'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'password'                 => 'hashed',
            'notification_preferences' => 'array',
        ];
    }

    public function getNotificationPreferences(): array
    {
        $defaults = [
            'email'      => false,
            'follow_ups' => true,
            'interviews' => true,
            'actions'    => true,
        ];

        return array_merge($defaults, $this->notification_preferences ?? []);
    }

    public function wantsNotification(string $type): bool
    {
        $prefs = $this->getNotificationPreferences();
        return (bool) ($prefs[$type] ?? true);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
