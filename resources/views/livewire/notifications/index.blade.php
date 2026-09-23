<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header section --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100 tracking-tight">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Stay updated with your job application reminders and action items.</p>
        </div>

        @if (auth()->user()->unreadNotifications->count() > 0)
            <button
                wire:click="markAllAsRead"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 text-sm font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/80 transition-colors"
                id="page-mark-all-read-btn"
            >
                <x-heroicon-o-check-circle class="w-4 h-4" />
                Mark all as read
            </button>
        @endif
    </div>

    {{-- Notifications List Card --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-slate-700 overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-slate-700/60">
            @forelse ($notifications as $notification)
                @php
                    $type = $notification->data['type'] ?? 'default';
                    $isUnread = is_null($notification->read_at);
                    $message = $notification->data['message'] ?? '';
                    $title = $notification->data['title'] ?? 'Notification';
                    $url = $notification->data['url'] ?? (
                        isset($notification->data['application_id'])
                            ? route('applications.show', $notification->data['application_id'])
                            : '#'
                    );
                @endphp

                <div
                    class="p-5 flex items-start gap-4 transition-colors {{ $isUnread ? 'bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-l-blue-500' : 'hover:bg-gray-50 dark:hover:bg-slate-750' }}"
                >
                    {{-- Icon --}}
                    <div class="p-2.5 rounded-xl shrink-0 {{ $isUnread ? 'bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400' : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400' }}">
                        @if ($type === 'follow_up')
                            <x-heroicon-o-clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        @elseif ($type === 'next_action')
                            <x-heroicon-o-calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        @elseif ($type === 'interview')
                            <x-heroicon-o-video-camera class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        @else
                            <x-heroicon-o-bell class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-100">
                                {{ $title }}
                            </h3>
                            @if ($isUnread)
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-700">
                                    New
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed">
                            {{ $message }}
                        </p>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-xs text-gray-400">
                                {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('M j, Y g:i A') }})
                            </span>
                            @if (isset($notification->data['application_id']))
                                <button
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors inline-flex items-center gap-1"
                                >
                                    View Application →
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 px-4 text-center">
                    <x-heroicon-o-bell-slash class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                    <h3 class="text-base font-semibold text-gray-900">No notifications</h3>
                    <p class="text-sm text-gray-500 mt-1">You're all caught up! Automated reminders will appear here.</p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
