<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Notification Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Manage how and when you receive automated job application reminders.') }}
        </p>
    </header>

    <form wire:submit="updateNotificationPreferences" class="mt-6 space-y-6">
        {{-- Email notifications --}}
        <div class="flex items-center justify-between py-2 border-b border-gray-100">
            <div>
                <label for="email_notifications" class="text-sm font-semibold text-gray-900 block">
                    {{ __('Email Notifications') }}
                </label>
                <p class="text-xs text-gray-500">
                    {{ __('Receive copy of reminders via email (master toggle).') }}
                </p>
            </div>
            <label for="email_notifications" class="relative inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    wire:model="email"
                    id="email_notifications"
                    aria-label="Email Notifications toggle"
                    class="sr-only peer"
                >
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
        </div>

        {{-- Follow-up reminders --}}
        <div class="flex items-center justify-between py-2 border-b border-gray-100">
            <div>
                <label for="follow_ups_notifications" class="text-sm font-semibold text-gray-900 block">
                    {{ __('Follow-up Reminders') }}
                </label>
                <p class="text-xs text-gray-500">
                    {{ __('Get notified when applied applications have no response after 7+ days.') }}
                </p>
            </div>
            <label for="follow_ups_notifications" class="relative inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    wire:model="follow_ups"
                    id="follow_ups_notifications"
                    aria-label="Follow-up Reminders toggle"
                    class="sr-only peer"
                >
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
        </div>

        {{-- Action date reminders --}}
        <div class="flex items-center justify-between py-2 border-b border-gray-100">
            <div>
                <label for="actions_notifications" class="text-sm font-semibold text-gray-900 block">
                    {{ __('Action Date Reminders') }}
                </label>
                <p class="text-xs text-gray-500">
                    {{ __('Get notified when application next action date is tomorrow, today, or overdue.') }}
                </p>
            </div>
            <label for="actions_notifications" class="relative inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    wire:model="actions"
                    id="actions_notifications"
                    aria-label="Action Date Reminders toggle"
                    class="sr-only peer"
                >
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
        </div>

        {{-- Interview reminders --}}
        <div class="flex items-center justify-between py-2 border-b border-gray-100">
            <div>
                <label for="interviews_notifications" class="text-sm font-semibold text-gray-900 block">
                    {{ __('Interview Reminders') }}
                </label>
                <p class="text-xs text-gray-500">
                    {{ __('Get notified 24 hours and 1 hour before scheduled interviews.') }}
                </p>
            </div>
            <label for="interviews_notifications" class="relative inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    wire:model="interviews"
                    id="interviews_notifications"
                    aria-label="Interview Reminders toggle"
                    class="sr-only peer"
                >
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button id="save-notification-preferences-btn">{{ __('Save Preferences') }}</x-primary-button>

            <x-action-message class="me-3" on="notification-preferences-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
