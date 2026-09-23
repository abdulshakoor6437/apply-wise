<div>

    {{-- ===================== HEADER ===================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Contacts</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Recruiters, hiring managers, and referrals in your network.</p>
        </div>
        <button
            wire:click="openCreate"
            id="add-contact-btn"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all active:scale-95"
        >
            <x-heroicon-o-plus class="w-4 h-4" />
            Add Contact
        </button>
    </div>

    {{-- ===================== FILTERS + SEARCH ===================== --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        {{-- Search --}}
        <div class="relative flex-1 max-w-sm">
            <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500" />
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                id="contacts-search"
                aria-label="Search contacts by name or email"
                placeholder="Search name or email…"
                class="w-full pl-10 pr-4 py-2.5 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            >
            <div wire:loading.delay wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="w-4 h-4 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>
        </div>

        {{-- Filter by Company --}}
        <select
            wire:model.live="filterCompany"
            id="contacts-filter-company"
            aria-label="Filter contacts by company"
            class="px-3.5 py-2.5 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
        >
            <option value="">All companies</option>
            @foreach ($companies as $company)
                <option value="{{ $company['id'] }}">{{ $company['name'] }}</option>
            @endforeach
        </select>

        {{-- Filter by Role --}}
        <select
            wire:model.live="filterRole"
            id="contacts-filter-role"
            aria-label="Filter contacts by role"
            class="px-3.5 py-2.5 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
        >
            <option value="">All roles</option>
            @foreach ($roleOptions as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- ===================== TABLE CARD ===================== --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">

        {{-- Loading skeleton --}}
        <div wire:loading.delay class="divide-y divide-gray-50">
            @for($i = 0; $i < 4; $i++)
            <div class="px-6 py-4 flex items-center gap-4 animate-pulse">
                <div class="w-9 h-9 bg-gray-100 rounded-full shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-3.5 bg-gray-100 rounded w-1/4"></div>
                    <div class="h-3 bg-gray-50 rounded w-1/5"></div>
                </div>
                <div class="h-5 w-20 bg-gray-100 rounded-full"></div>
                <div class="h-3.5 w-24 bg-gray-100 rounded"></div>
                <div class="h-3.5 w-28 bg-gray-100 rounded"></div>
                <div class="h-3.5 w-24 bg-gray-100 rounded"></div>
                <div class="flex gap-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg"></div>
                    <div class="w-8 h-8 bg-gray-100 rounded-lg"></div>
                </div>
            </div>
            @endfor
        </div>

        {{-- Real content --}}
        <div wire:loading.remove.delay>

            @if ($contacts->isEmpty() && !$search && !$filterCompany && !$filterRole)
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="flex items-center justify-center w-20 h-20 mb-5 rounded-2xl bg-indigo-50">
                        <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No contacts yet</h3>
                    <p class="text-sm text-gray-500 mb-5 max-w-xs">Add recruiters, hiring managers, and referrals to stay organized.</p>
                    <button wire:click="openCreate" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add your first contact
                    </button>
                </div>

            @elseif ($contacts->isEmpty())
                {{-- No results --}}
                <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                    <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-700">No contacts match your filters</h3>
                    <p class="text-sm text-gray-400 mt-1">Try different keywords or clear the filters.</p>
                </div>

            @else
                {{-- Table --}}
                @php
                    $roleColors = [
                        'recruiter'      => 'bg-blue-100 text-blue-700',
                        'hiring_manager' => 'bg-purple-100 text-purple-700',
                        'engineer'       => 'bg-emerald-100 text-emerald-700',
                        'referral'       => 'bg-yellow-100 text-yellow-700',
                        'other'          => 'bg-gray-100 text-gray-600',
                    ];
                @endphp
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="px-6 py-3 text-left">
                                    <button wire:click="sort('name')" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-colors group">
                                        Name
                                        <svg class="w-3.5 h-3.5 {{ $sortBy === 'name' ? 'text-indigo-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortBy === 'name')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 12V4m0 16l4-4m-4 4l-4-4"/>
                                            @endif
                                        </svg>
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-left">
                                    <button wire:click="sort('role')" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-colors group">
                                        Role
                                        <svg class="w-3.5 h-3.5 {{ $sortBy === 'role' ? 'text-indigo-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortBy === 'role')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 12V4m0 16l4-4m-4 4l-4-4"/>
                                            @endif
                                        </svg>
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                                <th class="px-6 py-3 text-left">
                                    <button wire:click="sort('last_contacted_at')" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-colors group">
                                        Last Contacted
                                        <svg class="w-3.5 h-3.5 {{ $sortBy === 'last_contacted_at' ? 'text-indigo-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortBy === 'last_contacted_at')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 12V4m0 16l4-4m-4 4l-4-4"/>
                                            @endif
                                        </svg>
                                    </button>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($contacts as $contact)
                            <tr class="hover:bg-gray-50/70 transition-colors group" wire:key="contact-{{ $contact->id }}">
                                {{-- Name + initials avatar --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 font-semibold text-sm shrink-0">
                                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $contact->name }}</p>
                                    </div>
                                </td>

                                {{-- Role badge --}}
                                <td class="px-6 py-4">
                                    @if($contact->role)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColors[$contact->role] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $roleOptions[$contact->role] ?? $contact->role }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>

                                {{-- Company --}}
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($contact->company)
                                        <span class="font-medium text-gray-700">{{ $contact->company->name }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Email --}}
                                <td class="px-6 py-4">
                                    @if($contact->email)
                                        <a href="mailto:{{ $contact->email }}"
                                           class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline transition-colors">
                                            {{ $contact->email }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>

                                {{-- Last Contacted with overdue / never logic --}}
                                <td class="px-6 py-4">
                                    @if(is_null($contact->last_contacted_at))
                                        <span class="text-xs text-gray-400 italic">Never contacted</span>
                                    @else
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm text-gray-600">
                                                {{ $contact->last_contacted_at->format('M j, Y') }}
                                            </span>
                                            @if($contact->last_contacted_at->diffInDays(now()) > 14)
                                                <span class="px-1.5 py-0.5 text-xs font-semibold bg-red-100 text-red-700 rounded-full">
                                                    Overdue
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button
                                            wire:click="openEdit('{{ $contact->id }}')"
                                            title="Edit contact"
                                            class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="confirmDelete('{{ $contact->id }}')"
                                            title="Delete contact"
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
                @if ($contacts->hasPages())
                    <div class="px-6 py-4 border-t border-gray-50">
                        {{ $contacts->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ===================== SLIDE-OVER: Create / Edit ===================== --}}
    <x-slide-over wire:model="showSlideOver" :title="$editingId ? 'Edit Contact' : 'Add Contact'">
        <form wire:submit="save" class="space-y-5" id="contact-form">

            {{-- Name --}}
            <div>
                <label for="contact-name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="name"
                    type="text"
                    id="contact-name"
                    placeholder="e.g. Jane Smith"
                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('name') border-red-300 bg-red-50 @enderror"
                >
                @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Role + Company --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="contact-role" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="role" id="contact-role"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white transition-all @error('role') border-red-300 bg-red-50 @enderror">
                        <option value="">Select role…</option>
                        @foreach ($roleOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="contact-company" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Company <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="company_id" id="contact-company"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white transition-all @error('company_id') border-red-300 bg-red-50 @enderror">
                        <option value="">Select company…</option>
                        @foreach ($companies as $c)
                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                    @error('company_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Email + Phone --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="contact-email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input wire:model="email" type="email" id="contact-email" placeholder="jane@company.com"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('email') border-red-300 bg-red-50 @enderror">
                    @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="contact-phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                    <input wire:model="phone" type="tel" id="contact-phone" placeholder="+1 (555) 000-0000"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
            </div>

            {{-- LinkedIn URL --}}
            <div>
                <label for="contact-linkedin" class="block text-sm font-medium text-gray-700 mb-1.5">LinkedIn URL</label>
                <input wire:model="linkedin_url" type="url" id="contact-linkedin" placeholder="https://linkedin.com/in/…"
                       class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('linkedin_url') border-red-300 bg-red-50 @enderror">
                @error('linkedin_url') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Last Contacted --}}
            <div>
                <label for="contact-last-contacted" class="block text-sm font-medium text-gray-700 mb-1.5">Last Contacted</label>
                <input wire:model="last_contacted_at" type="date" id="contact-last-contacted"
                       class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                <p class="mt-1 text-xs text-gray-400">Contacts not reached in 14+ days will be marked overdue.</p>
            </div>

            {{-- Notes --}}
            <div>
                <label for="contact-notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea wire:model="notes" id="contact-notes" rows="3"
                          placeholder="Conversation notes, follow-up reminders…"
                          class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition-all"></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-medium rounded-xl shadow-sm shadow-indigo-500/20 transition-all"
                >
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save Changes' : 'Add Contact' }}</span>
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

    {{-- ===================== CONFIRM DELETE MODAL ===================== --}}
    <x-confirm-modal
        wire:model="showDeleteModal"
        :title="'Delete ' . ($deletingName ?: 'Contact') . '?'"
        message="This will permanently remove this contact. This action cannot be undone."
        confirm-text="Delete"
        confirm-action="delete"
        confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
    />

</div>
