<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Livewire\Notifications\Bell;
use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Livewire\Profile\NotificationPreferencesForm;
use App\Models\Application;
use App\Models\Company;
use App\Models\Interview;
use App\Models\User;
use App\Notifications\FollowUpReminder;
use App\Notifications\InterviewReminder;
use App\Notifications\NextActionReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_reminders_command_runs_without_errors(): void
    {
        $this->artisan('app:send-reminders')
            ->assertExitCode(0);
    }

    public function test_follow_up_reminder_generated_for_applied_status_after_7_days(): void
    {
        $user = User::factory()->create();
        $company = Company::create(['user_id' => $user->id, 'name' => 'Acme Corp']);

        $application = Application::create([
            'user_id'     => $user->id,
            'company_id'  => $company->id,
            'job_title'   => 'Software Engineer',
            'status'      => ApplicationStatus::APPLIED->value,
            'date_applied'=> Carbon::now()->subDays(8),
        ]);

        // Manually update updated_at and activity created_at to 8 days ago
        $application->timestamps = false;
        $application->updated_at = Carbon::now()->subDays(8);
        $application->save();

        $application->activities()->update(['created_at' => Carbon::now()->subDays(8)]);

        Artisan::call('app:send-reminders');

        $this->assertEquals(1, $user->notifications()->count());
        $notification = $user->notifications()->first();
        $this->assertEquals(FollowUpReminder::class, $notification->type);
        $this->assertStringContainsString('Software Engineer', $notification->data['message']);
    }

    public function test_next_action_reminder_generated(): void
    {
        $user = User::factory()->create();
        $company = Company::create(['user_id' => $user->id, 'name' => 'Tech Corp']);

        Application::create([
            'user_id'          => $user->id,
            'company_id'       => $company->id,
            'job_title'        => 'Backend Engineer',
            'status'           => ApplicationStatus::APPLIED->value,
            'next_action_date' => Carbon::tomorrow()->toDateString(),
        ]);

        Artisan::call('app:send-reminders');

        $this->assertEquals(1, $user->notifications()->count());
        $notification = $user->notifications()->first();
        $this->assertEquals(NextActionReminder::class, $notification->type);
        $this->assertEquals('tomorrow', $notification->data['timing']);
    }

    public function test_interview_reminder_generated(): void
    {
        $user = User::factory()->create();
        $company = Company::create(['user_id' => $user->id, 'name' => 'Startup Inc']);

        $application = Application::create([
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'job_title'  => 'Fullstack Developer',
            'status'     => ApplicationStatus::INTERVIEW->value,
        ]);

        Interview::create([
            'application_id' => $application->id,
            'round_type'     => 'technical',
            'scheduled_at'   => Carbon::now()->addHours(12),
            'outcome'        => 'pending',
        ]);

        Artisan::call('app:send-reminders');

        $this->assertGreaterThanOrEqual(1, $user->notifications()->count());
        $notification = $user->notifications()->where('type', InterviewReminder::class)->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('Interview tomorrow', $notification->data['message']);
    }

    public function test_reminders_are_deduplicated(): void
    {
        $user = User::factory()->create();
        $company = Company::create(['user_id' => $user->id, 'name' => 'Corp Inc']);

        $application = Application::create([
            'user_id'          => $user->id,
            'company_id'       => $company->id,
            'job_title'        => 'DevOps Engineer',
            'status'           => ApplicationStatus::APPLIED->value,
            'next_action_date' => Carbon::tomorrow()->toDateString(),
        ]);

        // Run command twice
        Artisan::call('app:send-reminders');
        Artisan::call('app:send-reminders');

        $this->assertEquals(1, $user->notifications()->count());
    }

    public function test_user_preferences_disable_reminders(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email'      => false,
                'follow_ups' => false,
                'interviews' => true,
                'actions'    => true,
            ]
        ]);

        $company = Company::create(['user_id' => $user->id, 'name' => 'Test Co']);

        $application = Application::create([
            'user_id'     => $user->id,
            'company_id'  => $company->id,
            'job_title'   => 'QA Lead',
            'status'      => ApplicationStatus::APPLIED->value,
        ]);

        $application->timestamps = false;
        $application->updated_at = Carbon::now()->subDays(10);
        $application->save();

        Artisan::call('app:send-reminders');

        // No follow-up notification sent because follow_ups preference is false
        $this->assertEquals(0, $user->notifications()->count());
    }

    public function test_notification_bell_and_mark_all_read(): void
    {
        $user = User::factory()->create();
        $company = Company::create(['user_id' => $user->id, 'name' => 'Beta LLC']);

        $application = Application::create([
            'user_id'          => $user->id,
            'company_id'       => $company->id,
            'job_title'        => 'UI Designer',
            'status'           => ApplicationStatus::APPLIED->value,
            'next_action_date' => Carbon::today()->toDateString(),
        ]);

        Artisan::call('app:send-reminders');
        $this->assertEquals(1, $user->unreadNotifications()->count());

        // Test Livewire Bell component markAllAsRead
        Livewire::actingAs($user)
            ->test(Bell::class)
            ->call('markAllAsRead')
            ->assertHasNoErrors();

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_full_notifications_page_renders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notifications');
    }

    public function test_profile_notification_preferences_form_updates(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(NotificationPreferencesForm::class)
            ->set('email', true)
            ->set('follow_ups', false)
            ->set('interviews', true)
            ->set('actions', true)
            ->call('updateNotificationPreferences')
            ->assertHasNoErrors();

        $user->refresh();
        $prefs = $user->getNotificationPreferences();

        $this->assertTrue($prefs['email']);
        $this->assertFalse($prefs['follow_ups']);
        $this->assertTrue($prefs['interviews']);
        $this->assertTrue($prefs['actions']);
    }
}
