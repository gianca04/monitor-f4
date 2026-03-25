<div class="vanilla-tabs-container">
    <div class="mb-2">
        <nav class="vanilla-tabs-nav bk-tabs flex items-center gap-1 border-b border-gray-200 dark:border-gray-700 px-1 overflow-x-auto custom-scrollbar" aria-label="Tabs">
            <!-- El JS inyecta dinámicamente los botones de los tabs aquí -->
        </nav>
    </div>
    <div class="vanilla-tabs-content">
        {{ $slot }}
    </div>
</div>
