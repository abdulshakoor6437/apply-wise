<?php

namespace App\Livewire;

use App\Enums\ApplicationStatus;
use App\Models\Activity;
use App\Models\Application;
use App\Models\Document;
use App\Models\Interview;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public array $applicationsOverTimeChart = [];
    public array $responseRateBySourceChart = [];
    public array $stageDurationsChart = [];

    public function mount(): void
    {
        $this->loadChartData();
    }

    public function loadChartData(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $userApps = Application::where('user_id', $user->id)
            ->with(['activities'])
            ->get();

        // 1. Applications Over Time
        $appsOverTimeLabels = [];
        $appsOverTimeData = [];
        for ($i = 11; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = (clone $weekStart)->endOfWeek();

            $label = $weekStart->format('M j');
            $count = $userApps->filter(function($app) use ($weekStart, $weekEnd) {
                $date = $app->date_applied
                    ? Carbon::parse($app->date_applied)
                    : Carbon::parse($app->created_at);
                return $date >= $weekStart && $date <= $weekEnd;
            })->count();

            $appsOverTimeLabels[] = $label;
            $appsOverTimeData[] = $count;
        }

        // 2. Response Rate by Source
        $respondedStatuses = ['phone_screen', 'interview', 'offer', 'accepted', 'rejected'];
        $standardSources = ['LinkedIn', 'Indeed', 'Company Website', 'Referral', 'Other'];
        $responseRateBySourceLabels = $standardSources;
        $responseRateBySourceData = [];
        $responseRateBySourceTotals = [];
        $responseRateBySourceResponded = [];

        foreach ($standardSources as $src) {
            $matchingApps = $userApps->filter(function($app) use ($src, $standardSources) {
                $appSrc = trim($app->source ?: '');
                if ($src === 'Other') {
                    return !in_array($appSrc, ['LinkedIn', 'Indeed', 'Company Website', 'Referral']) || $appSrc === 'Other';
                }
                return strcasecmp($appSrc, $src) === 0;
            });

            $totalForSrc = $matchingApps->count();
            $respondedForSrc = $matchingApps->filter(function($app) use ($respondedStatuses) {
                $currentStatus = $app->status?->value ?? $app->status;
                if (in_array($currentStatus, $respondedStatuses)) return true;
                return $app->activities->contains(fn($act) => in_array($act->to_status, $respondedStatuses));
            })->count();

            $rate = $totalForSrc > 0 ? round(($respondedForSrc / $totalForSrc) * 100, 1) : 0;

            $responseRateBySourceData[] = $rate;
            $responseRateBySourceTotals[] = $totalForSrc;
            $responseRateBySourceResponded[] = $respondedForSrc;
        }

        // 3. Average Time in Each Stage
        $trackStages = ['wishlist', 'applied', 'phone_screen', 'interview', 'offer'];
        $stageDurationBuckets = array_fill_keys($trackStages, []);

        foreach ($userApps as $app) {
            $activitiesSorted = $app->activities->sortBy('created_at')->values();
            $actCount = $activitiesSorted->count();

            $appStart = $app->date_applied
                ? Carbon::parse($app->date_applied)->startOfDay()
                : Carbon::parse($app->created_at);

            if ($actCount > 0) {
                $firstAct = $activitiesSorted[0];
                $initialStage = $firstAct->from_status ?: 'applied';
                if (in_array($initialStage, $trackStages)) {
                    $firstActTime = Carbon::parse($firstAct->created_at);
                    $daysInInitial = max(0, $appStart->diffInDays($firstActTime));
                    $stageDurationBuckets[$initialStage][] = $daysInInitial;
                }

                for ($idx = 0; $idx < $actCount; $idx++) {
                    $currentAct = $activitiesSorted[$idx];
                    $status = $currentAct->to_status;
                    if (!in_array($status, $trackStages)) continue;

                    $startTime = Carbon::parse($currentAct->created_at);
                    $endTime = ($idx + 1 < $actCount)
                        ? Carbon::parse($activitiesSorted[$idx + 1]->created_at)
                        : now();

                    $daysSpent = max(0, $startTime->diffInDays($endTime));
                    $stageDurationBuckets[$status][] = $daysSpent;
                }
            } else {
                $currentStatus = $app->status?->value ?? $app->status;
                if (in_array($currentStatus, $trackStages)) {
                    $daysSpent = max(0, $appStart->diffInDays(now()));
                    $stageDurationBuckets[$currentStatus][] = $daysSpent;
                }
            }
        }

        $stageDurationData = [];
        foreach ($trackStages as $stg) {
            $b = $stageDurationBuckets[$stg];
            $stageDurationData[] = !empty($b) ? round(array_sum($b) / count($b), 1) : 0;
        }

        $this->applicationsOverTimeChart = [
            'labels' => $appsOverTimeLabels,
            'data'   => $appsOverTimeData,
        ];
        $this->responseRateBySourceChart = [
            'labels'    => $responseRateBySourceLabels,
            'data'      => $responseRateBySourceData,
            'totals'    => $responseRateBySourceTotals,
            'responded' => $responseRateBySourceResponded,
        ];
        $this->stageDurationsChart = [
            'labels' => ['Wishlist', 'Applied', 'Phone Screen', 'Interview', 'Offer'],
            'data'   => $stageDurationData,
        ];
    }

    public function render(): View
    {
        $user = Auth::user();

        $userApps = Application::where('user_id', $user->id)
            ->with(['activities', 'company'])
            ->get();

        $totalApplications = $userApps->count();

        // Upcoming scheduled interviews (scheduled_at >= now AND outcome = pending)
        $interviewsScheduled = Interview::whereHas(
            'application',
            fn ($q) => $q->where('user_id', $user->id)
        )->where('scheduled_at', '>=', now())
         ->where(function($q) {
             $q->whereNull('outcome')->orWhere('outcome', 'pending');
         })->count();

        // Next 3 upcoming interviews for right sidebar card
        $upcomingInterviews = Interview::whereHas(
            'application',
            fn ($q) => $q->where('user_id', $user->id)
        )->where('scheduled_at', '>=', now())
         ->where(function($q) {
             $q->whereNull('outcome')->orWhere('outcome', 'pending');
         })
         ->with(['application.company'])
         ->orderBy('scheduled_at', 'asc')
         ->take(3)
         ->get();

        // Top Performing Resumes
        $topResumes = Document::where('user_id', $user->id)
            ->where('doc_type', 'resume')
            ->has('applications')
            ->with('applications')
            ->get()
            ->sortByDesc(fn($doc) => $doc->response_rate ?? -1)
            ->take(3);

        // Applications in offer or accepted stage
        $offersReceived = $userApps->filter(fn($app) => in_array($app->status?->value ?? $app->status, ['offer', 'accepted']))->count();

        $companiesTracked = $user->companies()->count();

        // Recent Activity (last 8) - status changes + manual notes
        $recentActivities = Activity::whereHas('application', fn ($q) => $q->where('user_id', $user->id))
            ->with(['application.company'])
            ->take(8)
            ->get();

        // Upcoming Actions (next_action_date within next 7 days or overdue)
        $upcomingActions = Application::with('company')
            ->where('user_id', $user->id)
            ->whereNotNull('next_action_date')
            ->where('next_action_date', '<=', now()->addDays(7))
            ->orderBy('next_action_date', 'asc')
            ->take(5)
            ->get();

        // Pipeline summary breakdown
        $pipelineData = [];
        $kanbanStages = ApplicationStatus::kanbanStages();
        foreach ($kanbanStages as $stage) {
            $count = $userApps->filter(fn($app) => ($app->status?->value ?? $app->status) === $stage->value)->count();
            $pipelineData[] = [
                'stage' => $stage,
                'count' => $count,
                'percentage' => $totalApplications > 0 ? round(($count / $totalApplications) * 100) : 0,
            ];
        }

        // ==========================================
        // PHASE 7: ANALYTICS & METRICS CALCULATIONS
        // ==========================================

        // 1. PIPELINE FUNNEL (Cumulative stage counts & conversion rates)
        $stageRanks = [
            'wishlist'     => 1,
            'applied'      => 2,
            'phone_screen' => 3,
            'interview'    => 4,
            'offer'        => 5,
            'accepted'     => 6,
        ];

        $appMaxRanks = $userApps->map(function ($app) use ($stageRanks) {
            $currentStatus = $app->status?->value ?? $app->status;
            $ranks = [];
            if (isset($stageRanks[$currentStatus])) {
                $ranks[] = $stageRanks[$currentStatus];
            }
            foreach ($app->activities as $act) {
                if (isset($stageRanks[$act->to_status])) {
                    $ranks[] = $stageRanks[$act->to_status];
                }
                if (isset($stageRanks[$act->from_status])) {
                    $ranks[] = $stageRanks[$act->from_status];
                }
            }
            return !empty($ranks) ? max($ranks) : 1;
        });

        $wishlistCount    = $appMaxRanks->filter(fn($r) => $r >= 1)->count();
        $appliedCount     = $appMaxRanks->filter(fn($r) => $r >= 2)->count();
        $phoneScreenCount = $appMaxRanks->filter(fn($r) => $r >= 3)->count();
        $interviewCount   = $appMaxRanks->filter(fn($r) => $r >= 4)->count();
        $offerCount       = $appMaxRanks->filter(fn($r) => $r >= 5)->count();
        $acceptedCount    = $appMaxRanks->filter(fn($r) => $r >= 6)->count();

        $pipelineFunnel = [
            'counts' => [
                'wishlist'     => $wishlistCount,
                'applied'      => $appliedCount,
                'phone_screen' => $phoneScreenCount,
                'interview'    => $interviewCount,
                'offer'        => $offerCount,
                'accepted'     => $acceptedCount,
            ],
            'conversions' => [
                'wishlist_to_applied'       => $wishlistCount > 0 ? round(($appliedCount / $wishlistCount) * 100, 1) : 0,
                'applied_to_phone_screen'   => $appliedCount > 0 ? round(($phoneScreenCount / $appliedCount) * 100, 1) : 0,
                'phone_screen_to_interview' => $phoneScreenCount > 0 ? round(($interviewCount / $phoneScreenCount) * 100, 1) : 0,
                'interview_to_offer'        => $interviewCount > 0 ? round(($offerCount / $interviewCount) * 100, 1) : 0,
                'offer_to_accepted'         => $offerCount > 0 ? round(($acceptedCount / $offerCount) * 100, 1) : 0,
            ],
            'overallConversionRate' => $appliedCount > 0 ? round(($interviewCount / $appliedCount) * 100, 1) : 0,
        ];

        // 2. SECONDARY METRICS ROW
        // (a) Average Response Time
        $respondedStatuses = ['phone_screen', 'interview', 'offer', 'accepted', 'rejected'];
        $responseTimes = [];

        foreach ($userApps as $app) {
            $firstResponse = $app->activities
                ->filter(fn($act) => in_array($act->to_status, $respondedStatuses))
                ->sortBy('created_at')
                ->first();

            if ($firstResponse) {
                $start = $app->date_applied
                    ? Carbon::parse($app->date_applied)->startOfDay()
                    : $app->created_at;
                $end = Carbon::parse($firstResponse->created_at);
                $days = max(0, $start->diffInDays($end));
                $responseTimes[] = $days;
            }
        }

        $avgResponseTime = !empty($responseTimes)
            ? round(array_sum($responseTimes) / count($responseTimes), 1)
            : 0;

        // (b) Most Active Source
        $sourcesGrouped = $userApps
            ->groupBy(fn($app) => trim($app->source ?: 'Unknown'))
            ->map(fn($group) => $group->count())
            ->sortByDesc(fn($count) => $count);

        $topSourceKey = $sourcesGrouped->keys()->first();
        $topSourceCount = $sourcesGrouped->first() ?? 0;
        $mostActiveSource = [
            'name'  => $topSourceKey && $topSourceKey !== 'Unknown' ? $topSourceKey : ($totalApplications > 0 ? 'Not Specified' : 'None'),
            'count' => $topSourceCount,
        ];

        // (c) Interview Success Rate
        $userInterviews = Interview::whereHas('application', fn($q) => $q->where('user_id', $user->id))->get();
        $passedCount = $userInterviews->where('outcome', 'passed')->count();
        $failedCount = $userInterviews->where('outcome', 'failed')->count();
        $totalCompletedInterviews = $passedCount + $failedCount;
        $interviewSuccessRate = [
            'rate'   => $totalCompletedInterviews > 0 ? round(($passedCount / $totalCompletedInterviews) * 100, 1) : 0,
            'passed' => $passedCount,
            'failed' => $failedCount,
            'total'  => $totalCompletedInterviews,
        ];

        // 3. CHART.JS DATASETS
        $this->loadChartData();

        return view('livewire.pages.dashboard.index', [
            'totalApplications'     => $totalApplications,
            'interviewsScheduled'   => $interviewsScheduled,
            'upcomingInterviews'    => $upcomingInterviews,
            'topResumes'            => $topResumes,
            'offersReceived'        => $offersReceived,
            'companiesTracked'      => $companiesTracked,
            'recentActivities'      => $recentActivities,
            'upcomingActions'       => $upcomingActions,
            'pipelineSummary'       => $pipelineData,

            // Phase 7 Analytics
            'pipelineFunnel'            => $pipelineFunnel,
            'avgResponseTime'           => $avgResponseTime,
            'mostActiveSource'          => $mostActiveSource,
            'interviewSuccessRate'      => $interviewSuccessRate,
            'applicationsOverTimeChart' => $this->applicationsOverTimeChart,
            'responseRateBySourceChart' => $this->responseRateBySourceChart,
            'stageDurationsChart'       => $this->stageDurationsChart,
        ])->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
