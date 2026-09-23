<div class="space-y-6">
    {{-- Header / Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-200 dark:border-slate-800">
        <div class="flex items-start gap-4">
            <a href="{{ route('applications') }}" wire:navigate
               class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 hover:border-gray-300 transition-all shadow-sm shrink-0">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100 tracking-tight">{{ $application->job_title }}</h1>
                    @php
                        $statusEnum = $application->status instanceof \App\Enums\ApplicationStatus
                            ? $application->status
                            : \App\Enums\ApplicationStatus::tryFrom($application->status);
                        $color = $statusEnum ? $statusEnum->color() : 'gray';
                        $label = $statusEnum ? $statusEnum->label() : ucfirst($application->status);
                        
                        $badgeClasses = match($color) {
                            'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            'yellow' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            'purple' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                            'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'red' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                            'slate' => 'bg-slate-50 text-slate-700 ring-slate-600/20',
                            default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $badgeClasses }}">
                        {{ $label }}
                    </span>
                </div>
                <p class="text-base text-gray-600 dark:text-slate-300 font-medium mt-1">
                    at <span class="text-gray-900 dark:text-slate-100 font-semibold">{{ $application->company->name ?? 'Unknown Company' }}</span>
                    @if($application->company->industry)
                        <span class="text-gray-400 dark:text-slate-500 font-normal"> • {{ $application->company->industry }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                wire:click="openEdit"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <x-heroicon-o-pencil-square class="w-4 h-4" />
                Edit Application
            </button>
        </div>
    </div>

    {{-- Grid Layout: 2/3 Main, 1/3 Sidebar --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Column (2/3) --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Details Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 shadow-sm p-6 space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-slate-100 flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-indigo-500" />
                    Application Overview
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 pt-2 p-4 rounded-xl bg-gray-50 dark:bg-slate-900/60 border border-gray-100 dark:border-slate-700">
                    {{-- Date Applied --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Date Applied</span>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $application->date_applied ? $application->date_applied->format('M j, Y') : 'Not specified' }}
                        </p>
                    </div>

                    {{-- Source --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Source</span>
                        @if($application->source)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($application->source) }}
                            </span>
                        @else
                            <p class="text-sm text-gray-400 italic">—</p>
                        @endif
                    </div>

                    {{-- Next Action Date --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Next Action</span>
                        @if($application->next_action_date)
                            @php
                                $today = now()->startOfDay();
                                $actionDate = $application->next_action_date->startOfDay();
                                $diff = intval(round($today->diffInDays($actionDate, false)));
                            @endphp
                            @if($diff < 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                    Overdue ({{ $application->next_action_date->format('M j') }})
                                </span>
                            @elseif($diff === 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                    Due Today
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                    {{ $application->next_action_date->format('M j, Y') }}
                                </span>
                            @endif
                        @else
                            <p class="text-sm text-gray-400 italic">No action scheduled</p>
                        @endif
                    </div>

                    {{-- Salary Range --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Salary Range</span>
                        <p class="text-sm font-medium text-gray-900">
                            @if($application->salary_min && $application->salary_max)
                                ${{ number_format($application->salary_min) }} — ${{ number_format($application->salary_max) }}
                            @elseif($application->salary_min)
                                From ${{ number_format($application->salary_min) }}
                            @elseif($application->salary_max)
                                Up to ${{ number_format($application->salary_max) }}
                            @else
                                <span class="text-gray-400 italic">Not specified</span>
                            @endif
                        </p>
                    </div>

                    {{-- Excitement Rating --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Excitement Rating</span>
                        @if($application->excitement_rating)
                            <div class="flex items-center gap-1 text-amber-400">
                                @for($i = 1; $i <= $application->excitement_rating; $i++)
                                    <x-heroicon-s-star class="w-4 h-4 text-amber-400" />
                                @endfor
                                <span class="text-xs font-semibold text-gray-600 ml-1">({{ $application->excitement_rating }}/5)</span>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Not rated</p>
                        @endif
                    </div>

                    {{-- Job Posting URL --}}
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Job Posting</span>
                        @if($application->job_posting_url)
                            <a href="{{ $application->job_posting_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                                <span>View Posting</span>
                                <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                            </a>
                        @else
                            <p class="text-sm text-gray-400 italic">No link provided</p>
                        @endif
                    </div>
                </div>

                {{-- Notes Section --}}
                @if($application->notes)
                    <div class="pt-4 border-t border-gray-100">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">Notes</span>
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/60 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">
                            {{ $application->notes }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Activity Timeline --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5 text-indigo-500" />
                        Activity Timeline
                    </h2>
                    <span class="text-xs text-gray-400 font-medium">{{ $application->activities->count() }} events</span>
                </div>

                {{-- Manual Note Entry Form (Part B1) --}}
                <form wire:submit="addActivityNote" class="flex gap-2">
                    <input
                        type="text"
                        wire:model="newActivityNote"
                        placeholder="Add a note (e.g. Sent follow-up email, Recruiter replied)..."
                        class="flex-1 text-xs rounded-xl border border-gray-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    >
                    <button
                        type="submit"
                        class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition-all shrink-0 flex items-center gap-1.5"
                    >
                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                        Add Note
                    </button>
                </form>
                @error('newActivityNote')
                    <p class="text-xs text-rose-600 -mt-4">{{ $message }}</p>
                @enderror

                @if($application->activities->isEmpty())
                    <div class="text-center py-8 text-gray-400 text-sm italic">
                        No activity records found yet.
                    </div>
                @else
                    {{-- Timeline Connecting Line (Part B3) --}}
                    <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200">
                        @foreach($application->activities as $activity)
                            @php
                                $isManualNote = is_null($activity->from_status) && is_null($activity->to_status) && !is_null($activity->notes);
                                
                                $toEnum = $activity->to_status ? \App\Enums\ApplicationStatus::tryFrom($activity->to_status) : null;
                                $fromEnum = $activity->from_status ? \App\Enums\ApplicationStatus::tryFrom($activity->from_status) : null;
                                
                                $dotColor = match($toEnum?->color()) {
                                    'blue' => 'bg-blue-500 ring-blue-100',
                                    'yellow' => 'bg-amber-500 ring-amber-100',
                                    'purple' => 'bg-purple-500 ring-purple-100',
                                    'green' => 'bg-emerald-500 ring-emerald-100',
                                    'emerald' => 'bg-emerald-500 ring-emerald-100',
                                    'red' => 'bg-rose-500 ring-rose-100',
                                    default => 'bg-indigo-500 ring-indigo-100',
                                };
                            @endphp

                            <div class="relative group">
                                @if($isManualNote)
                                    {{-- Manual Note Dot & Icon --}}
                                    <div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full border-2 border-white bg-gray-400 ring-4 ring-gray-100 flex items-center justify-center text-white shadow-sm">
                                        <x-heroicon-o-chat-bubble-left class="w-3 h-3 text-white" />
                                    </div>

                                    {{-- Manual Note Block --}}
                                    <div class="bg-gray-50 rounded-lg p-3.5 border border-gray-100 space-y-1">
                                        <p class="text-xs text-gray-800 font-medium leading-relaxed whitespace-pre-wrap">{{ $activity->notes }}</p>
                                        <time class="text-[11px] text-gray-400 font-medium block">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </time>
                                    </div>
                                @else
                                    {{-- Status Change Dot --}}
                                    <div class="absolute -left-6 top-1.5 w-5 h-5 rounded-full border-2 border-white {{ $dotColor }} ring-4 shadow-sm"></div>

                                    <div>
                                        <div class="flex items-center justify-between gap-4">
                                            <h3 class="text-sm font-semibold text-gray-900">
                                                @if($activity->from_status)
                                                    Moved from 
                                                    <span class="font-bold text-gray-700">{{ $fromEnum ? $fromEnum->label() : ucfirst($activity->from_status) }}</span>
                                                    to
                                                    <span class="font-bold text-indigo-600">{{ $toEnum ? $toEnum->label() : ucfirst($activity->to_status) }}</span>
                                                @else
                                                    Application created with status <span class="font-bold text-indigo-600">{{ $toEnum ? $toEnum->label() : ucfirst($activity->to_status) }}</span>
                                                @endif
                                            </h3>
                                            <time class="text-xs text-gray-400 font-medium shrink-0">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </time>
                                        </div>

                                        @if($activity->notes)
                                            <p class="text-xs text-gray-600 mt-1 italic">
                                                "{{ $activity->notes }}"
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Column (1/3) --}}
        <div class="space-y-6">
            {{-- Interviews Card (Part A1 & A2) --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <x-heroicon-o-calendar-days class="w-5 h-5 text-indigo-500" />
                        Interviews
                    </h3>
                    <button
                        type="button"
                        wire:click="openCreateInterview"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition-colors"
                    >
                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                        Add Interview
                    </button>
                </div>

                @php
                    $allInterviews = $application->interviews;
                    
                    $upcomingInterviews = $allInterviews->filter(function($i) {
                        return $i->scheduled_at && $i->scheduled_at->isFuture() && strtolower($i->outcome ?? 'pending') === 'pending';
                    })->sortBy('scheduled_at');

                    $pastInterviews = $allInterviews->reject(function($i) {
                        return $i->scheduled_at && $i->scheduled_at->isFuture() && strtolower($i->outcome ?? 'pending') === 'pending';
                    })->sortByDesc('scheduled_at');
                @endphp

                @if($allInterviews->isEmpty())
                    <div class="text-center py-6 px-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-xs text-gray-500 font-medium">No interviews scheduled yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Click "Add Interview" to schedule your first round.</p>
                    </div>
                @else
                    <div class="space-y-5">
                        {{-- Upcoming Interviews Section --}}
                        @if($upcomingInterviews->isNotEmpty())
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 block mb-2.5">
                                    Upcoming Interviews ({{ $upcomingInterviews->count() }})
                                </span>
                                <div class="space-y-2.5">
                                    @foreach($upcomingInterviews as $interview)
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

                                            $isSoon = $interview->scheduled_at->isToday() || $interview->scheduled_at->isTomorrow();
                                        @endphp

                                        <div class="p-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-white hover:shadow-sm transition-all space-y-2">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $roundBadge }}">
                                                        {{ $roundLabel }}
                                                    </span>
                                                    @if($interview->format)
                                                        <span class="px-2 py-0.5 text-[11px] font-medium rounded bg-gray-100 text-gray-600 border border-gray-200">
                                                            {{ ucfirst($interview->format) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Edit / Delete buttons --}}
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <button
                                                        type="button"
                                                        wire:click="openEditInterview('{{ $interview->id }}')"
                                                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                                                        title="Edit Interview"
                                                    >
                                                        <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                                    </button>
                                                    <button
                                                        type="button"
                                                        wire:click="confirmDeleteInterview('{{ $interview->id }}')"
                                                        class="p-1 rounded text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                        title="Delete Interview"
                                                    >
                                                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between text-xs text-gray-600">
                                                <span>{{ $interview->scheduled_at->format('M j, Y \a\t g:i A') }}</span>

                                                @if($isSoon)
                                                    <span class="inline-flex items-center gap-1 text-[11px] text-amber-600 font-semibold">
                                                        <span class="relative flex h-2 w-2">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                                        </span>
                                                        {{ $interview->scheduled_at->isToday() ? 'Today' : 'Tomorrow' }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($interview->prep_notes)
                                                <p class="text-xs text-gray-500 italic bg-white p-2 rounded border border-gray-100">
                                                    "{{ $interview->prep_notes }}"
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Past Interviews Section --}}
                        @if($pastInterviews->isNotEmpty())
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-2.5">
                                    Past Interviews ({{ $pastInterviews->count() }})
                                </span>
                                <div class="space-y-2">
                                    @foreach($pastInterviews as $interview)
                                        @php
                                            $outcomeBadge = match(strtolower($interview->outcome ?? 'pending')) {
                                                'passed' => 'bg-emerald-100 text-emerald-800',
                                                'failed' => 'bg-rose-100 text-rose-800',
                                                'cancelled' => 'bg-gray-100 text-gray-600',
                                                default => 'bg-amber-100 text-amber-800',
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

                                        <div class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/70 text-xs space-y-1.5 opacity-80 hover:opacity-100 transition-opacity">
                                            <div class="flex items-center justify-between">
                                                <span class="font-semibold text-gray-700">{{ $roundLabel }}</span>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $outcomeBadge }}">
                                                        {{ ucfirst($interview->outcome ?? 'Pending') }}
                                                    </span>
                                                    <button
                                                        type="button"
                                                        wire:click="openEditInterview('{{ $interview->id }}')"
                                                        class="p-0.5 text-gray-400 hover:text-gray-700"
                                                    >
                                                        <x-heroicon-o-pencil class="w-3 h-3" />
                                                    </button>
                                                    <button
                                                        type="button"
                                                        wire:click="confirmDeleteInterview('{{ $interview->id }}')"
                                                        class="p-0.5 text-gray-400 hover:text-rose-600"
                                                    >
                                                        <x-heroicon-o-trash class="w-3 h-3" />
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-[11px] text-gray-400">
                                                {{ $interview->scheduled_at ? $interview->scheduled_at->format('M j, Y') : 'No date' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Linked Documents Card (Part B1) --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <x-heroicon-o-document-text class="w-5 h-5 text-indigo-500" />
                        Linked Documents
                    </h3>
                    <button
                        type="button"
                        wire:click="openAttachDocument"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition-colors"
                    >
                        <x-heroicon-o-paper-clip class="w-3.5 h-3.5" />
                        Attach Document
                    </button>
                </div>

                @if($application->documents->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($application->documents as $doc)
                            @php
                                $isResume = $doc->doc_type === 'resume';
                            @endphp
                            <div class="p-3 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg {{ $isResume ? 'bg-indigo-100 text-indigo-700' : 'bg-teal-100 text-teal-700' }} flex items-center justify-center shrink-0">
                                        @if($isResume)
                                            <x-heroicon-o-document-text class="w-4 h-4" />
                                        @else
                                            <x-heroicon-o-document class="w-4 h-4" />
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate" title="{{ $doc->label }}">{{ $doc->label }}</p>
                                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">
                                            {{ $isResume ? 'Resume' : 'Cover Letter' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <a
                                        href="{{ route('documents.download', $doc->id) }}"
                                        class="p-1 rounded text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                        title="Download"
                                    >
                                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="unlinkDocument('{{ $doc->id }}')"
                                        class="p-1 rounded text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                        title="Unlink document from application"
                                    >
                                        <x-heroicon-o-x-mark class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 px-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-xs text-gray-500 font-medium">No documents linked yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Attach a resume or cover letter to track metrics.</p>
                    </div>
                @endif
            </div>

            {{-- Company Contacts Card --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <x-heroicon-o-users class="w-5 h-5 text-indigo-500" />
                        Company Contacts
                    </h3>
                    <span class="text-xs text-gray-400 font-medium">{{ $contacts->count() }} contacts</span>
                </div>

                @if($contacts->isEmpty())
                    <div class="text-center py-6 px-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-xs text-gray-500 font-medium">No contacts found at {{ $application->company->name ?? 'this company' }}.</p>
                        <a href="{{ route('contacts') }}" wire:navigate class="text-xs font-semibold text-indigo-600 hover:underline mt-1 inline-block">
                            + Add Contact
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($contacts as $contact)
                            <div class="p-3 rounded-xl border border-gray-200/70 bg-gray-50/70 space-y-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900">{{ $contact->name }}</p>
                                    @if($contact->role)
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700">
                                            {{ ucfirst($contact->role) }}
                                        </span>
                                    @endif
                                </div>
                                @if($contact->email)
                                    <a href="mailto:{{ $contact->email }}" class="text-xs text-gray-500 hover:text-indigo-600 hover:underline flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $contact->email }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Application Edit Slide-over --}}
    <x-slide-over
        id="edit-application-slide-over"
        title="Edit Application"
        wire:model="showSlideOver"
    >
        <form wire:submit="save" class="space-y-5">
            {{-- Job Title --}}
            <div>
                <label for="edit-job-title" class="block text-sm font-medium text-gray-700 mb-1">Job Title *</label>
                <input
                    type="text"
                    id="edit-job-title"
                    wire:model="jobTitle"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    required
                >
                @error('jobTitle') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Company --}}
            <div>
                <label for="edit-company-id" class="block text-sm font-medium text-gray-700 mb-1">Company *</label>
                <select
                    id="edit-company-id"
                    wire:model="companyId"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                    required
                >
                    <option value="">Select a company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('companyId') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="edit-status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select
                    id="edit-status"
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

            {{-- Date Applied & Source inline grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit-date-applied" class="block text-sm font-medium text-gray-700 mb-1">Date Applied</label>
                    <input
                        type="date"
                        id="edit-date-applied"
                        wire:model="dateApplied"
                        class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    >
                    @error('dateApplied') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="edit-source" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                    <select
                        id="edit-source"
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
            <div>
                <label for="edit-job-posting-url" class="block text-sm font-medium text-gray-700 mb-1">Job Posting URL</label>
                <input
                    type="url"
                    id="edit-job-posting-url"
                    wire:model="jobPostingUrl"
                    placeholder="https://..."
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                >
                @error('jobPostingUrl') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Salary Range (Min / Max) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Salary Range ($)</label>
                <div class="grid grid-cols-2 gap-4">
                    <input
                        type="number"
                        id="edit-salary-min"
                        wire:model="salaryMin"
                        placeholder="Min (e.g. 80000)"
                        class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    >
                    <input
                        type="number"
                        id="edit-salary-max"
                        wire:model="salaryMax"
                        placeholder="Max (e.g. 120000)"
                        class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    >
                </div>
                @error('salaryMin') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                @error('salaryMax') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Excitement Rating (Stars) --}}
            <div>
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
            <div>
                <label for="edit-next-action-date" class="block text-sm font-medium text-gray-700 mb-1">Next Action Date</label>
                <input
                    type="date"
                    id="edit-next-action-date"
                    wire:model="nextActionDate"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                >
                @error('nextActionDate') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="edit-notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea
                    id="edit-notes"
                    wire:model="notes"
                    rows="4"
                    placeholder="Add interview thoughts, questions asked, contact info..."
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                ></textarea>
                @error('notes') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Linked Documents (Part B2) --}}
            <div>
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

            {{-- Action buttons --}}
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="$wire.set('showSlideOver', false)"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-all"
                >
                    Update Application
                </button>
            </div>
        </form>
    </x-slide-over>

    {{-- Interview Slide-over (Part A1 & A3) --}}
    <x-slide-over
        id="interview-slide-over"
        :title="$editingInterviewId ? 'Edit Interview' : 'Schedule Interview'"
        wire:model="showInterviewSlideOver"
    >
        <form wire:submit="saveInterview" class="space-y-5">
            {{-- Round Type --}}
            <div>
                <label for="interview-round-type" class="block text-sm font-medium text-gray-700 mb-1">Round Type *</label>
                <select
                    id="interview-round-type"
                    wire:model="interviewRoundType"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                    required
                >
                    <option value="phone_screen">Phone Screen</option>
                    <option value="technical">Technical</option>
                    <option value="behavioral">Behavioral</option>
                    <option value="onsite">On-site</option>
                    <option value="final">Final</option>
                    <option value="other">Other</option>
                </select>
                @error('interviewRoundType') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Date & Time --}}
            <div>
                <label for="interview-scheduled-at" class="block text-sm font-medium text-gray-700 mb-1">Date & Time *</label>
                <input
                    type="datetime-local"
                    id="interview-scheduled-at"
                    wire:model="interviewScheduledAt"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    required
                >
                @error('interviewScheduledAt') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Format --}}
            <div>
                <label for="interview-format" class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                <select
                    id="interview-format"
                    wire:model="interviewFormat"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                >
                    <option value="">Select format</option>
                    <option value="remote">Remote</option>
                    <option value="onsite">On-site</option>
                    <option value="hybrid">Hybrid</option>
                </select>
                @error('interviewFormat') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Outcome --}}
            <div>
                <label for="interview-outcome" class="block text-sm font-medium text-gray-700 mb-1">Outcome *</label>
                <select
                    id="interview-outcome"
                    wire:model="interviewOutcome"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                    required
                >
                    <option value="pending">Pending</option>
                    <option value="passed">Passed</option>
                    <option value="failed">Failed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                @error('interviewOutcome') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Prep Notes --}}
            <div>
                <label for="interview-prep-notes" class="block text-sm font-medium text-gray-700 mb-1">Prep Notes</label>
                <textarea
                    id="interview-prep-notes"
                    wire:model="interviewPrepNotes"
                    rows="4"
                    placeholder="Questions to ask, people to meet, topics to review..."
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                ></textarea>
                @error('interviewPrepNotes') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Action buttons --}}
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="$wire.set('showInterviewSlideOver', false)"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-all"
                >
                    {{ $editingInterviewId ? 'Update Interview' : 'Save Interview' }}
                </button>
            </div>
        </form>
    </x-slide-over>

    {{-- Delete Interview Confirmation Modal (Part A4) --}}
    <x-confirm-modal
        wire:model="showDeleteInterviewModal"
        title="Delete Interview"
        message="Are you sure you want to delete this {{ ucfirst($this->deletingInterview?->round_type ?? 'interview') }} interview? This action cannot be undone."
        confirmText="Delete Interview"
        confirmAction="deleteInterview"
    />

    {{-- Attach Document Slide-over (Part B1) --}}
    <x-slide-over
        id="attach-document-slide-over"
        title="Attach Document"
        wire:model="showAttachDocumentModal"
    >
        @php
            $linkedIds = $application->documents->pluck('id')->toArray();
            $availableDocs = $allUserDocuments->reject(fn($d) => in_array($d->id, $linkedIds));
        @endphp

        <div class="space-y-4">
            <p class="text-xs text-gray-500">
                Select a document from your library to attach to <span class="font-bold text-gray-900">{{ $application->job_title }}</span>:
            </p>

            @if($availableDocs->isEmpty())
                <div class="text-center py-8 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-medium text-gray-600">No unattached documents available.</p>
                    <a href="{{ route('documents', ['upload' => 1]) }}" wire:navigate class="text-xs font-semibold text-indigo-600 hover:underline mt-2 inline-block">
                        + Upload a new document
                    </a>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($availableDocs as $doc)
                        @php
                            $isResume = $doc->doc_type === 'resume';
                        @endphp
                        <button
                            type="button"
                            wire:click="attachDocument('{{ $doc->id }}')"
                            class="w-full text-left p-3 rounded-xl border border-gray-200 bg-white hover:bg-indigo-50/50 hover:border-indigo-300 transition-all flex items-center justify-between group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $isResume ? 'bg-indigo-50 text-indigo-600' : 'bg-teal-50 text-teal-600' }} flex items-center justify-center shrink-0">
                                    @if($isResume)
                                        <x-heroicon-o-document-text class="w-4 h-4" />
                                    @else
                                        <x-heroicon-o-document class="w-4 h-4" />
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 group-hover:text-indigo-600 transition-colors truncate">{{ $doc->label }}</p>
                                    <p class="text-[11px] text-gray-400 truncate">{{ $doc->original_filename }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-indigo-600 group-hover:translate-x-0.5 transition-transform shrink-0">+ Attach</span>
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end">
                <button
                    type="button"
                    @click="$wire.set('showAttachDocumentModal', false)"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </button>
            </div>
        </div>
    </x-slide-over>
</div>
