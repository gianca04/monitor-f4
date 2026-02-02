<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col items-center justify-center py-6 text-center">
            <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                ¡Bienvenido, {{ auth()->user()->employee?->first_name ?? auth()->user()->name }}!
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                Tu rol actual es: <span
                    class="font-medium text-primary-600">{{ auth()->user()->getRoleNames()->first() }}</span>
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
