@props(['quoteWarehouse', 'client', 'quote', 'clientLogo' => null, 'clientBusiness' => null, 'clientAddress' => null])

@php
    $status = strtolower($quoteWarehouse->estatus);
    $statusText = $status === 'pending' || $status === 'pendiente' ? 'Pendiente' : ($status === 'atendido' ? 'Atendido' : ucfirst($status));

    $badgeClasses = match ($status) {
        'pending', 'pendiente' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
        'atendido' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
        default => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
    };
@endphp

<div
    class="flex flex-wrap items-center justify-between gap-2 p-2 px-3 mb-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center gap-3">
        {{-- Client Info --}}
        <div class="flex items-center gap-2.5 py-1">
            @if($clientLogo)
                <div class="h-8 w-8 rounded-md overflow-hidden bg-gray-50 flex items-center justify-center border border-gray-100 dark:border-gray-700 p-0.5">
                    <img src="{{ asset('storage/'.$clientLogo) }}" alt="{{ $clientBusiness }}" class="max-h-full max-w-full object-contain">
                </div>
            @else
                <div class="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500">
                    <span class="material-symbols-outlined text-[18px]">business</span>
                </div>
            @endif
            
            <div class="flex flex-col">
                @if($clientBusiness)
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase leading-none mb-0.5">{{ $clientBusiness }}</span>
                @endif
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-tight">{{ $client }}</span>
                    @if($clientAddress)
                        <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-700/50 px-1.5 py-0.5 rounded border border-gray-100 dark:border-gray-600">
                            <span class="material-symbols-outlined text-[12px] text-gray-400">location_on</span>
                            <span class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide truncate max-w-[180px]">{{ $clientAddress }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="hidden sm:block h-4 w-px bg-gray-200 dark:bg-gray-700"></div>

        {{-- Status Badge --}}
        <span
            class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase border rounded {{ $badgeClasses }}">
            {{ $statusText }}
        </span>
    </div>

    {{-- Quick Action Button --}}
    <button type="button"
        class="btn-fill-all inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-black text-primary-700 bg-primary-100/50 hover:bg-primary-100 border border-primary-200 rounded-md transition-all dark:bg-primary-900/30 dark:text-primary-400 dark:border-primary-800 dark:hover:bg-primary-900/50 uppercase shadow-sm">
        <span class="material-symbols-outlined text-[14px]">done_all</span>
        <span>Llenar Restante</span>
    </button>
</div>