<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewReminder extends Notification
{
    use Queueable;

    public function __construct(
        public Interview $interview,
        public string $window // '24h', '1h'
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
        $application = $this->interview->application;
        $companyName = $application?->company?->name ?? 'Company';
        $jobTitle = $application?->job_title ?? 'Job';
        $roundType = ucfirst($this->interview->round_type ?? 'Interview');
        $time = $this->interview->scheduled_at ? $this->interview->scheduled_at->format('g:i A') : '';

        return match ($this->window) {
            '24h'   => "Interview tomorrow: {$roundType} for {$jobTitle} at {$companyName} at {$time}",
            '1h'    => "Interview in 1 hour: {$roundType} for {$jobTitle} at {$companyName}",
            default => "Upcoming Interview: {$roundType} for {$jobTitle} at {$companyName}",
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->interview->application;
        $companyName = $application?->company?->name ?? 'Company';
        $message = $this->buildMessage();

        return (new MailMessage)
            ->subject("Interview Reminder: {$roundType} at {$companyName}")
            ->line($message)
            ->action('View Application', route('applications.show', $this->interview->application_id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'interview',
            'title'          => 'Interview Reminder',
            'window'         => $this->window,
            'message'        => $this->buildMessage(),
            'interview_id'   => $this->interview->id,
            'application_id' => $this->interview->application_id,
            'url'            => route('applications.show', $this->interview->application_id),
        ];
    }
}
