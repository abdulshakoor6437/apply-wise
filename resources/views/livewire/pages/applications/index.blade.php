<div class="space-y-6 flex flex-col h-full">

    {{-- Board Header Controls --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-200 dark:border-slate-800">
        {{-- Title & View Mode Toggle --}}
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100 tracking-tight">Applications</h1>

            {{-- Toggle switch: Board vs List --}}
            <div class="flex items-center p-1 bg-gray-200/80 dark:bg-slate-800 rounded-xl">
                <button
                    type="button"
                    wire:click="$set('viewMode', 'board')"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 {{ $viewMode === 'board' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}"
                    id="view-mode-board-btn"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    Board
                </button>
                <button
                    type="button"
                    wire:click="$set('viewMode', 'list')"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 {{ $viewMode === 'list' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}"
                    id="view-mode-list-btn"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    List
                </button>
            </div>
        </div>

        {{-- Add Application Button --}}
        <div class="flex items-center gap-3">
            <button
                type="button"
                wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                id="add-application-btn"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Application
            </button>
        </div>
    </div>

    {{-- Filter Chips & Search Bar --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-white dark:bg-slate-800/80 p-3 rounded-xl border border-gray-100 dark:border-slate-700/80 shadow-sm">
        {{-- Search input --}}
        <div class="flex-1 relative">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                aria-label="Search job title or company name"
                placeholder="Search job title or company name…"
                class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                id="applications-search-input"
            >
        </div>

        {{-- Company filter dropdown --}}
        <div class="w-full sm:w-48">
            <select
                wire:model.live="filterCompany"
                aria-label="Filter applications by company"
                class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                id="filter-company-select"
            >
                <option value="">All Companies</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Source filter dropdown --}}
        <div class="w-full sm:w-44">
            <select
                wire:model.live="filterSource"
                aria-label="Filter applications by source"
                class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                id="filter-source-select"
            >
                <option value="">All Sources</option>
                <option value="LinkedIn">LinkedIn</option>
                <option value="Indeed">Indeed</option>
                <option value="Company Website">Company Website</option>
                <option value="Referral">Referral</option>
                <option value="Other">Other</option>
            </select>
        </div>
    </div>

    {{-- ==================== VIEW 1: KANBAN BOARD ==================== --}}
    @if($viewMode === 'board')
        <div
            wire:ignore.self
            x-data="{
                initSortable() {
                    this.$el.querySelectorAll('.kanban-column-body').forEach(el => {
                        if (el._sortable) return;
                        el._sortable = new Sortable(el, {
                            group: 'kanban',
                            animation: 150,
                            ghostClass: 'opacity-40',
                            handle: '.card-drag-handle',
                            onEnd: (evt) => {
                                const cardId = evt.item.dataset.id;
                                const newStatus = evt.to.dataset.status;
                                const newIndex = evt.newIndex;
                                @this.call('updateCardPosition', cardId, newStatus, newIndex);
                            }
                        });
                    });
                }
            }"
            x-init="initSortable()"
            class="flex-1 overflow-x-auto pb-6"
        >
            <div class="flex gap-5 min-w-max items-start min-h-[calc(100vh-16rem)]">
                @foreach($stages as $stage)
                    @php
                        $stageApps = $boardData[$stage->value] ?? collect();
                        $colorClass = match($stage->color()) {
                            'blue' => 'border-t-blue-500',
                            'yellow' => 'border-t-amber-500',
                            'purple' => 'border-t-purple-500',
                            'green' => 'border-t-emerald-500',
                            'emerald' => 'border-t-emerald-500',
                            'red' => 'border-t-rose-500',
                            default => 'border-t-gray-400',
                        };
                        $badgeBg = match($stage->color()) {
                            'blue' => 'bg-blue-100 text-blue-800',
                            'yellow' => 'bg-amber-100 text-amber-800',
                            'purple' => 'bg-purple-100 text-purple-800',
                            'green' => 'bg-emerald-100 text-emerald-800',
                            'emerald' => 'bg-emerald-100 text-emerald-800',
                            'red' => 'bg-rose-100 text-rose-800',
                            default => 'bg-gray-200 text-gray-800',
                        };
                    @endphp

                    {{-- Kanban Column Container --}}
                    <div class="w-80 shrink-0 flex flex-col rounded-xl bg-gray-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700/80 shadow-sm overflow-hidden border-t-4 {{ $colorClass }}">

                        {{-- Column Header --}}
                        <div class="flex items-center justify-between px-4 py-3.5 bg-white dark:bg-slate-800 border-b border-gray-200/70 dark:border-slate-700">
                            <span class="text-sm font-semibold text-gray-700 dark:text-slate-200">{{ $stage->label() }}</span>
                            <span class="bg-white dark:bg-slate-700 rounded-full px-2 py-0.5 text-xs font-medium text-gray-700 dark:text-slate-200 shadow-sm border border-gray-100 dark:border-slate-600">
                                {{ $stageApps->count() }}
                            </span>
                        </div>

                        {{-- Column Body (Drop Target) --}}
                        <div
                            data-status="{{ $stage->value }}"
                            class="kanban-column-body flex-1 p-3 gap-3 min-h-[500px] overflow-y-auto max-h-[calc(100vh-20rem)] flex flex-col"
                        >
                            {{-- Empty drop zone shown only when column has no cards --}}
                            @if($stageApps->isEmpty())
                                <div class="flex-1 flex items-center justify-center min-h-[200px]">
                                    <div class="w-full h-full min-h-[180px] flex items-center justify-center border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-lg">
                                        <span class="text-xs text-gray-400 dark:text-slate-500">Drag applications here</span>
                                    </div>
                                </div>
                            @endif
                            @foreach($stageApps as $card)
                                @php
                                    $isHighExcitement = $card->excitement_rating && $card->excitement_rating >= 4;
                                    $appliedDaysAgo = null;
                                    if ($card->date_applied) {
                                        $days = intval(round($card->date_applied->startOfDay()->diffInDays(now()->startOfDay())));
                                        $appliedDaysAgo = ($days === 0 || $card->date_applied->isToday()) ? 'Applied today' : "Applied {$days}d ago";
                                    }
                                @endphp

                                {{-- Application Card --}}
                                <div
                                    data-id="{{ $card->id }}"
                                    class="group bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-100 dark:border-slate-700 p-4 hover:shadow-md dark:hover:bg-slate-750 transition-all duration-200 cursor-grab active:cursor-grabbing relative {{ $isHighExcitement ? 'border-l-[3px] border-l-indigo-400' : '' }}"
                                >
                                    {{-- Drag Handle --}}
                                    <div class="card-drag-handle absolute top-3 right-3 p-1 rounded hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-300 dark:text-slate-600 hover:text-gray-500 dark:hover:text-slate-300 cursor-grab active:cursor-grabbing transition-colors">
                                        <x-heroicon-o-ellipsis-vertical class="w-4 h-4" />
                                    </div>

                                    {{-- Card Content (clickable to open detail page) --}}
                                    <a href="{{ route('applications.show', $card->id) }}" wire:navigate class="block pr-6">
                                        {{-- Row 1: Company Name --}}
                                        <p class="text-sm font-semibold text-gray-900 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors leading-tight">
                                            {{ $card->company->name ?? 'Unknown Company' }}
                                        </p>

                                        {{-- Row 2: Job Title --}}
                                        <p class="text-sm text-gray-600 dark:text-slate-300 mt-0.5">
                                            {{ $card->job_title }}
                                        </p>

                                        {{-- Row 3: Applied date --}}
                                        @if($appliedDaysAgo)
                                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">
                                                {{ $appliedDaysAgo }}
                                            </p>
                                        @endif
                                    </a>

                                    {{-- Row 4: Card Footer Badges --}}
                                    <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between gap-2 flex-wrap text-xs">
                                        {{-- Excitement Stars --}}
                                        @if($card->excitement_rating)
                                            <div class="flex items-center text-amber-400">
                                                @for($i = 1; $i <= $card->excitement_rating; $i++)
                                                    <x-heroicon-s-star class="w-3.5 h-3.5 text-amber-400" />
                                                @endfor
                                            </div>
                                        @else
                                            <div></div>
                                        @endif

                                        {{-- Next Action Badge --}}
                                        @if($card->next_action_date)
                                            @php
                                                $today = now()->startOfDay();
                                                $actionDate = $card->next_action_date->startOfDay();
                                                $diff = intval(round($today->diffInDays($actionDate, false)));
                                            @endphp
                                            @if($diff < 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600">
                                                    Overdue
                                                </span>
                                            @elseif($diff === 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-600">
                                                    Due today
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                                                    {{ $card->next_action_date->format('M j') }}
                                                </span>
                                            @endif
                                        @endif

                                        {{-- Source Badge --}}
                                        @if($card->source)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                {{ $card->source }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Row 5: Upcoming Interview Badge (Part A5) --}}
                                    @php
                                        $upcomingCardInterview = $card->interviews
                                            ->filter(fn($i) => $i->scheduled_at && $i->scheduled_at->isFuture() && strtolower($i->outcome ?? 'pending') === 'pending')
                                            ->sortBy('scheduled_at')
                                            ->first();
                                    @endphp
                                    @if($upcomingCardInterview)
                                        <div class="mt-2.5 pt-2 border-t border-gray-100/80 flex items-center">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                                <x-heroicon-o-calendar class="w-3.5 h-3.5 text-purple-600" />
                                                Interview {{ $upcomingCardInterview->scheduled_at->format('M j') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ==================== VIEW 2: LIST / TABLE VIEW ==================== --}}
    @if($viewMode === 'list')
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex-1">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Company</th>
                            <th class="px-6 py-4">Job Title</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Applied</th>
                            <th class="px-6 py-4">Source</th>
                            <th class="px-6 py-4">Salary Range</th>
                            <th class="px-6 py-4">Next Action</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($applications as $app)
                            @php
                                $statusEnum = $app->status instanceof \App\Enums\ApplicationStatus
                                    ? $app->status
                                    : \App\Enums\ApplicationStatus::tryFrom($app->status);
                                $color = $statusEnum ? $statusEnum->color() : 'gray';
                                $label = $statusEnum ? $statusEnum->label() : ucfirst($app->status);

                                $badgeClasses = match($color) {
                                    'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                    'yellow' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                    'purple' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                                    'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    'red' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                                    default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                {{-- Company --}}
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    <a href="{{ route('applications.show', $app->id) }}" wire:navigate class="hover:text-indigo-600 transition-colors">
                                        {{ $app->company->name ?? 'Unknown Company' }}
                                    </a>
                                </td>

                                {{-- Job Title --}}
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    <a href="{{ route('applications.show', $app->id) }}" wire:navigate class="hover:text-indigo-600 transition-colors">
                                        {{ $app->job_title }}
                                    </a>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $badgeClasses }}">
                                        {{ $label }}
                                    </span>
                                </td>

                                {{-- Applied --}}
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $app->date_applied ? $app->date_applied->format('M j, Y') : '—' }}
                                </td>

                                {{-- Source --}}
                                <td class="px-6 py-4 text-xs">
                                    @if($app->source)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $app->source }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>

                                {{-- Salary Range --}}
                                <td class="px-6 py-4 text-xs text-gray-700 font-medium whitespace-nowrap">
                                    @if($app->salary_min && $app->salary_max)
                                        ${{ number_format($app->salary_min/1000) }}k — ${{ number_format($app->salary_max/1000) }}k
                                    @elseif($app->salary_min)
                                        From ${{ number_format($app->salary_min/1000) }}k
                                    @elseif($app->salary_max)
                                        Up to ${{ number_format($app->salary_max/1000) }}k
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>

                                {{-- Next Action --}}
                                <td class="px-6 py-4 text-xs whitespace-nowrap">
                                    @if($app->next_action_date)
                                        @php
                                            $today = now()->startOfDay();
                                            $actionDate = $app->next_action_date->startOfDay();
                                            $diff = intval(round($today->diffInDays($actionDate, false)));
                                        @endphp
                                        @if($diff < 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                                Overdue ({{ $app->next_action_date->format('M j') }})
                                            </span>
                                        @elseif($diff === 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                Due today
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                                {{ $app->next_action_date->format('M j') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('applications.show', $app->id) }}"
                                            wire:navigate
                                            class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="View Details"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <button
                                            type="button"
                                            wire:click="openEdit('{{ $app->id }}')"
                                            class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="Edit"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="confirmDelete('{{ $app->id }}')"
                                            class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                            title="Delete"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                        </svg>
                                        <p class="text-base font-semibold text-gray-900">No applications found</p>
                                        <p class="text-xs text-gray-500 mt-1">Try adjusting your filters or click "+ Add Application".</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Slide-over (Create & Edit) --}}
    <x-slide-over
        id="application-slide-over"
        title="{{ $editingId ? 'Edit Application' : 'Create Application' }}"
        wire:model="showSlideOver"
    >
        <form wire:submit="save" class="space-y-5">
            {{-- Job Title --}}
            <div>
                <label for="job-title-input" class="block text-sm font-medium text-gray-700 mb-1">Job Title *</label>
                <input
                    type="text"
                    id="job-title-input"
                    wire:model="jobTitle"
                    placeholder="e.g. Senior Frontend Engineer"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    required
                >
                @error('jobTitle') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Company --}}
            <div>
                <label for="company-id-input" class="block text-sm font-medium text-gray-700 mb-1">Company *</label>
                @if($companies->isEmpty())
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 flex items-center justify-between">
                        <span>No companies yet — Add one first</span>
                        <a href="{{ route('companies', ['create' => 1]) }}" wire:navigate class="font-semibold underline hover:text-amber-900">Add Company</a>
                    </div>
                @else
                    <select
                        id="company-id-input"
                        wire:model="companyId"
                        class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                        required
                    >
                        <option value="">Select a company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('companyId') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status-input" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select
                    id="status-input"
                    wire:model.live="status"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                    required
                >
                    @foreach(\App\Enums\ApplicationStatus::kanbanStages() as $stage)
                        <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                    @endforeach
                </select>
                @error('status') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Date Applied & Source inline --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="date-applied-input" class="block text-sm font-medium text-gray-700 mb-1">Date Applied</label>
                    <input
                        type="date"
                        id="date-applied-input"
                        wire:model="dateApplied"
                        class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    >
                    @error('dateApplied') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="source-input" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                    <select
                        id="source-input"
                        wire:model="source"
                        class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                    >
                        <option value="">Select source</option>
                        <option value="LinkedIn">LinkedIn</option>
                        <option value="Indeed">Indeed</option>
                        <option value="Company Website">Company Website</option>
                        <option value="Referral">Referral</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('source') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Job Posting URL --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <label for="job-posting-url-input" class="block text-sm font-medium text-gray-700 mb-1">Job Posting URL</label>
                <input
                    type="url"
                    id="job-posting-url-input"
                    wire:model="jobPostingUrl"
                    placeholder="https://linkedin.com/jobs/view/..."
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                >
                @error('jobPostingUrl') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Salary Range (Min / Max) --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Salary Range ($)</label>
                <div class="grid grid-cols-2 gap-4">
                    <input
                        type="number"
                        id="salary-min-input"
                        wire:model="salaryMin"
                        placeholder="Min (e.g. 80000)"
                        class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    >
                    <input
                        type="number"
                        id="salary-max-input"
                        wire:model="salaryMax"
                        placeholder="Max (e.g. 120000)"
                        class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    >
                </div>
                @error('salaryMin') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                @error('salaryMax') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Excitement Rating (Alpine 5-Star Component) --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Excitement Rating</label>
                <div x-data="{ rating: @entangle('excitementRating'), hoverRating: 0 }" class="flex items-center gap-1 py-1">
                    <template x-for="star in 5" :key="star">
                        <button
                            type="button"
                            @click="rating = (rating === star ? null : star)"
                            @mouseenter="hoverRating = star"
                            @mouseleave="hoverRating = 0"
                            class="p-1 focus:outline-none transition-transform hover:scale-110"
                        >
                            <svg class="w-6 h-6" :class="(hoverRating || rating) >= star ? 'text-amber-400 fill-amber-400' : 'text-gray-300 fill-gray-200'" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    </template>
                    <span x-show="rating" class="text-xs text-gray-500 ml-2" x-text="rating + ' / 5 stars'"></span>
                </div>
            </div>

            {{-- Next Action Date --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <label for="next-action-date-input" class="block text-sm font-medium text-gray-700 mb-1">Next Action Date</label>
                <input
                    type="date"
                    id="next-action-date-input"
                    wire:model="nextActionDate"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                >
                @error('nextActionDate') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Notes --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <label for="notes-input" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea
                    id="notes-input"
                    wire:model="notes"
                    rows="4"
                    placeholder="Add interview notes, recruiter contacts, follow-up ideas..."
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                ></textarea>
                @error('notes') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Linked Documents (Part B2) --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Linked Documents</label>
                @if($allUserDocuments->isEmpty())
                    <p class="text-xs text-gray-400 italic">No documents uploaded yet. <a href="{{ route('documents', ['upload' => 1]) }}" wire:navigate class="text-indigo-600 underline">Upload a document</a> first.</p>
                @else
                    <div class="space-y-2 max-h-40 overflow-y-auto p-3 rounded-xl border border-gray-200 bg-gray-50">
                        @foreach($allUserDocuments as $doc)
                            <label class="flex items-center gap-2.5 text-xs text-gray-700 font-medium cursor-pointer hover:text-gray-900">
                                <input
                                    type="checkbox"
                                    value="{{ $doc->id }}"
                                    wire:model="documentIds"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <span class="truncate">{{ $doc->label }} ({{ $doc->doc_type === 'resume' ? 'Resume' : 'Cover Letter' }})</span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 border-t border-gray-200 space-y-2">
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-lg text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-all"
                >
                    {{ $editingId ? 'Update Application' : 'Save Application' }}
                </button>
                <button
                    type="button"
                    @click="$wire.set('showSlideOver', false)"
                    class="w-full py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-slide-over>

    {{-- Delete Confirmation Modal --}}
    <x-confirm-modal
        id="delete-application-modal"
        title="Delete Application"
        message="Delete this application for {{ $this->deletingApplication->job_title ?? '' }} at {{ $this->deletingApplication->company->name ?? '' }}? All interviews, activities, and linked documents for this application will be removed."
        confirmText="Delete Application"
        confirmAction="delete"
        wire:model="showDeleteModal"
    />
</div>
