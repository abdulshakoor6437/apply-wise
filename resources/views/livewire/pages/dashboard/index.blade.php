<div class="space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100 tracking-tight">Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Overview of your active job search pipeline and key performance analytics.</p>
        </div>
        <div class="flex items-center gap-3">
            <a
                href="{{ route('applications', ['create' => 1]) }}"
                wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                id="dashboard-new-app-btn"
            >
                <x-heroicon-o-plus class="w-4 h-4" />
                Add Application
            </a>
        </div>
    </div>

    {{-- SECTION 1: Main Stats Row (2 Cards on Mobile, 4 Cards on Desktop) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        {{-- Card 1: Total Applications --}}
        <div class="bg-indigo-50/70 dark:bg-slate-900 rounded-xl border border-indigo-100 dark:border-slate-800 shadow-sm p-4 sm:p-6 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0">
                    <x-heroicon-o-briefcase class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <span class="text-[10px] sm:text-xs font-semibold text-indigo-700 dark:text-indigo-400 bg-indigo-100/80 dark:bg-indigo-950/60 px-2 py-0.5 sm:py-1 rounded-md">Total</span>
            </div>
            <div class="mt-3 sm:mt-4">
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-slate-100 tracking-tight" id="stat-total-apps">{{ $totalApplications }}</p>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-slate-400 mt-1">Total Applications</p>
            </div>
        </div>

        {{-- Card 2: Interviews Scheduled --}}
        <div class="bg-purple-50/70 dark:bg-slate-900 rounded-xl border border-purple-100 dark:border-slate-800 shadow-sm p-4 sm:p-6 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md shadow-purple-500/20 shrink-0">
                    <x-heroicon-o-calendar-days class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <span class="text-[10px] sm:text-xs font-semibold text-purple-700 dark:text-purple-400 bg-purple-100/80 dark:bg-purple-950/60 px-2 py-0.5 sm:py-1 rounded-md">Scheduled</span>
            </div>
            <div class="mt-3 sm:mt-4">
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-slate-100 tracking-tight" id="stat-interviews-scheduled">{{ $interviewsScheduled }}</p>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-slate-400 mt-1">Interviews Scheduled</p>
            </div>
        </div>

        {{-- Card 3: Offers Received --}}
        <div class="bg-emerald-50/70 dark:bg-slate-900 rounded-xl border border-emerald-100 dark:border-slate-800 shadow-sm p-4 sm:p-6 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0">
                    <x-heroicon-o-trophy class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <span class="text-[10px] sm:text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-100/80 dark:bg-emerald-950/60 px-2 py-0.5 sm:py-1 rounded-md">Offers</span>
            </div>
            <div class="mt-3 sm:mt-4">
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-slate-100 tracking-tight" id="stat-offers-received">{{ $offersReceived }}</p>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-slate-400 mt-1">Offers Received</p>
            </div>
        </div>

        {{-- Card 4: Companies Tracked --}}
        <div class="bg-blue-50/70 dark:bg-slate-900 rounded-xl border border-blue-100 dark:border-slate-800 shadow-sm p-4 sm:p-6 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                    <x-heroicon-o-building-office class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <span class="text-[10px] sm:text-xs font-semibold text-blue-700 dark:text-blue-400 bg-blue-100/80 dark:bg-blue-950/60 px-2 py-0.5 sm:py-1 rounded-md">Companies</span>
            </div>
            <div class="mt-3 sm:mt-4">
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-slate-100 tracking-tight" id="stat-companies-tracked">{{ $companiesTracked }}</p>
                <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-slate-400 mt-1">Companies Tracked</p>
            </div>
        </div>
    </div>

    {{-- SECTION 2: Secondary Metrics Row (3 Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        {{-- Card 1: Average Response Time --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <x-heroicon-o-clock class="w-5 h-5 text-amber-600" />
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Avg Response Time</p>
                <p class="text-xl font-bold text-gray-900 mt-0.5" id="metric-avg-response-time">
                    {{ $avgResponseTime > 0 ? $avgResponseTime . ' days' : 'N/A' }}
                </p>
                <p class="text-[11px] text-gray-400 mt-0.5">Days to initial status response</p>
            </div>
        </div>

        {{-- Card 2: Most Active Source --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5 text-sky-600" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Most Active Source</p>
                <p class="text-xl font-bold text-gray-900 mt-0.5 truncate" id="metric-most-active-source">
                    {{ $mostActiveSource['name'] }}
                </p>
                <p class="text-[11px] text-gray-400 mt-0.5">
                    {{ $mostActiveSource['count'] }} {{ Str::plural('application', $mostActiveSource['count']) }}
                </p>
            </div>
        </div>

        {{-- Card 3: Interview Success Rate --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center shrink-0">
                <x-heroicon-o-check-badge class="w-5 h-5 text-teal-600" />
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Interview Pass Rate</p>
                <p class="text-xl font-bold text-gray-900 mt-0.5" id="metric-interview-success-rate">
                    {{ $interviewSuccessRate['rate'] }}%
                </p>
                <p class="text-[11px] text-gray-400 mt-0.5">
                    {{ $interviewSuccessRate['passed'] }} passed / {{ $interviewSuccessRate['failed'] }} failed ({{ $interviewSuccessRate['total'] }} total)
                </p>
            </div>
        </div>
    </div>

    {{-- SECTION 3: Two-Column Section (Pipeline Funnel 2/3 + Pipeline Summary 1/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column (2/3 width): Application Pipeline Funnel --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                <div>
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <x-heroicon-o-funnel class="w-5 h-5 text-indigo-600" />
                        Application Pipeline Funnel
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Cumulative stage progression and stage-to-stage conversion efficiency.</p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-100 text-xs font-semibold text-indigo-700 self-start sm:self-auto">
                    <span>Overall Conversion:</span>
                    <span class="font-bold text-indigo-900">{{ $pipelineFunnel['overallConversionRate'] }}%</span>
                    <span class="text-[10px] text-indigo-500 font-normal">(Applied → Interview)</span>
                </div>
            </div>

            <div class="flex-1 py-6 space-y-4">
                @php
                    $funnelStages = [
                        ['key' => 'wishlist',     'label' => 'Wishlist',     'count' => $pipelineFunnel['counts']['wishlist'],     'bg' => 'bg-slate-500',   'text' => 'text-slate-700'],
                        ['key' => 'applied',      'label' => 'Applied',      'count' => $pipelineFunnel['counts']['applied'],      'bg' => 'bg-indigo-600',  'text' => 'text-indigo-700',   'conv' => $pipelineFunnel['conversions']['wishlist_to_applied'],       'from' => 'Wishlist'],
                        ['key' => 'phone_screen', 'label' => 'Phone Screen', 'count' => $pipelineFunnel['counts']['phone_screen'], 'bg' => 'bg-amber-500',   'text' => 'text-amber-700',    'conv' => $pipelineFunnel['conversions']['applied_to_phone_screen'],   'from' => 'Applied'],
                        ['key' => 'interview',    'label' => 'Interview',    'count' => $pipelineFunnel['counts']['interview'],    'bg' => 'bg-purple-600',  'text' => 'text-purple-700',   'conv' => $pipelineFunnel['conversions']['phone_screen_to_interview'], 'from' => 'Phone Screen'],
                        ['key' => 'offer',        'label' => 'Offer',        'count' => $pipelineFunnel['counts']['offer'],        'bg' => 'bg-emerald-600', 'text' => 'text-emerald-700',  'conv' => $pipelineFunnel['conversions']['interview_to_offer'],        'from' => 'Interview'],
                        ['key' => 'accepted',     'label' => 'Accepted',     'count' => $pipelineFunnel['counts']['accepted'],     'bg' => 'bg-teal-600',    'text' => 'text-teal-700',     'conv' => $pipelineFunnel['conversions']['offer_to_accepted'],         'from' => 'Offer'],
                    ];

                    $maxFunnelCount = max(1, $pipelineFunnel['counts']['wishlist']);
                @endphp

                @foreach($funnelStages as $index => $stage)
                    @if(isset($stage['conv']))
                        {{-- Conversion Rate Indicator between stages --}}
                        <div class="flex items-center gap-2 pl-24 my-1">
                            <span class="text-gray-300">↓</span>
                            <span class="text-[11px] font-semibold {{ $stage['text'] }} bg-gray-50 px-2 py-0.5 rounded border border-gray-100">
                                {{ $stage['conv'] }}% conversion from {{ $stage['from'] }}
                            </span>
                        </div>
                    @endif

                    @php
                        $barPct = round(($stage['count'] / $maxFunnelCount) * 100);
                        $displayPct = max($barPct, $stage['count'] > 0 ? 8 : 0);
                    @endphp

                    <div class="flex items-center gap-4">
                        <div class="w-24 text-right shrink-0">
                            <span class="text-xs font-bold text-gray-800">{{ $stage['label'] }}</span>
                        </div>
                        <div class="flex-1 bg-gray-100 rounded-lg h-9 p-1 relative overflow-hidden flex items-center">
                            <div
                                class="{{ $stage['bg'] }} h-full rounded-md transition-all duration-500 flex items-center justify-end pr-3"
                                style="width: {{ $displayPct }}%"
                            ></div>
                            <span class="absolute left-3 text-xs font-bold {{ $barPct > 20 ? 'text-white' : 'text-gray-800' }}">
                                {{ $stage['count'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right Column (1/3 width): Pipeline Summary --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2 pb-3 border-b border-gray-100">
                <x-heroicon-o-chart-bar class="w-5 h-5 text-indigo-500" />
                Pipeline Breakdown
            </h2>

            <div class="pt-4 space-y-4 flex-1">
                @foreach($pipelineSummary as $item)
                    @php
                        $barBg = match($item['stage']->color()) {
                            'blue' => 'bg-blue-500',
                            'yellow' => 'bg-amber-500',
                            'purple' => 'bg-purple-500',
                            'green' => 'bg-emerald-500',
                            'emerald' => 'bg-emerald-500',
                            'red' => 'bg-rose-500',
                            default => 'bg-indigo-500',
                        };
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-medium text-gray-700">{{ $item['stage']->label() }}</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-gray-900">{{ $item['count'] }}</span>
                                <span class="text-[10px] text-gray-400">({{ $item['percentage'] }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="{{ $barBg }} h-2 rounded-full transition-all duration-300" style="width: {{ max($item['percentage'], $item['count'] > 0 ? 8 : 0) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SECTION 4: Two-Column Section (Charts: Applications Over Time 1/2 + Response Rate by Source 1/2) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Chart 1: Applications Over Time --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 shadow-sm p-6 flex flex-col"
             x-data
             x-init="$nextTick(() => { if (typeof initDashboardCharts === 'function') initDashboardCharts(); })"
        >
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-700">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-slate-100 flex items-center gap-2">
                        <x-heroicon-o-presentation-chart-line class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        Applications Over Time
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Application submission frequency over the last 12 weeks.</p>
                </div>
            </div>

            <div class="pt-4 flex-1 space-y-4">
                {{-- Graphical Visual Bar Representation --}}
                @php
                    $maxAppsOverTime = max(1, max($applicationsOverTimeChart['data'] ?? [1]));
                @endphp
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-900/60 border border-gray-100 dark:border-slate-700/80">
                    <div class="flex items-end justify-between gap-1 h-32 pt-2">
                        @foreach(($applicationsOverTimeChart['labels'] ?? []) as $idx => $label)
                            @php
                                $val = $applicationsOverTimeChart['data'][$idx] ?? 0;
                                $heightPct = round(($val / $maxAppsOverTime) * 100);
                                $barHeight = max($heightPct, $val > 0 ? 15 : 4);
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1 group relative">
                                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 transition-opacity">
                                    {{ $val }}
                                </span>
                                <div class="w-full max-w-[20px] bg-gray-200 dark:bg-slate-700 rounded-t-md overflow-hidden flex items-end h-20">
                                    <div class="w-full bg-gradient-to-t from-indigo-600 to-indigo-400 rounded-t-md transition-all duration-500"
                                         style="height: {{ $barHeight }}%">
                                    </div>
                                </div>
                                <span class="text-[9px] font-medium text-gray-400 dark:text-slate-500 truncate w-full text-center" title="{{ $label }}">
                                    {{ $label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Interactive Canvas --}}
                <div class="h-48 relative w-full">
                    <canvas id="chart-applications-over-time" class="w-full h-full block"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart 2: Response Rate by Source --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 shadow-sm p-6 flex flex-col"
             x-data
             x-init="$nextTick(() => { if (typeof initDashboardCharts === 'function') initDashboardCharts(); })"
        >
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-700">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-slate-100 flex items-center gap-2">
                        <x-heroicon-o-chart-pie class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        Response Rate by Source
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Employer response percentages broken down by job source.</p>
                </div>
            </div>

            <div class="pt-4 flex-1 space-y-4">
                {{-- Graphical Visual Progress Bars Representation --}}
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-900/60 border border-gray-100 dark:border-slate-700/80 space-y-2.5">
                    @php
                        $sourceColors = ['bg-indigo-600', 'bg-blue-600', 'bg-cyan-500', 'bg-emerald-500', 'bg-purple-600'];
                    @endphp
                    @foreach(($responseRateBySourceChart['labels'] ?? []) as $idx => $sourceLabel)
                        @php
                            $ratePct = $responseRateBySourceChart['data'][$idx] ?? 0;
                            $totalCount = $responseRateBySourceChart['totals'][$idx] ?? 0;
                            $respCount = $responseRateBySourceChart['responded'][$idx] ?? 0;
                            $barColor = $sourceColors[$idx % count($sourceColors)];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $sourceLabel }}</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $ratePct }}%</span>
                                    <span class="text-[10px] text-gray-400 dark:text-slate-500">({{ $respCount }}/{{ $totalCount }})</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500" style="width: {{ max($ratePct, $totalCount > 0 ? 5 : 0) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Interactive Canvas --}}
                <div class="h-44 relative w-full">
                    <canvas id="chart-response-rate-by-source" class="w-full h-full block"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 5: Two-Column Section (Recent Activity 2/3 + Stacked Cards 1/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column (2/3 width): Recent Activity Feed --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-indigo-500" />
                    Recent Activity
                </h2>
                <a href="{{ route('applications') }}" wire:navigate class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                    View all
                </a>
            </div>

            <div class="flex-1 py-4">
                @if($recentActivities->isEmpty())
                    <div class="py-12 text-center flex flex-col items-center justify-center">
                        <x-heroicon-o-rectangle-stack class="w-12 h-12 text-gray-300 mb-3" />
                        <p class="text-sm font-semibold text-gray-800">No activity yet</p>
                        <p class="text-xs text-gray-400 mt-1">Start by adding your first job application.</p>
                        <a href="{{ route('applications', ['create' => 1]) }}" wire:navigate class="mt-4 px-3.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100 transition-colors">
                            + Add Application
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($recentActivities as $activity)
                            @php
                                $isManualNote = is_null($activity->from_status) && is_null($activity->to_status) && !is_null($activity->notes);
                                $toEnum = $activity->to_status ? \App\Enums\ApplicationStatus::tryFrom($activity->to_status) : null;
                                $fromEnum = $activity->from_status ? \App\Enums\ApplicationStatus::tryFrom($activity->from_status) : null;
                                $dotColor = $isManualNote ? 'bg-gray-400' : match($toEnum?->color()) {
                                    'blue' => 'bg-blue-500',
                                    'yellow' => 'bg-amber-500',
                                    'purple' => 'bg-purple-500',
                                    'green' => 'bg-emerald-500',
                                    'emerald' => 'bg-emerald-500',
                                    'red' => 'bg-rose-500',
                                    default => 'bg-indigo-500',
                                };
                            @endphp
                            <div class="flex items-start gap-3 text-sm">
                                <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }} mt-1.5 shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800 font-medium">
                                        @if($isManualNote)
                                            Added note on <span class="font-bold text-gray-900">{{ $activity->application->job_title ?? 'Application' }}</span>
                                            at <span class="font-semibold text-gray-900">{{ $activity->application->company->name ?? 'Company' }}</span>:
                                            <span class="italic text-gray-600">"{{ \Illuminate\Support\Str::limit($activity->notes, 50) }}"</span>
                                        @elseif($activity->from_status)
                                            Moved <span class="font-bold text-gray-900">{{ $activity->application->job_title ?? 'Application' }}</span>
                                            at <span class="font-semibold text-gray-900">{{ $activity->application->company->name ?? 'Company' }}</span>
                                            from <span class="text-gray-500">{{ $fromEnum ? $fromEnum->label() : ucfirst($activity->from_status) }}</span>
                                            <span class="text-gray-400">→</span>
                                            <span class="font-bold text-indigo-600">{{ $toEnum ? $toEnum->label() : ucfirst($activity->to_status) }}</span>
                                        @else
                                            Created application for <span class="font-bold text-gray-900">{{ $activity->application->job_title ?? 'Application' }}</span>
                                            at <span class="font-semibold text-gray-900">{{ $activity->application->company->name ?? 'Company' }}</span>
                                            with status <span class="font-bold text-indigo-600">{{ $toEnum ? $toEnum->label() : ucfirst($activity->to_status) }}</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column (1/3 width): Stacked Sidebar Cards --}}
        <div class="space-y-6">
            {{-- Card 1: Upcoming Actions --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2 pb-3 border-b border-gray-100">
                    <x-heroicon-o-calendar-days class="w-5 h-5 text-indigo-500" />
                    Upcoming Actions
                </h2>

                <div class="pt-3 space-y-3">
                    @forelse($upcomingActions as $action)
                        @php
                            $today = now()->startOfDay();
                            $actionDate = $action->next_action_date->startOfDay();
                            $diff = intval(round($today->diffInDays($actionDate, false)));
                        @endphp
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                            <div class="min-w-0 pr-2">
                                <a href="{{ route('applications.show', $action->id) }}" wire:navigate class="text-xs font-bold text-gray-900 hover:text-indigo-600 transition-colors truncate block">
                                    {{ $action->company->name ?? 'Company' }}
                                </a>
                                <p class="text-[11px] text-gray-500 truncate">{{ $action->job_title }}</p>
                            </div>
                            <div>
                                @if($diff < 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">
                                        Overdue
                                    </span>
                                @elseif($diff === 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800">
                                        Due today
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700">
                                        {{ $action->next_action_date->format('M j') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-gray-400 italic">
                            No upcoming actions scheduled.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Card 2: Upcoming Interviews --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2 pb-3 border-b border-gray-100">
                    <x-heroicon-o-video-camera class="w-5 h-5 text-indigo-500" />
                    Upcoming Interviews
                </h2>

                <div class="pt-3 space-y-3">
                    @forelse($upcomingInterviews as $interview)
                        @php
                            $roundBadge = match(strtolower($interview->round_type)) {
                                'phone_screen' => 'bg-blue-100 text-blue-800',
                                'technical' => 'bg-purple-100 text-purple-800',
                                'behavioral' => 'bg-teal-100 text-teal-800',
                                'onsite' => 'bg-amber-100 text-amber-800',
                                'final' => 'bg-rose-100 text-rose-800',
                                default => 'bg-gray-100 text-gray-800',
                            };

                            $roundLabel = match(strtolower($interview->round_type)) {
                                'phone_screen' => 'Phone Screen',
                                'technical' => 'Technical',
                                'behavioral' => 'Behavioral',
                                'onsite' => 'On-site',
                                'final' => 'Final',
                                default => ucfirst($interview->round_type),
                            };
                        @endphp
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                            <div class="min-w-0 pr-2">
                                <a href="{{ route('applications.show', $interview->application_id) }}" wire:navigate class="text-xs font-bold text-gray-900 hover:text-indigo-600 transition-colors truncate block">
                                    {{ $interview->application->company->name ?? 'Company' }}
                                </a>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[11px] text-gray-500 truncate">{{ $interview->application->job_title }}</span>
                                    <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded {{ $roundBadge }}">
                                        {{ $roundLabel }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">
                                    {{ $interview->scheduled_at->format('M j, Y \a\t g:i A') }}
                                </p>
                            </div>
                            <a href="{{ route('applications.show', $interview->application_id) }}" wire:navigate class="text-xs font-semibold text-indigo-600 hover:underline shrink-0">
                                View
                            </a>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-gray-400 italic">
                            No interviews scheduled.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Card 3: Top Performing Resumes --}}
            @if($topResumes->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2 pb-3 border-b border-gray-100">
                        <x-heroicon-o-document-chart-bar class="w-5 h-5 text-indigo-500" />
                        Resume Performance
                    </h2>

                    <div class="pt-3 space-y-3">
                        @foreach($topResumes as $resume)
                            @php
                                $rate = $resume->response_rate;
                                $rateColor = match(true) {
                                    $rate === null => 'bg-gray-300',
                                    $rate >= 30.0 => 'bg-emerald-500',
                                    $rate >= 15.0 => 'bg-amber-500',
                                    default => 'bg-rose-500',
                                };
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-semibold text-gray-800 truncate max-w-[170px]" title="{{ $resume->label }}">
                                        {{ $resume->label }}
                                    </span>
                                    <span class="font-bold text-gray-900">
                                        {{ $rate !== null ? $rate . '%' : 'No apps' }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $rateColor }} h-1.5 rounded-full transition-all duration-300" style="width: {{ max($rate ?? 0, 5) }}%"></div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    Linked to {{ $resume->applications->count() }} {{ Str::plural('app', $resume->applications->count()) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- SECTION 6: Full Width Chart (Average Time in Each Stage) --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 shadow-sm p-6"
         x-data
         x-init="$nextTick(() => { if (typeof initDashboardCharts === 'function') initDashboardCharts(); })"
    >
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-700">
            <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-slate-100 flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    Average Time Spent in Each Pipeline Stage
                </h2>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Average duration (in days) applications spend in each status before advancing.</p>
            </div>
        </div>

        <div class="pt-4 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            {{-- Graphical Visual Stage Duration Progress Bars Representation --}}
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-900/60 border border-gray-100 dark:border-slate-700/80 space-y-3.5">
                @php
                    $maxDuration = max(1, max($stageDurationsChart['data'] ?? [1]));
                @endphp
                @foreach(($stageDurationsChart['labels'] ?? []) as $idx => $stageLabel)
                    @php
                        $days = $stageDurationsChart['data'][$idx] ?? 0;
                        $pct = round(($days / $maxDuration) * 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $stageLabel }}</span>
                            <span class="font-bold text-purple-600 dark:text-purple-400">{{ $days }} {{ Str::plural('day', $days) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ max($pct, $days > 0 ? 6 : 0) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Interactive Canvas --}}
            <div class="h-56 relative w-full">
                <canvas id="chart-stage-durations" class="w-full h-full block"></canvas>
            </div>
        </div>
    </div>

    {{-- SECTION 7: Quick Actions Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        {{-- Action 1: Add Application --}}
        <a
            href="{{ route('applications', ['create' => 1]) }}"
            wire:navigate
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-4 group"
        >
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-600 transition-colors">
                <x-heroicon-o-plus class="w-5 h-5 text-indigo-600 group-hover:text-white transition-colors" />
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Add Application</p>
                <p class="text-xs text-gray-500">Track a new job opportunity</p>
            </div>
        </a>

        {{-- Action 2: Add Company --}}
        <a
            href="{{ route('companies', ['create' => 1]) }}"
            wire:navigate
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-4 group"
        >
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                <x-heroicon-o-building-office-2 class="w-5 h-5 text-blue-600 group-hover:text-white transition-colors" />
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Add Company</p>
                <p class="text-xs text-gray-500">Save company profile & details</p>
            </div>
        </a>

        {{-- Action 3: Upload Resume --}}
        <a
            href="{{ route('documents', ['upload' => 1]) }}"
            wire:navigate
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-4 group"
        >
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center group-hover:bg-teal-600 transition-colors">
                <x-heroicon-o-document-text class="w-5 h-5 text-teal-600 group-hover:text-white transition-colors" />
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 group-hover:text-teal-600 transition-colors">Upload Resume</p>
                <p class="text-xs text-gray-500">Manage resumes & documents</p>
            </div>
        </a>
    </div>

@script
<script>
    function initDashboardCharts() {
        const Chart = window.Chart;
        if (typeof Chart === 'undefined') {
            setTimeout(initDashboardCharts, 100);
            return;
        }

        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.6)';

        const data1 = @json($applicationsOverTimeChart);
        const data2 = @json($responseRateBySourceChart);
        const data3 = @json($stageDurationsChart);

        // Chart 1: Applications Over Time (Line Chart)
        const c1 = document.getElementById('chart-applications-over-time');
        if (c1 && data1) {
            if (window.chart1Instance) { window.chart1Instance.destroy(); }
            window.chart1Instance = new Chart(c1.getContext('2d'), {
                type: 'line',
                data: {
                    labels: data1.labels || [],
                    datasets: [{
                        label: 'Applications',
                        data: data1.data || [],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                            titleColor: isDark ? '#f8fafc' : '#0f172a',
                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                            borderColor: isDark ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, precision: 0, color: textColor },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: { color: textColor },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Chart 2: Response Rate by Source (Bar Chart)
        const c2 = document.getElementById('chart-response-rate-by-source');
        if (c2 && data2) {
            if (window.chart2Instance) { window.chart2Instance.destroy(); }
            window.chart2Instance = new Chart(c2.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data2.labels || [],
                    datasets: [{
                        label: 'Response Rate (%)',
                        data: data2.data || [],
                        backgroundColor: ['#6366f1', '#3b82f6', '#06b6d4', '#10b981', '#8b5cf6'],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                            titleColor: isDark ? '#f8fafc' : '#0f172a',
                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                            borderColor: isDark ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const idx = context.dataIndex;
                                    const totals = data2.totals || [];
                                    const responded = data2.responded || [];
                                    return ` ${context.raw}% (${responded[idx] || 0}/${totals[idx] || 0} responded)`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: v => v + '%', color: textColor },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: { color: textColor },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Chart 3: Average Time Spent in Each Pipeline Stage (Horizontal Bar Chart)
        const c3 = document.getElementById('chart-stage-durations');
        if (c3 && data3) {
            if (window.chart3Instance) { window.chart3Instance.destroy(); }
            window.chart3Instance = new Chart(c3.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data3.labels || [],
                    datasets: [{
                        label: 'Average Days',
                        data: data3.data || [],
                        backgroundColor: '#8b5cf6',
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                            titleColor: isDark ? '#f8fafc' : '#0f172a',
                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                            borderColor: isDark ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return ` Avg. ${context.raw} days in stage`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 1, color: textColor },
                            grid: { color: gridColor },
                            title: { display: true, text: 'Average Days Spent', color: textColor }
                        },
                        y: {
                            ticks: { color: textColor },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }

    initDashboardCharts();
    document.addEventListener('livewire:navigated', initDashboardCharts);
    window.addEventListener('resize', initDashboardCharts);
</script>
@endscript
</div>
