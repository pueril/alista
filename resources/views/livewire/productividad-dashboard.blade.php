<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Resumen de Productividad</h3>
            <p class="text-sm text-gray-500 mt-1">Análisis de rendimiento por colaborador y SKU</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($this->isSupervisor)
                <div>
                    <x-input-label for="tipo" :value="__('Vista por')" />
                    <div class="flex gap-2 mt-1">
                        <button
                            wire:click="$set('tipo', 'colaborador')"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg transition-colors {{ $tipo === 'colaborador' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Colaborador
                        </button>
                        <button
                            wire:click="$set('tipo', 'sku')"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg transition-colors {{ $tipo === 'sku' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            SKU
                        </button>
                    </div>
                </div>
            @endif
            <div>
                <x-input-label for="fechaInicio" :value="__('Fecha inicio')" />
                <x-text-input
                    id="fechaInicio"
                    type="date"
                    wire:model.live="fechaInicio"
                    class="mt-1 block w-full"
                />
            </div>
            <div>
                <x-input-label for="fechaFin" :value="__('Fecha fin')" />
                <x-text-input
                    id="fechaFin"
                    type="date"
                    wire:model.live="fechaFin"
                    class="mt-1 block w-full"
                />
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($data as $item)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    @if($tipo === 'colaborador')
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-lg font-bold text-blue-600">{{ $item['colaborador']->iniciales ?? '-' }}</span>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            @if($tipo === 'colaborador')
                                {{ $item['colaborador']->nombre ?? 'N/A' }}
                            @else
                                {{ $item['sku']->codigo ?? 'N/A' }}
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500">
                            @if($tipo === 'colaborador')
                                Colaborador
                            @else
                                {{ $item['sku']->familia ?? '' }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Productividad Promedio</span>
                        @php
                            $prod = $item['promedioProductividad'] ?? 0;
                            $color = $prod >= 100 ? 'text-green-600' : ($prod >= 80 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <span class="font-semibold {{ $color }}">
                            {{ number_format($prod, 1, ',', '.') }}%
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Procesadas</span>
                        <span class="font-semibold text-gray-800">{{ number_format($item['totalProcesadas'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @if($tipo === 'colaborador')
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Horas</span>
                            <span class="font-semibold text-gray-800">{{ number_format($item['totalHoras'] ?? 0, 1, ',', '.') }}</span>
                        </div>
                        @if(isset($item['asistencia']) && ($item['asistencia']['ausencia'] != 0 || $item['asistencia']['atraso'] != 0 || $item['asistencia']['total'] != 0))
                            <div class="mt-3 pt-3 border-t border-dashed">
                                <p class="text-sm font-medium text-gray-700 mb-2">Asistencia Acumulada:</p>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Ausencias (AU)</span>
                                    <span class="font-medium {{ ($item['asistencia']['ausencia'] ?? 0) < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($item['asistencia']['ausencia'] ?? 0, 1, ',', '.') }}%
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Atrasos (AT)</span>
                                    <span class="font-medium {{ ($item['asistencia']['atraso'] ?? 0) < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($item['asistencia']['atraso'] ?? 0, 1, ',', '.') }}%
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm font-semibold mt-1">
                                    <span class="text-gray-700">Total Asistencia</span>
                                    <span class="{{ ($item['asistencia']['total'] ?? 0) < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($item['asistencia']['total'] ?? 0, 1, ',', '.') }}%
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                @if($tipo === 'colaborador' && isset($item['skus']) && count($item['skus']) > 0)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm font-medium text-gray-700 mb-2">Por SKU:</p>
                        <div class="space-y-2">
                            @foreach(array_slice($item['skus'], 0, 3) as $s)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $s['sku']->codigo ?? '-' }}</span>
                                    <span class="font-medium">{{ number_format($s['promedioProductividad'] ?? 0, 1, ',', '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($tipo === 'sku' && isset($item['colaboradores']) && count($item['colaboradores']) > 0)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm font-medium text-gray-700 mb-2">Por Colaborador:</p>
                        <div class="space-y-2">
                            @foreach($item['colaboradores'] as $c)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $c['colaborador']->iniciales ?? '-' }}</span>
                                    <span class="font-medium">{{ number_format($c['promedioProductividad'] ?? 0, 1, ',', '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if(count($data) === 0)
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="text-gray-500">No hay datos de productividad para mostrar</p>
        </div>
    @endif
</div>
