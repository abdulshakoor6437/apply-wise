@props(['title' => ''])

@php
    $wireModel = $attributes->wire('model');
@endphp

<div
    x-data="{ open: @entangle($wireModel).live }"
    x-show="open"
    x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-50 overflow-hidden"
    style="display: none;"
    role="dialog"
    aria-modal="true"
>
    {{-- Dark overlay --}}
    <div
        x-show="open"
        x-transition:enter="ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
        @click="open = false"
    ></div>

    {{-- Slide-over panel --}}
    <div class="fixed inset-y-0 right-0 flex max-w-full pl-16">
        <div
            x-show="open"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-screen max-w-lg flex flex-col bg-white dark:bg-slate-900 shadow-2xl"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100">{{ $title }}</h2>
                <button
                    @click="open = false"
                    class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-all"
                    aria-label="Close panel"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Scrollable content --}}
            <div class="flex-1 overflow-y-auto px-6 py-6 text-gray-900 dark:text-slate-100">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
