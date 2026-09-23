<div>

    {{-- ===================== HEADER ===================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Companies</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Track the companies you're pursuing or researching.</p>
        </div>
        <button
            wire:click="openCreate"
            id="add-company-btn"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all active:scale-95"
        >
            <x-heroicon-o-plus class="w-4 h-4" />
            Add Company
        </button>
    </div>

    {{-- ===================== SEARCH ===================== --}}
    <div class="mb-4">
        <div class="relative max-w-sm">
            <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500" />
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                id="companies-search"
                aria-label="Search companies by name, industry, or location"
                placeholder="Search name, industry, location…"
                class="w-full pl-10 pr-4 py-2.5 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            >
            <div wire:loading.delay wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="w-4 h-4 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ===================== TABLE CARD ===================== --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">

        {{-- Loading skeleton --}}
        <div wire:loading.delay class="divide-y divide-gray-50">
            @for($i = 0; $i < 4; $i++)
            <div class="px-6 py-4 flex items-center gap-4 animate-pulse">
                <div class="w-8 h-8 bg-gray-100 rounded-lg shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-3.5 bg-gray-100 rounded w-1/3"></div>
                    <div class="h-3 bg-gray-50 rounded w-1/4"></div>
                </div>
                <div class="h-6 w-12 bg-gray-100 rounded-full"></div>
                <div class="h-6 w-12 bg-gray-100 rounded-full"></div>
                <div class="h-3 w-20 bg-gray-100 rounded"></div>
                <div class="flex gap-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg"></div>
                    <div class="w-8 h-8 bg-gray-100 rounded-lg"></div>
                </div>
            </div>
            @endfor
        </div>

        {{-- Real content --}}
        <div wire:loading.remove.delay>

            @if ($companies->isEmpty() && !$search)
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="flex items-center justify-center w-20 h-20 mb-5 rounded-2xl bg-indigo-50">
                        <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No companies yet</h3>
                    <p class="text-sm text-gray-500 mb-5 max-w-xs">Start by adding the companies you're applying to or researching.</p>
                    <button wire:click="openCreate" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add your first company
                    </button>
                </div>

            @elseif ($companies->isEmpty())
                {{-- No results --}}
                <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                    <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-700">No results for "{{ $search }}"</h3>
                    <p class="text-sm text-gray-400 mt-1">Try different keywords or clear the search.</p>
                </div>

            @else
                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                @php
                                    $sortIcon = function(string $col) use ($sortBy, $sortDir): string {
                                        if ($sortBy !== $col) return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 12V4m0 16l4-4m-4 4l-4-4"/>';
                                        return $sortDir === 'asc'
                                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>'
                                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>';
                                    };
                                @endphp

                                <th class="px-6 py-3 text-left">
                                    <button wire:click="sort('name')" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-colors group">
                                        Name
                                        <svg class="w-3.5 h-3.5 {{ $sortBy === 'name' ? 'text-indigo-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $sortIcon('name') !!}
                                        </svg>
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-left">
                                    <button wire:click="sort('industry')" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-colors group">
                                        Industry
                                        <svg class="w-3.5 h-3.5 {{ $sortBy === 'industry' ? 'text-indigo-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $sortIcon('industry') !!}
                                        </svg>
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-left">
                                    <button wire:click="sort('location')" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-colors group">
                                        Location
                                        <svg class="w-3.5 h-3.5 {{ $sortBy === 'location' ? 'text-indigo-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $sortIcon('location') !!}
                                        </svg>
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Apps</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Contacts</th>
                                <th class="px-6 py-3 text-left">
                                    <button wire:click="sort('created_at')" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-colors group">
                                        Added
                                        <svg class="w-3.5 h-3.5 {{ $sortBy === 'created_at' ? 'text-indigo-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $sortIcon('created_at') !!}
                                        </svg>
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($companies as $company)
                            <tr class="hover:bg-gray-50/70 transition-colors group" wire:key="company-{{ $company->id }}">
                                {{-- Name + size badge --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 font-semibold text-sm shrink-0">
                                            {{ strtoupper(substr($company->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $company->name }}</p>
                                            @if($company->size)
                                                <p class="text-xs text-gray-400">{{ $sizeOptions[$company->size] ?? $company->size }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $company->industry ?: '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $company->location ?: '—' }}</td>

                                {{-- Applications count badge --}}
                                <td class="px-6 py-4">
                                    <a href="{{ route('applications') }}?company={{ $company->id }}"
                                       class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                        {{ $company->applications_count }}
                                    </a>
                                </td>

                                {{-- Contacts count --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        {{ $company->contacts_count }}
                                    </span>
                                </td>

                                {{-- Date added --}}
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $company->created_at->format('M j, Y') }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button
                                            wire:click="openView('{{ $company->id }}')"
                                            title="View company profile"
                                            class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all"
                                            id="view-company-{{ $company->id }}-btn"
                                        >
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </button>
                                        <button
                                            wire:click="openEdit('{{ $company->id }}')"
                                            title="Edit company"
                                            class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="confirmDelete('{{ $company->id }}')"
                                            title="Delete company"
                                            class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($companies->hasPages())
                    <div class="px-6 py-4 border-t border-gray-50">
                        {{ $companies->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ===================== SLIDE-OVER: Create / Edit ===================== --}}
    <x-slide-over wire:model="showSlideOver" :title="$editingId ? 'Edit Company' : 'Add Company'">
        <form wire:submit="save" class="space-y-5" id="company-form">

            {{-- Name --}}
            <div>
                <label for="company-name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Company Name <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="name"
                    type="text"
                    id="company-name"
                    placeholder="e.g. Google, Stripe, Airbnb"
                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('name') border-red-300 bg-red-50 @enderror"
                >
                @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Industry + Location --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="company-industry" class="block text-sm font-medium text-gray-700 mb-1.5">Industry</label>
                    <input wire:model="industry" type="text" id="company-industry" placeholder="e.g. Technology, Finance"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label for="company-location" class="block text-sm font-medium text-gray-700 mb-1.5">Location</label>
                    <input wire:model="location" type="text" id="company-location" placeholder="e.g. San Francisco, CA"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
            </div>

            {{-- Company Size --}}
            <div>
                <label for="company-size" class="block text-sm font-medium text-gray-700 mb-1.5">Company Size</label>
                <select wire:model="size" id="company-size"
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white transition-all">
                    <option value="">Select size…</option>
                    @foreach ($sizeOptions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Website --}}
            <div>
                <label for="company-website" class="block text-sm font-medium text-gray-700 mb-1.5">Website</label>
                <input wire:model="website" type="url" id="company-website" placeholder="https://company.com"
                       class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('website') border-red-300 bg-red-50 @enderror">
                @error('website') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- LinkedIn URL --}}
            <div>
                <label for="company-linkedin" class="block text-sm font-medium text-gray-700 mb-1.5">LinkedIn URL</label>
                <input wire:model="linkedin_url" type="url" id="company-linkedin" placeholder="https://linkedin.com/company/…"
                       class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('linkedin_url') border-red-300 bg-red-50 @enderror">
                @error('linkedin_url') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="company-notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea wire:model="notes" id="company-notes" rows="4"
                          placeholder="Research notes, culture info, key contacts…"
                          class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition-all"></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-medium rounded-xl shadow-sm shadow-indigo-500/20 transition-all"
                >
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save Changes' : 'Add Company' }}</span>
                    <span wire:loading wire:target="save" class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Saving…
                    </span>
                </button>
                <button
                    type="button"
                    wire:click="$set('showSlideOver', false)"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-slide-over>

    {{-- ===================== SLIDE-OVER: View Details ===================== --}}
    @if($viewingCompany)
    <x-slide-over wire:model="showViewModal" title="Company Profile">
        <div class="space-y-6">
            {{-- Company Header Card --}}
            <div class="flex items-start gap-4 p-4 rounded-xl bg-indigo-50/70 border border-indigo-100">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600 text-white font-bold text-lg shrink-0 shadow-md shadow-indigo-500/20">
                    {{ strtoupper(substr($viewingCompany->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $viewingCompany->name }}</h3>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @if($viewingCompany->industry)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                {{ $viewingCompany->industry }}
                            </span>
                        @endif
                        @if($viewingCompany->size)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $sizeOptions[$viewingCompany->size] ?? $viewingCompany->size }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <span class="text-gray-400 block font-medium uppercase tracking-wider text-[10px]">Location</span>
                    <span class="text-gray-800 font-semibold mt-0.5 block">{{ $viewingCompany->location ?: 'Not specified' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <span class="text-gray-400 block font-medium uppercase tracking-wider text-[10px]">Added On</span>
                    <span class="text-gray-800 font-semibold mt-0.5 block">{{ $viewingCompany->created_at->format('M j, Y') }}</span>
                </div>
            </div>

            {{-- External Links --}}
            <div class="space-y-2">
                @if($viewingCompany->website)
                    <a href="{{ $viewingCompany->website }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-between p-3 rounded-xl bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 text-xs font-medium text-gray-700 hover:text-indigo-600 transition-all">
                        <span class="flex items-center gap-2 truncate">
                            <x-heroicon-o-globe-alt class="w-4 h-4 text-indigo-500 shrink-0" />
                            Website
                        </span>
                        <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                    </a>
                @endif

                @if($viewingCompany->linkedin_url)
                    <a href="{{ $viewingCompany->linkedin_url }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-between p-3 rounded-xl bg-white border border-gray-200 hover:border-blue-300 hover:bg-blue-50/50 text-xs font-medium text-gray-700 hover:text-blue-600 transition-all">
                        <span class="flex items-center gap-2 truncate">
                            <x-heroicon-o-link class="w-4 h-4 text-blue-500 shrink-0" />
                            LinkedIn Profile
                        </span>
                        <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                    </a>
                @endif
            </div>

            {{-- Notes --}}
            @if($viewingCompany->notes)
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Notes</h4>
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                        {{ $viewingCompany->notes }}
                    </div>
                </div>
            @endif

            {{-- Associated Applications --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Applications ({{ $viewingCompany->applications->count() }})
                    </h4>
                    <a href="{{ route('applications') }}?company={{ $viewingCompany->id }}" wire:navigate class="text-xs text-indigo-600 font-semibold hover:underline">
                        View in Kanban
                    </a>
                </div>

                @if($viewingCompany->applications->isEmpty())
                    <p class="text-xs text-gray-400 italic">No applications logged for this company.</p>
                @else
                    <div class="space-y-2">
                        @foreach($viewingCompany->applications as $app)
                            <a href="{{ route('applications.show', $app->id) }}" wire:navigate
                               class="flex items-center justify-between p-2.5 rounded-lg bg-gray-50 hover:bg-indigo-50/50 border border-gray-100 transition-colors">
                                <span class="text-xs font-semibold text-gray-800">{{ $app->job_title }}</span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                    {{ ucfirst($app->status?->value ?? $app->status) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Associated Contacts --}}
            <div>
                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    Contacts ({{ $viewingCompany->contacts->count() }})
                </h4>

                @if($viewingCompany->contacts->isEmpty())
                    <p class="text-xs text-gray-400 italic">No contacts added for this company.</p>
                @else
                    <div class="space-y-2">
                        @foreach($viewingCompany->contacts as $contact)
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-gray-50 border border-gray-100 text-xs">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $contact->name }}</p>
                                    @if($contact->role)
                                        <p class="text-[11px] text-gray-500">{{ $contact->role }}</p>
                                    @endif
                                </div>
                                @if($contact->email)
                                    <a href="mailto:{{ $contact->email }}" class="text-indigo-600 hover:underline text-[11px]">
                                        {{ $contact->email }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button
                    type="button"
                    wire:click="openEdit('{{ $viewingCompany->id }}'); $set('showViewModal', false);"
                    class="flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all"
                >
                    Edit Company
                </button>
                <button
                    type="button"
                    wire:click="closeViewModal"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </x-slide-over>
    @endif

    {{-- ===================== CONFIRM DELETE MODAL ===================== --}}
    <x-confirm-modal
        wire:model="showDeleteModal"
        :title="'Delete ' . ($deletingName ?: 'Company') . '?'"
        message="This will permanently delete all associated applications and contacts. This action cannot be undone."
        confirm-text="Delete"
        confirm-action="delete"
        confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
    />

</div>
