@props([
    'title'         => 'Are you sure?',
    'message'       => '',
    'confirmText'   => 'Delete',
    'confirmClass'  => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
    'confirmAction' => '',
])

@php
    $wireModel = $attributes->wire('model');
@endphp

<div
    x-data="{ open: @entangle($wireModel).live }"
    x-show="open"
    x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-[60] flex items-center justify-center p-4"
    style="display: none;"
    role="dialog"
    aria-modal="true"
>
    {{-- Overlay --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
        @click="open = false"
    ></div>

    {{-- Modal card --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="relative w-full max-w-md bg-white dark:bg-slate-900 border border-transparent dark:border-slate-800 rounded-2xl shadow-2xl p-6"
    >
        <div class="flex items-start gap-4">
            {{-- Warning icon --}}
            <div class="flex items-center justify-center w-11 h-11 rounded-full bg-red-100 dark:bg-rose-950/60 shrink-0">
                <svg class="w-5 h-5 text-red-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold text-gray-900 dark:text-slate-100">{{ $title }}</h3>
                @if($message)
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-slate-400 leading-relaxed">{{ $message }}</p>
                @endif
            </div>
        </div>

        <div class="mt-6 flex gap-3 justify-end">
            <button
                type="button"
                @click="open = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
                Cancel
            </button>
            <button
                type="button"
                wire:click="{{ $confirmAction }}"
                @click="open = false"
                class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $confirmClass }}"
            >
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>
