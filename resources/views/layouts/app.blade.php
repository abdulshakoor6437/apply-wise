<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      x-init="
        if (darkMode) document.documentElement.classList.add('dark');
        $watch('darkMode', val => {
            localStorage.setItem('theme', val ? 'dark' : 'light');
            if (val) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })
      "
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — JobTracker' : config('app.name', 'JobTracker') }}</title>
    <meta name="description" content="Track your job applications, interviews, and offers in one place.">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet"/>

    <!-- Scripts & Styles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>if (typeof window.Chart === 'undefined') { document.write('<script src="{{ asset('js/chart.umd.min.js') }}"><\/script>'); }</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-100 transition-colors duration-200" x-data="{ sidebarOpen: false }">

    {{-- Livewire SPA Progress Bar --}}
    <div
        x-data="{ loading: false }"
        x-init="
            document.addEventListener('livewire:navigating', () => loading = true);
            document.addEventListener('livewire:navigated', () => loading = false);
        "
        x-show="loading"
        class="fixed top-0 left-0 right-0 z-50 h-1 bg-indigo-600 animate-pulse"
    ></div>

    <div class="flex h-screen overflow-hidden">

        {{-- ===================== SIDEBAR ===================== --}}
        {{-- Mobile overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-20 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
            x-cloak
        ></div>

        {{-- Sidebar panel --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col bg-slate-900 dark:bg-slate-900 transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 lg:flex"
        >
            {{-- Logo / Brand --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700/60">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-500 shadow-lg shadow-indigo-500/30">
                    <x-heroicon-o-briefcase class="w-5 h-5 text-white" />
                </div>
                <span class="text-white font-semibold text-lg tracking-tight">JobTracker</span>
            </div>

            {{-- Navigation links --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Main</p>

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" wire:navigate @click="sidebarOpen = false"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-heroicon-o-home class="w-5 h-5 shrink-0" />
                    Dashboard
                </a>

                {{-- Applications / Kanban --}}
                <a href="{{ route('applications') }}" wire:navigate @click="sidebarOpen = false"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('applications') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-heroicon-o-rectangle-stack class="w-5 h-5 shrink-0" />
                    Applications
                    <span class="ml-auto text-xs bg-slate-700 text-slate-300 rounded-full px-2 py-0.5 group-hover:bg-slate-600">Kanban</span>
                </a>

                <p class="px-3 mt-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Manage</p>

                {{-- Companies --}}
                <a href="{{ route('companies') }}" wire:navigate @click="sidebarOpen = false"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('companies') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-heroicon-o-building-office-2 class="w-5 h-5 shrink-0" />
                    Companies
                </a>

                {{-- Contacts --}}
                <a href="{{ route('contacts') }}" wire:navigate @click="sidebarOpen = false"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('contacts') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-heroicon-o-users class="w-5 h-5 shrink-0" />
                    Contacts
                </a>

                {{-- Documents --}}
                <a href="{{ route('documents') }}" wire:navigate @click="sidebarOpen = false"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('documents') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-heroicon-o-document-text class="w-5 h-5 shrink-0" />
                    Documents
                </a>
            </nav>

            {{-- Bottom section — Profile / Settings --}}
            <div class="px-3 py-4 border-t border-slate-700/60">
                <a href="{{ route('profile') }}" wire:navigate @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-all duration-150">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white text-xs font-semibold shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                    <x-heroicon-o-cog-6-tooth class="w-4 h-4 text-slate-400 shrink-0" />
                </a>
            </div>
        </aside>
        {{-- ===================== END SIDEBAR ===================== --}}

        {{-- ===================== MAIN AREA ===================== --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Top bar --}}
            <header class="flex items-center gap-4 px-6 py-4 bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800 shadow-sm shrink-0 transition-colors duration-200">

                {{-- Mobile hamburger --}}
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-700 dark:hover:text-slate-200 transition-colors"
                    aria-label="Toggle sidebar"
                    id="sidebar-toggle-btn"
                >
                    <x-heroicon-o-bars-3 class="w-5 h-5" />
                </button>

                {{-- Search bar --}}
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500" />
                        <input
                            type="search"
                            placeholder="Search applications, companies…"
                            aria-label="Search applications, companies"
                            class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-400 dark:placeholder-slate-500"
                            id="global-search-input"
                        >
                    </div>
                </div>

                <div class="flex items-center gap-2 ml-auto">
                    {{-- Dark Mode Toggle --}}
                    <button
                        @click="darkMode = !darkMode"
                        class="p-2 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-700 dark:hover:text-slate-200 transition-colors focus:outline-none"
                        title="Toggle Dark/Light Mode"
                        id="dark-mode-toggle-btn"
                    >
                        <x-heroicon-o-sun x-show="darkMode" class="w-5 h-5 text-amber-400" x-cloak />
                        <x-heroicon-o-moon x-show="!darkMode" class="w-5 h-5 text-slate-600 dark:text-slate-400" />
                    </button>

                    {{-- Notification bell --}}
                    <livewire:notifications.bell />

                    {{-- User dropdown --}}
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
                                id="user-menu-btn"
                            >
                                <div class="flex items-center justify-center w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="hidden sm:block">{{ Auth::user()->name ?? 'User' }}</span>
                                <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 dark:text-slate-500" />
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('profile') }}" wire:navigate id="user-menu-profile-link">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    id="user-menu-logout-btn"
                                >
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-slate-950 p-6 lg:p-8 transition-colors duration-200">
                {{ $slot }}
            </main>

        </div>
        {{-- ===================== END MAIN AREA ===================== --}}

    </div>

    {{-- Global toast notifications --}}
    <x-toast />

    @stack('scripts')
</body>
</html>
