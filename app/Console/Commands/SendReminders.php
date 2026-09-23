<?php

namespace App\Console\Commands;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Interview;
use App\Notifications\FollowUpReminder;
use App\Notifications\InterviewReminder;
use App\Notifications\NextActionReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process follow-up, next action, and interview reminders for applications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting reminder checks...');

        $this->checkFollowUpReminders();
        $this->checkNextActionReminders();
        $this->checkInterviewReminders();

        $this->info('Reminder processing completed successfully.');

        return Command::SUCCESS;
    }

    /**
     * Follow-up check:
     * Status = applied, updated_at > 7 days ago, no Activity in last 7 days.
     * Dedup: 1 follow-up reminder per application per 7-day period.
     */
    protected function checkFollowUpReminders(): void
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $applications = Application::with(['user', 'company', 'activities'])
            ->where('status', ApplicationStatus::APPLIED->value)
            ->where('updated_at', '<=', $sevenDaysAgo)
            ->get();

        foreach ($applications as $application) {
            $user = $application->user;

            if (!$user || !$user->wantsNotification('follow_ups')) {
                continue;
            }

            // Check if any activity occurred in the last 7 days
            $hasRecentActivity = $application->activities()
                ->where('created_at', '>=', $sevenDaysAgo)
                ->exists();

            if ($hasRecentActivity) {
                continue;
            }

            // Dedup check: check if FollowUpReminder notification exists for this application in the last 7 days
            $alreadyNotified = $user->notifications()
                ->where('type', FollowUpReminder::class)
                ->where('created_at', '>=', $sevenDaysAgo)
                ->get()
                ->contains(fn ($n) => ($n->data['application_id'] ?? null) === $application->id);

            if ($alreadyNotified) {
                continue;
            }

            $daysCount = (int) max(7, Carbon::now()->diffInDays($application->updated_at));

            $user->notify(new FollowUpReminder($application, $daysCount));
            $this->info("Sent FollowUpReminder for application: {$application->job_title}");
        }
    }

    /**
     * Next action check:
     * Query applications where next_action_date is tomorrow, today, or yesterday.
     * Dedup: 1 reminder per application per timing window per day.
     */
    protected function checkNextActionReminders(): void
    {
        $todayStr     = Carbon::today()->toDateString();
        $tomorrowStr  = Carbon::tomorrow()->toDateString();
        $yesterdayStr = Carbon::yesterday()->toDateString();

        $applications = Application::with(['user', 'company'])
            ->whereNotNull('next_action_date')
            ->whereDate('next_action_date', '>=', $yesterdayStr)
            ->whereDate('next_action_date', '<=', $tomorrowStr)
            ->get();

        foreach ($applications as $application) {
            $user = $application->user;

            if (!$user || !$user->wantsNotification('actions')) {
                continue;
            }

            $actionDateStr = $application->next_action_date->toDateString();

            if ($actionDateStr === $tomorrowStr) {
                $timing = 'tomorrow';
            } elseif ($actionDateStr === $todayStr) {
                $timing = 'today';
            } else {
                $timing = 'yesterday';
            }

            // Dedup check: check if NextActionReminder with same application_id and timing was sent today
            $alreadyNotified = $user->notifications()
                ->where('type', NextActionReminder::class)
                ->whereDate('created_at', Carbon::today())
                ->get()
                ->contains(fn ($n) =>
                    ($n->data['application_id'] ?? null) === $application->id &&
                    ($n->data['timing'] ?? null) === $timing
                );

            if ($alreadyNotified) {
                continue;
            }

            $user->notify(new NextActionReminder($application, $timing));
            $this->info("Sent NextActionReminder ({$timing}) for application: {$application->job_title}");
        }
    }

    /**
     * Interview check:
     * Query interviews where scheduled_at is within next 24h or next 1h and outcome is pending/null.
     * Dedup: 1 reminder per interview per time window (24h vs 1h).
     */
    protected function checkInterviewReminders(): void
    {
        $now = Carbon::now();
        $in24Hours = (clone $now)->addHours(24);

        $interviews = Interview::with(['application.user', 'application.company'])
            ->where(function ($q) {
                $q->whereNull('outcome')
                  ->orWhere('outcome', '')
                  ->orWhere('outcome', 'pending');
            })
            ->whereBetween('scheduled_at', [$now, $in24Hours])
            ->get();

        foreach ($interviews as $interview) {
            $application = $interview->application;
            $user = $application?->user;

            if (!$user || !$user->wantsNotification('interviews')) {
                continue;
            }

            $diffInMinutes = $now->diffInMinutes($interview->scheduled_at, false);

            if ($diffInMinutes < 0) {
                continue; // already passed
            }

            // Determine if within 1h or 24h window
            $windows = [];

            if ($diffInMinutes <= 60) {
                $windows[] = '1h';
            }

            if ($diffInMinutes <= 1440) { // 24 hours
                $windows[] = '24h';
            }

            foreach ($windows as $window) {
                // Dedup check for interview_id & window
                $alreadyNotified = $user->notifications()
                    ->where('type', InterviewReminder::class)
                    ->get()
                    ->contains(fn ($n) =>
                        ($n->data['interview_id'] ?? null) === $interview->id &&
                        ($n->data['window'] ?? null) === $window
                    );

                if (!$alreadyNotified) {
                    $user->notify(new InterviewReminder($interview, $window));
                    $this->info("Sent InterviewReminder ({$window}) for interview round: {$interview->round_type}");
                }
            }
        }
    }
}
