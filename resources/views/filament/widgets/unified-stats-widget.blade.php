<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($this->getData()['stats'] as $stat)
            <div
                class="relative flex flex-col p-6 transition-all duration-300 bg-white border border-gray-200 shadow-sm rounded-xl dark:bg-gray-800 dark:border-gray-700 hover:shadow-md">
                <div class="flex items-center gap-x-4">
                    <div
                        class="flex items-center justify-center w-12 h-12 rounded-lg {{ $stat['color'] }} text-white shadow-sm">
                        <x-dynamic-component :component="'heroicon-o-' . $stat['icon']" class="w-6 h-6" />
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                            {{ $stat['label'] }}
                        </p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $stat['value'] }}
                        </h3>
                    </div>
                </div>

                <div class="absolute bottom-0 left-0 w-full h-1 rounded-b-xl {{ $stat['color'] }} opacity-50"></div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
