<x-filament-widgets::widget>
    <x-filament::section>
        <div
            class="flex flex-col items-center justify-center py-8 text-center rounded-lg shadow-md bg-gradient-to-br from-primary-50 to-primary-100 dark:from-gray-800 dark:to-gray-900">
            <img src="{{ asset('images/no-image.png') }}" alt="Imagen de bienvenida"
                class="object-cover w-24 h-24 mb-4 rounded-full">
            <div class="flex items-center mb-4 space-x-2">
                <x-heroicon-o-user class="w-8 h-8 text-primary-600" />
                <h1 class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">
                    ¡Bienvenido, {{ auth()->user()->employee?->first_name ?? auth()->user()->name }}!
                </h1>
            </div>
            <p class="mb-2 text-sm text-gray-600 dark:text-gray-300">
                Tu rol actual es: <span
                    class="font-semibold text-primary-700 dark:text-primary-400">{{ auth()->user()->getRoleNames()->first() }}</span>
            </p>
            <div class="flex items-start mt-4 space-x-2">
                <x-heroicon-o-information-circle class="w-5 h-5 text-gray-500 mt-0.5" />
                <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                    @switch(auth()->user()->getRoleNames()->first())
                        @case('Almacen')
                            Te encargarás de la logística de las cotizaciones aprobadas.
                        @break

                        @default
                            Descripción no disponible para este rol.
                    @endswitch
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
