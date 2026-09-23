<div class="relative" x-data="{ open: false }">
    {{-- Bell Button --}}
    <button
        @click="open = !open"
        class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors focus:outline-none"
        id="notification-bell-btn"
        aria-label="Notifications"
    >
        <x-heroicon-o-bell class="w-5 h-5" />

        @if ($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        @click.outside="open = false"
        x-cloak
        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-xl bg-white dark:bg-slate-800 shadow-xl ring-1 ring-black/5 dark:ring-slate-700 z-50 overflow-hidden divide-y divide-gray-100 dark:divide-slate-700/60"
    >
        {{-- Header --}}
        <div class="px-4 py-3 flex items-center justify-between bg-gray-50 dark:bg-slate-800/90 border-b border-gray-100 dark:border-slate-700">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-900 dark:text-slate-100">Notifications</span>
                @if ($unreadCount > 0)
                    <span class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-950/60 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300">
                        {{ $unreadCount }} new
                    </span>
                @endif
            </div>

            @if ($unreadCount > 0)
                <button
                    wire:click="markAllAsRead"
                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors"
                    id="mark-all-read-btn"
                >
                    Mark all as read
                </button>
            @endif
        </div>

        {{-- List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            @forelse ($notifications as $notification)
                @php
                    $type = $notification->data['type'] ?? 'default';
                    $isUnread = is_null($notification->read_at);
                    $message = $notification->data['message'] ?? '';
                    $time = $notification->created_at->diffForHumans();
                @endphp

                <div
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="group flex items-start gap-3 p-3.5 hover:bg-gray-50 cursor-pointer transition-colors relative {{ $isUnread ? 'bg-indigo-50/40' : '' }}"
                >
                    {{-- Icon based on type --}}
                    <div class="mt-0.5 shrink-0 p-1.5 rounded-lg {{ $isUnread ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500' }}">
                        @if ($type === 'follow_up')
                            <x-heroicon-o-clock class="w-4 h-4 text-amber-600" />
                        @elseif ($type === 'next_action')
                            <x-heroicon-o-calendar class="w-4 h-4 text-blue-600" />
                        @elseif ($type === 'interview')
                            <x-heroicon-o-video-camera class="w-4 h-4 text-purple-600" />
                        @else
                            <x-heroicon-o-bell class="w-4 h-4 text-indigo-600" />
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900 truncate">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </p>
                        <p class="text-xs text-gray-600 mt-0.5 line-clamp-2 leading-relaxed">
                            {{ $message }}
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1">
                            {{ $time }}
                        </p>
                    </div>

                    {{-- Unread Blue Dot --}}
                    @if ($isUnread)
                        <span class="mt-1.5 h-2 w-2 rounded-full bg-blue-600 shrink-0"></span>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">
                    <x-heroicon-o-bell-slash class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                    <p class="text-xs">No notifications yet</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="p-2.5 bg-gray-50 text-center">
            <a
                href="{{ route('notifications.index') }}"
                wire:navigate
                @click="open = false"
                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors block py-1"
                id="view-all-notifications-link"
            >
                View all notifications →
            </a>
        </div>
    </div>
</div>
