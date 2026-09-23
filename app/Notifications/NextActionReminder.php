<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NextActionReminder extends Notification
{
    use Queueable;

    public function __construct(
        public Application $application,
        public string $timing // 'tomorrow', 'today', 'yesterday'
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

    protected function buildMessage(): string
    {
        $companyName = $this->application->company?->name ?? 'Company';
        $jobTitle = $this->application->job_title;
        $dateFormatted = $this->application->next_action_date ? $this->application->next_action_date->format('M j, Y') : '';

        return match ($this->timing) {
            'tomorrow'  => "Reminder: {$jobTitle} at {$companyName} has an action due tomorrow ({$dateFormatted})",
            'today'     => "Action due today for {$jobTitle} at {$companyName}",
            'yesterday' => "Overdue: Action was due yesterday for {$jobTitle} at {$companyName}",
            default     => "Action due for {$jobTitle} at {$companyName}",
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        $companyName = $this->application->company?->name ?? 'Company';
        $message = $this->buildMessage();

        return (new MailMessage)
            ->subject("Next Action Reminder: {$this->application->job_title} at {$companyName}")
            ->line($message)
            ->action('View Application', route('applications.show', $this->application->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'next_action',
            'title'          => 'Next Action Reminder',
            'timing'         => $this->timing,
            'message'        => $this->buildMessage(),
            'application_id' => $this->application->id,
            'url'            => route('applications.show', $this->application->id),
        ];
    }
}
