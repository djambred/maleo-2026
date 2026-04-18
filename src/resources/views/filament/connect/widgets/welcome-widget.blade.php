<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-4">
            <div class="flex-shrink-0">
                <img
                    src="{{ auth()->user()->getFilamentAvatarUrl() }}"
                    alt="{{ auth()->user()->name }}"
                    class="h-16 w-16 rounded-full object-cover ring-2 ring-primary-500"
                />
            </div>
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Hi, {{ auth()->user()->name }}! 👋
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Welcome to <span class="font-semibold text-primary-600 dark:text-primary-400">Maleo Connect</span> — Parent Communication Portal
                </p>
                <div class="mt-2 flex items-center gap-2">
                    <x-filament::badge color="warning">
                        Parent
                    </x-filament::badge>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
