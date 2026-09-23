<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUpReminder extends Notification
{
    use Queueable;

    public function __construct(
        public Application $application,
        public int $daysCount
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (method_exists($notifiable, 'getNotificationPreferences')) {
            $prefs = $notifiable->getNotificationPreferences();
            if (!empty($prefs['email'])) {
                $channels[] = 'mail';
            }
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $companyName = $this->application->company?->name ?? 'Company';
        $message = "No response in {$this->daysCount} days for {$this->application->job_title} at {$companyName}. Time to follow up?";

        return (new MailMessage)
            ->subject("Follow-up Reminder: {$this->application->job_title} at {$companyName}")
            ->line($message)
            ->action('View Application', route('applications.show', $this->application->id));
    }

    public function toArray(object $notifiable): array
    {
        $companyName = $this->application->company?->name ?? 'Company';
        $message = "No response in {$this->daysCount} days for {$this->application->job_title} at {$companyName}. Time to follow up?";

        return [
            'type'           => 'follow_up',
            'title'          => 'Follow-up Reminder',
            'message'        => $message,
            'application_id' => $this->application->id,
            'url'            => route('applications.show', $this->application->id),
        ];
    }
}
