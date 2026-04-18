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
                    Welcome to <span class="font-semibold text-primary-600 dark:text-primary-400">Maleo SIAKAD</span> — Academic Information System
                </p>
                <div class="mt-2 flex items-center gap-2">
                    @foreach(auth()->user()->roles as $role)
                        <x-filament::badge
                            :color="match($role->name) {
                                'super_admin' => 'danger',
                                'admin' => 'warning',
                                default => 'gray',
                            }"
                        >
                            {{ str_replace('_', ' ', ucwords($role->name, '_')) }}
                        </x-filament::badge>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
