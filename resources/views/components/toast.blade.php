<div
    x-data="{
        toasts: [],
        add(detail) {
            const toast = { id: Date.now(), type: detail.type ?? 'info', message: detail.message ?? '' };
            this.toasts.push(toast);
            setTimeout(() => this.remove(toast.id), 3500);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="add($event.detail)"
    class="fixed top-4 right-4 z-[200] flex flex-col gap-2.5 pointer-events-none"
    style="width: 360px; max-width: calc(100vw - 2rem);"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            :class="{
                'bg-emerald-50 border-emerald-200': toast.type === 'success',
                'bg-red-50 border-red-200': toast.type === 'error',
                'bg-blue-50 border-blue-200': toast.type === 'info',
                'bg-yellow-50 border-yellow-200': toast.type === 'warning',
            }"
            class="pointer-events-auto flex items-start gap-3 px-4 py-3.5 rounded-xl border shadow-lg shadow-black/5"
        >
            {{-- Icon --}}
            <div class="shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </template>
            </div>

            {{-- Message --}}
            <p
                class="flex-1 text-sm font-medium leading-snug"
                :class="{
                    'text-emerald-800': toast.type === 'success',
                    'text-red-800': toast.type === 'error',
                    'text-blue-800': toast.type === 'info',
                    'text-yellow-800': toast.type === 'warning',
                }"
                x-text="toast.message"
            ></p>

            {{-- Close button --}}
            <button
                @click="remove(toast.id)"
                class="shrink-0 opacity-50 hover:opacity-100 transition-opacity"
                :class="{
                    'text-emerald-700': toast.type === 'success',
                    'text-red-700': toast.type === 'error',
                    'text-blue-700': toast.type === 'info',
                    'text-yellow-700': toast.type === 'warning',
                }"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
