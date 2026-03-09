<div class="space-y-6">
    <!-- Filtros -->
    <div class="rounded-2xl bg-slate-200/80 p-4 shadow-inner border border-slate-300/50">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-wrap items-end gap-3 sm:gap-4">
                <div class="w-[8.5rem]">
                    <x-input-label for="fechaInicio" :value="__('Desde')" class="!text-xs" />
                    <x-text-input
                        id="fechaInicio"
                        type="date"
                        wire:model.live="fechaInicio"
                        class="mt-1 block w-full text-sm h-9 py-1.5 rounded-lg border-slate-300"
                    />
                </div>
                <div class="w-[8.5rem]">
                    <x-input-label for="fechaFin" :value="__('Hasta')" class="!text-xs" />
                    <x-text-input
                        id="fechaFin"
                        type="date"
                        wire:model.live="fechaFin"
                        class="mt-1 block w-full text-sm h-9 py-1.5 rounded-lg border-slate-300"
                    />
                </div>
            </div>
            <div class="flex flex-col justify-end">
                <span class="block text-xs text-transparent select-none">.</span>
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="mt-1 h-9 flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    {{ __('Limpiar Filtros') }}
                </button>
            </div>
        </div>
    </div>

    <div class="flex justify-center px-2 sm:px-0">
        <!-- Contenedor con contraste: grilla 2x2 optimizada para tablet -->
        <div class="w-full max-w-2xl rounded-2xl bg-slate-200/80 p-4 sm:p-6 shadow-inner">
            <div class="grid grid-cols-2 gap-4 sm:gap-6">
            <!-- Tarjeta Colaboradores -->
            <div class="relative overflow-hidden bg-white rounded-xl shadow-md border border-slate-300 hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Colaboradores</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalColaboradores }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total activos</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-400 to-blue-600"></div>
        </div>

            <!-- Tarjeta Registros diarios -->
            <div class="relative overflow-hidden bg-white rounded-xl shadow-md border border-slate-300 hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Registros diarios</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalRegistros }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total registrados</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-400 to-green-600"></div>
        </div>

            <!-- Tarjeta SKUs activos -->
            <div class="relative overflow-hidden bg-white rounded-xl shadow-md border border-slate-300 hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">SKUs activos</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalSkus }}</p>
                        <p class="text-xs text-gray-500 mt-1">En sistema</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-400 to-purple-600"></div>
        </div>

            <!-- Tarjeta Productividad promedio -->
            <div class="relative overflow-hidden bg-white rounded-xl shadow-md border border-slate-300 hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Productividad promedio</p>
                        <p class="text-3xl font-bold text-gray-900">
                            @if(! is_null($productividadPromedio))
                                {{ number_format($productividadPromedio, 1) }}<span class="text-xl text-gray-500">%</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Promedio general</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-400 to-orange-600"></div>
        </div>
        </div>
    </div>
</div>
</div>

