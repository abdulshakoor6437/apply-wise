<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Models\Activity;
use App\Models\Application;
use App\Models\Company;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_successfully_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertViewHas('pipelineFunnel')
            ->assertViewHas('avgResponseTime')
            ->assertViewHas('mostActiveSource')
            ->assertViewHas('interviewSuccessRate')
            ->assertViewHas('applicationsOverTimeChart')
            ->assertViewHas('responseRateBySourceChart')
            ->assertViewHas('stageDurationsChart');
    }

    public function test_dashboard_calculates_correct_analytics_metrics(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Corp',
        ]);

        $app1 = Application::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'job_title' => 'Software Engineer',
            'status' => 'interview',
            'source' => 'LinkedIn',
            'date_applied' => now()->subDays(10)->toDateString(),
        ]);

        Activity::create([
            'application_id' => $app1->id,
            'from_status' => 'applied',
            'to_status' => 'phone_screen',
            'created_at' => now()->subDays(7),
        ]);

        $app2 = Application::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'job_title' => 'Backend Developer',
            'status' => 'applied',
            'source' => 'LinkedIn',
            'date_applied' => now()->subDays(5)->toDateString(),
        ]);

        Interview::create([
            'application_id' => $app1->id,
            'round_type' => 'technical',
            'scheduled_at' => now()->subDays(2),
            'outcome' => 'passed',
        ]);

        Interview::create([
            'application_id' => $app1->id,
            'round_type' => 'final',
            'scheduled_at' => now()->subDay(),
            'outcome' => 'failed',
        ]);

        Livewire::test(Dashboard::class)
            ->assertViewHas('totalApplications', 2)
            ->assertViewHas('mostActiveSource', function ($source) {
                return $source['name'] === 'LinkedIn' && $source['count'] === 2;
            })
            ->assertViewHas('interviewSuccessRate', function ($rate) {
                return $rate['rate'] == 50.0 && $rate['passed'] === 1 && $rate['failed'] === 1;
            })
            ->assertViewHas('pipelineFunnel', function ($funnel) {
                return $funnel['counts']['applied'] === 2 && $funnel['counts']['interview'] === 1;
            });
    }
}
