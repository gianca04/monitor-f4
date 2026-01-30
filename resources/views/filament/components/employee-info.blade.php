{{-- Vista para mostrar información del empleado identificado --}}
<div class="p-4 border rounded-lg bg-primary-50 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800">
    <div class="flex items-center gap-3">
        <div
            class="flex items-center justify-center flex-shrink-0 w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-800">
            <x-heroicon-o-user class="w-6 h-6 text-primary-600 dark:text-primary-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white">{{ $employee->first_name }}
                {{ $employee->last_name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    {{ $employee->document_type }}: {{ $employee->document_number }}
                </span>
            </p>
        </div>
    </div>
</div>