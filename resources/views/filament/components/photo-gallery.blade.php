{{-- Galería de Fotos Antes/Después - Componente Filament v4 --}}
@vite('resources/css/app.css')

<div class="space-y-6">
    {{-- Header de la Galería --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div
                class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg shadow-primary-500/30">
                <x-heroicon-o-photo class="w-5 h-5 text-white" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Galería de Evidencias
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Comparación antes y después del trabajo
                </p>
            </div>
        </div>
        <span
            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300 ring-1 ring-inset ring-primary-600/20">
            <x-heroicon-o-camera class="w-3.5 h-3.5 mr-1.5" />
            3 comparaciones
        </span>
    </div>

    {{-- Grid de Comparaciones --}}
    <div class="grid gap-6 sm:grid-cols-1 lg:grid-cols-2">

        {{-- Card de Comparación 1 --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none ring-1 ring-gray-200/50 dark:ring-gray-700/50 transition-all duration-300 hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-1">
            {{-- Badge de estado --}}
            <div class="absolute top-4 right-4 z-20">
                <span
                    class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-500/90 text-white backdrop-blur-sm shadow-lg">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 mr-1" />
                    Completado
                </span>
            </div>

            {{-- Contenedor de Imágenes con Slider --}}
            <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800"
                x-data="{ 
                     sliderPosition: 50,
                     isDragging: false
                 }" @mousedown="isDragging = true" @mouseup="isDragging = false" @mouseleave="isDragging = false"
                @mousemove="if(isDragging) sliderPosition = Math.max(0, Math.min(100, ($event.offsetX / $el.offsetWidth) * 100))">

                {{-- Imagen DESPUÉS (derecha/fondo) --}}
                <div class="absolute inset-0">
                    <img src="https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&h=500&fit=crop"
                        alt="Después del trabajo" class="w-full h-full object-cover" />
                    <div class="absolute bottom-3 right-3 z-10">
                        <span
                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-emerald-500/90 text-white backdrop-blur-sm">
                            <x-heroicon-o-check class="w-3 h-3 mr-1" />
                            Después
                        </span>
                    </div>
                </div>

                {{-- Imagen ANTES (izquierda/recortada) --}}
                <div class="absolute inset-0 overflow-hidden"
                    :style="'clip-path: inset(0 ' + (100 - sliderPosition) + '% 0 0)'">
                    <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&h=500&fit=crop"
                        alt="Antes del trabajo" class="w-full h-full object-cover" />
                    <div class="absolute bottom-3 left-3 z-10">
                        <span
                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-amber-500/90 text-white backdrop-blur-sm">
                            <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                            Antes
                        </span>
                    </div>
                </div>

                {{-- Línea divisoria del Slider --}}
                <div class="absolute top-0 bottom-0 z-20 w-1 -translate-x-1/2 cursor-ew-resize"
                    :style="'left: ' + sliderPosition + '%'">
                    <div class="absolute inset-0 bg-white shadow-lg"></div>
                    {{-- Handle del slider --}}
                    <div
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white shadow-xl flex items-center justify-center cursor-ew-resize border-2 border-gray-200 transition-transform duration-150 hover:scale-110">
                        <div class="flex items-center gap-0.5">
                            <x-heroicon-s-chevron-left class="w-3 h-3 text-gray-600" />
                            <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-600" />
                        </div>
                    </div>
                </div>

                {{-- Overlay de gradiente sutil --}}
                <div
                    class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/30 to-transparent pointer-events-none">
                </div>
            </div>

            {{-- Contenido de la Card --}}
            <div class="p-5 space-y-4">
                {{-- Descripción --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            <span
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Antes</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                            Área de trabajo con acumulación de residuos y materiales desordenados.
                        </p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Después</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                            Espacio completamente limpio y organizado según estándares.
                        </p>
                    </div>
                </div>

                {{-- Footer con metadata --}}
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-calendar class="w-4 h-4" />
                        <span>Hace 2 horas</span>
                    </div>
                    <button type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                        <x-heroicon-o-arrows-pointing-out class="w-4 h-4" />
                        Ver ampliado
                    </button>
                </div>
            </div>
        </div>

        {{-- Card de Comparación 2 --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none ring-1 ring-gray-200/50 dark:ring-gray-700/50 transition-all duration-300 hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-1">
            <div class="absolute top-4 right-4 z-20">
                <span
                    class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-500/90 text-white backdrop-blur-sm shadow-lg">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 mr-1" />
                    Completado
                </span>
            </div>

            <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800"
                x-data="{ sliderPosition: 50, isDragging: false }" @mousedown="isDragging = true"
                @mouseup="isDragging = false" @mouseleave="isDragging = false"
                @mousemove="if(isDragging) sliderPosition = Math.max(0, Math.min(100, ($event.offsetX / $el.offsetWidth) * 100))">

                <div class="absolute inset-0">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=500&fit=crop"
                        alt="Después del trabajo" class="w-full h-full object-cover" />
                    <div class="absolute bottom-3 right-3 z-10">
                        <span
                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-emerald-500/90 text-white backdrop-blur-sm">
                            <x-heroicon-o-check class="w-3 h-3 mr-1" />
                            Después
                        </span>
                    </div>
                </div>

                <div class="absolute inset-0 overflow-hidden"
                    :style="'clip-path: inset(0 ' + (100 - sliderPosition) + '% 0 0)'">
                    <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&h=500&fit=crop"
                        alt="Antes del trabajo" class="w-full h-full object-cover" />
                    <div class="absolute bottom-3 left-3 z-10">
                        <span
                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-amber-500/90 text-white backdrop-blur-sm">
                            <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                            Antes
                        </span>
                    </div>
                </div>

                <div class="absolute top-0 bottom-0 z-20 w-1 -translate-x-1/2 cursor-ew-resize"
                    :style="'left: ' + sliderPosition + '%'">
                    <div class="absolute inset-0 bg-white shadow-lg"></div>
                    <div
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white shadow-xl flex items-center justify-center cursor-ew-resize border-2 border-gray-200 transition-transform duration-150 hover:scale-110">
                        <div class="flex items-center gap-0.5">
                            <x-heroicon-s-chevron-left class="w-3 h-3 text-gray-600" />
                            <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-600" />
                        </div>
                    </div>
                </div>

                <div
                    class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/30 to-transparent pointer-events-none">
                </div>
            </div>

            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            <span
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Antes</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                            Sistema de tuberías con fugas visibles y corrosión.
                        </p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Después</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                            Reparación completa del sistema de tuberías sin fugas.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-calendar class="w-4 h-4" />
                        <span>Hace 5 horas</span>
                    </div>
                    <button type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                        <x-heroicon-o-arrows-pointing-out class="w-4 h-4" />
                        Ver ampliado
                    </button>
                </div>
            </div>
        </div>

        {{-- Card de Comparación 3 - Solo foto "Después" --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none ring-1 ring-gray-200/50 dark:ring-gray-700/50 transition-all duration-300 hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-1">
            <div class="absolute top-4 right-4 z-20">
                <span
                    class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-500/90 text-white backdrop-blur-sm shadow-lg">
                    <x-heroicon-s-clock class="w-3.5 h-3.5 mr-1" />
                    En progreso
                </span>
            </div>

            <div
                class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800">
                {{-- Solo imagen después --}}
                <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&h=500&fit=crop"
                    alt="Antes del trabajo" class="w-full h-full object-cover" />

                {{-- Overlay indicando que falta foto --}}
                <div class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-[2px]">
                    <div class="text-center p-6 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20">
                        <div
                            class="w-16 h-16 mx-auto mb-3 rounded-full bg-amber-500/20 flex items-center justify-center">
                            <x-heroicon-o-camera class="w-8 h-8 text-amber-400" />
                        </div>
                        <p class="text-sm font-medium text-white">Pendiente: Foto "Después"</p>
                        <p class="text-xs text-white/70 mt-1">Sube la evidencia del trabajo completado</p>
                    </div>
                </div>

                <div class="absolute bottom-3 left-3 z-10">
                    <span
                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-amber-500/90 text-white backdrop-blur-sm">
                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                        Antes
                    </span>
                </div>
            </div>

            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            <span
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Antes</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                            Instalación eléctrica obsoleta que requiere actualización.
                        </p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                            <span
                                class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Después</span>
                        </div>
                        <p class="text-sm text-gray-400 dark:text-gray-500 italic line-clamp-2">
                            Pendiente de documentar...
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-calendar class="w-4 h-4" />
                        <span>Hace 1 día</span>
                    </div>
                    <button type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors">
                        <x-heroicon-o-plus-circle class="w-4 h-4" />
                        Agregar foto
                    </button>
                </div>
            </div>
        </div>

        {{-- Card Vacía - Agregar Nueva Comparación --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50 ring-2 ring-dashed ring-gray-300 dark:ring-gray-600 transition-all duration-300 hover:ring-primary-400 dark:hover:ring-primary-500 hover:bg-gradient-to-br hover:from-primary-50 hover:to-primary-100 dark:hover:from-primary-900/20 dark:hover:to-primary-800/20 cursor-pointer">
            <div class="flex flex-col items-center justify-center p-8 min-h-[320px]">
                <div
                    class="w-20 h-20 mb-4 rounded-2xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors">
                    <x-heroicon-o-plus
                        class="w-10 h-10 text-gray-400 dark:text-gray-500 group-hover:text-primary-500 dark:group-hover:text-primary-400 transition-colors" />
                </div>
                <h4
                    class="text-base font-medium text-gray-600 dark:text-gray-400 group-hover:text-primary-700 dark:group-hover:text-primary-300 transition-colors">
                    Agregar Evidencia
                </h4>
                <p class="mt-1 text-sm text-gray-400 dark:text-gray-500 text-center max-w-xs">
                    Sube fotos de antes y después para documentar el trabajo realizado
                </p>
            </div>
        </div>

    </div>

    {{-- Instrucciones de uso --}}
    <div
        class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50">
        <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5" />
        <div class="text-sm text-blue-700 dark:text-blue-300">
            <p class="font-medium">Consejo:</p>
            <p class="mt-1 text-blue-600 dark:text-blue-400">
                Arrastra el control deslizante hacia la izquierda o derecha para comparar las imágenes antes y después
                del trabajo.
            </p>
        </div>
    </div>
</div>