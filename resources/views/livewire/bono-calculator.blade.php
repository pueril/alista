<div class="space-y-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Cálculo de Bono</h3>
        @if($this->isSupervisor)
            <button
                type="button"
                wire:click="openConfigModal"
                class="inline-flex items-center rounded-lg bg-sky-200 px-6 py-3.5 text-lg font-semibold text-sky-900 shadow-md border border-sky-300 hover:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 active:bg-sky-400"
            >
                {{ __('Configuración') }}
            </button>
        @endif
    </div>

    <!-- Filtros (compactos, mismo estilo que Registros diarios) -->
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
                    wire:click="$set('fechaInicio', null); $set('fechaFin', null)"
                    class="mt-1 h-9 flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    {{ __('Limpiar Filtros') }}
                </button>
            </div>
        </div>
    </div>

    @if($config)
        <!-- Resumen de Configuración: 1 fila x 4 columnas (estilo dashboard) -->
        <div class="w-full max-w-6xl rounded-2xl bg-slate-200/80 p-4 sm:p-6 shadow-inner">
            <div class="flex flex-row flex-nowrap gap-4 sm:gap-6 w-full min-w-0">
                <div class="relative overflow-hidden bg-white rounded-xl shadow-md border border-slate-300 flex-1 min-w-0">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-600 mb-1">Total a Pagar</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($this->totalBonosPagar, 0, ',', '.') }} CLP</p>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-400 to-green-600"></div>
                </div>
                <div class="relative overflow-hidden bg-white rounded-xl shadow-md border border-slate-300 flex-1 min-w-0">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-600 mb-1">Bono Líder</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($config->lider_productividad + $config->lider_asistencia, 0, ',', '.') }} CLP</p>
                        <p class="text-xs text-gray-500 mt-1">Prod: {{ number_format($config->lider_productividad, 0, ',', '.') }} | Asist: {{ number_format($config->lider_asistencia, 0, ',', '.') }}</p>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-400 to-blue-600"></div>
                </div>
                <div class="relative overflow-hidden bg-white rounded-xl shadow-md border border-slate-300 flex-1 min-w-0">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-600 mb-1">Bono Ayudante</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($config->ayudante_productividad + $config->ayudante_asistencia, 0, ',', '.') }} CLP</p>
                        <p class="text-xs text-gray-500 mt-1">Prod: {{ number_format($config->ayudante_productividad, 0, ',', '.') }} | Asist: {{ number_format($config->ayudante_asistencia, 0, ',', '.') }}</p>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-400 to-purple-600"></div>
                </div>
                <div class="relative overflow-hidden bg-white rounded-xl shadow-md border border-slate-300 flex-1 min-w-0">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-600 mb-1">Distribución</p>
                        <div class="text-xs text-gray-900 mt-1 leading-snug">
                            <p class="font-semibold">Líder: {{ round(($config->lider_productividad / ($config->lider_productividad + $config->lider_asistencia)) * 100) }}% Prod / {{ round(($config->lider_asistencia / ($config->lider_productividad + $config->lider_asistencia)) * 100) }}% Asist</p>
                            <p class="font-semibold">Ayud: {{ round(($config->ayudante_productividad / ($config->ayudante_productividad + $config->ayudante_asistencia)) * 100) }}% Prod / {{ round(($config->ayudante_asistencia / ($config->ayudante_productividad + $config->ayudante_asistencia)) * 100) }}% Asist</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-400 to-orange-600"></div>
                </div>
            </div>
        </div>

        <!-- Tabla de Cumplimiento (estilo tabla informativa: fuente pequeña, compacta, esquinas redondeadas) -->
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-4 max-w-3xl overflow-hidden">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Tabla de Cumplimiento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Productividad -->
                <div>
                    <h4 class="text-xs font-medium text-gray-600 mb-1">Productividad (Meta: {{ $config->meta_productividad }}%)</h4>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-2 py-1.5 text-left font-medium text-gray-600">Resultado</th>
                                <th class="px-2 py-1.5 text-center font-medium text-gray-600">Semáforo</th>
                                <th class="px-2 py-1.5 text-center font-medium text-gray-600">Factor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-slate-100">
                                <td class="px-2 py-1.5 text-gray-900">&gt;= {{ $config->meta_productividad }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span class="ml-1 text-green-600 font-medium">VERDE</span>
                                </td>
                                <td class="px-2 py-1.5 text-center font-semibold">{{ $config->factor_verde }}</td>
                            </tr>
                            <tr class="border-t border-slate-100">
                                <td class="px-2 py-1.5 text-gray-900">&gt;= {{ $config->limite_amarillo_prod }}% y &lt; {{ $config->meta_productividad }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span class="ml-1 font-medium" style="color:#92400e !important;">AMARILLO</span>
                                </td>
                                <td class="px-2 py-1.5 text-center font-semibold">{{ $config->factor_amarillo }}</td>
                            </tr>
                            <tr class="border-t border-slate-100">
                                <td class="px-2 py-1.5 text-gray-900">&lt; {{ $config->limite_amarillo_prod }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    <span class="ml-1 text-red-600 font-medium">ROJO</span>
                                </td>
                                <td class="px-2 py-1.5 text-center font-semibold">{{ $config->factor_rojo }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Asistencia -->
                <div>
                    <h4 class="text-xs font-medium text-gray-600 mb-1">Asistencia (Meta: {{ $config->meta_asistencia }}%)</h4>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-2 py-1.5 text-left font-medium text-gray-600">Resultado</th>
                                <th class="px-2 py-1.5 text-center font-medium text-gray-600">Semáforo</th>
                                <th class="px-2 py-1.5 text-center font-medium text-gray-600">Factor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-slate-100">
                                <td class="px-2 py-1.5 text-gray-900">&gt;= {{ $config->meta_asistencia }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span class="ml-1 text-green-600 font-medium">VERDE</span>
                                </td>
                                <td class="px-2 py-1.5 text-center font-semibold">{{ $config->factor_verde }}</td>
                            </tr>
                            <tr class="border-t border-slate-100">
                                <td class="px-2 py-1.5 text-gray-900">&gt;= {{ $config->limite_amarillo_asist }}% y &lt; {{ $config->meta_asistencia }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span class="ml-1 font-medium" style="color:#92400e !important;">AMARILLO</span>
                                </td>
                                <td class="px-2 py-1.5 text-center font-semibold">{{ $config->factor_amarillo }}</td>
                            </tr>
                            <tr class="border-t border-slate-100">
                                <td class="px-2 py-1.5 text-gray-900">&lt; {{ $config->limite_amarillo_asist }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    <span class="ml-1 text-red-600 font-medium">ROJO</span>
                                </td>
                                <td class="px-2 py-1.5 text-center font-semibold">{{ $config->factor_rojo }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de Resultados Líderes (estilo informativo: fuente pequeña, tabla compacta, esquinas redondeadas) -->
    @if(count($this->lideres) > 0)
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden max-w-5xl">
            <div class="px-3 py-2 border-b border-slate-200 bg-slate-50/70">
                <h2 class="text-sm font-semibold text-gray-800">Bonos Líderes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-2 py-2 text-left font-semibold text-gray-600">Colaborador</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-600">KPI</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-600">Meta</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-600">Resultado</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-600">Semáforo</th>
                            <th class="px-2 py-2 text-right font-semibold text-gray-600">Bono</th>
                            <th class="px-2 py-2 text-right font-semibold text-gray-600 bg-green-50/80">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->lideres as $idx => $r)
                            @php
                                $rowBg = $idx % 3 === 0 ? 'bg-white' : ($idx % 3 === 1 ? 'bg-blue-200' : 'bg-slate-300');
                            @endphp
                            <tr class="{{ $rowBg }}">
                                <td class="px-2 py-1.5" rowspan="2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-[10px] shrink-0">
                                            {{ $r['colaborador']->iniciales }}
                                        </span>
                                        <span class="font-medium text-gray-900">{{ $r['colaborador']->nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-2 py-1.5 text-center text-gray-700">Productividad Promedio</td>
                                <td class="px-2 py-1.5 text-center">{{ $r['metaProductividad'] }}%</td>
                                <td class="px-2 py-1.5 text-center font-medium">{{ number_format($r['productividadPromedio'], 1, ',', '.') }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    @php
                                        $semaforo = $r['semaforo_prod'];
                                        $colorClass = $semaforo === 'VERDE' ? 'text-green-600 bg-green-100' : ($semaforo === 'AMARILLO' ? 'bg-amber-100' : 'text-red-600 bg-red-100');
                                        $dotColor = $semaforo === 'VERDE' ? 'bg-green-500' : ($semaforo === 'AMARILLO' ? 'bg-amber-500' : 'bg-red-500');
                                    @endphp
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $colorClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                        @if($semaforo === 'AMARILLO')<span style="color:#92400e !important;">{{ $semaforo }}</span>@else{{ $semaforo }}@endif
                                    </span>
                                </td>
                                <td class="px-2 py-1.5 text-right font-medium">{{ number_format($r['bonoProductividad'], 0, ',', '.') }} CLP</td>
                                <td class="px-2 py-1.5 text-right font-bold text-green-600 bg-green-50/80" rowspan="2">{{ number_format($r['bonoTotal'], 0, ',', '.') }} CLP</td>
                            </tr>
                            <tr class="{{ $rowBg }}">
                                <td class="px-2 py-1.5 text-center text-gray-700">Total Asistencia</td>
                                <td class="px-2 py-1.5 text-center">{{ $r['metaAsistencia'] }}%</td>
                                <td class="px-2 py-1.5 text-center font-medium">{{ number_format($r['totalAsistencia'], 1, ',', '.') }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    @php
                                        $semaforo = $r['semaforo_asist'];
                                        $colorClass = $semaforo === 'VERDE' ? 'text-green-600 bg-green-100' : ($semaforo === 'AMARILLO' ? 'bg-amber-100' : 'text-red-600 bg-red-100');
                                        $dotColor = $semaforo === 'VERDE' ? 'bg-green-500' : ($semaforo === 'AMARILLO' ? 'bg-amber-500' : 'bg-red-500');
                                    @endphp
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $colorClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                        @if($semaforo === 'AMARILLO')<span style="color:#92400e !important;">{{ $semaforo }}</span>@else{{ $semaforo }}@endif
                                    </span>
                                </td>
                                <td class="px-2 py-1.5 text-right font-medium">{{ number_format($r['bonoAsistencia'], 0, ',', '.') }} CLP</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tabla de Resultados Ayudantes (estilo informativo, esquinas redondeadas) -->
    @if(count($this->ayudantes) > 0)
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden max-w-5xl">
            <div class="px-3 py-2 border-b border-slate-200 bg-slate-50/70">
                <h2 class="text-sm font-semibold text-gray-800">Bonos Ayudantes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-2 py-2 text-left font-semibold text-gray-600">Colaborador</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-600">KPI</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-600">Meta</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-600">Resultado</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-600">Semáforo</th>
                            <th class="px-2 py-2 text-right font-semibold text-gray-600">Bono</th>
                            <th class="px-2 py-2 text-right font-semibold text-gray-600 bg-green-50/80">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->ayudantes as $idx => $r)
                            @php
                                $rowBgAy = $idx % 2 === 0 ? 'bg-purple-100' : 'bg-slate-200';
                                $borderAy = 'border-b-2 border-slate-400';
                            @endphp
                            <tr class="{{ $rowBgAy }}">
                                <td class="px-2 py-1.5" rowspan="2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-6 h-6 rounded-full bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-[10px] shrink-0">
                                            {{ $r['colaborador']->iniciales }}
                                        </span>
                                        <span class="font-medium text-gray-900">{{ $r['colaborador']->nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-2 py-1.5 text-center text-gray-700">Productividad Promedio</td>
                                <td class="px-2 py-1.5 text-center">{{ $r['metaProductividad'] }}%</td>
                                <td class="px-2 py-1.5 text-center font-medium">{{ number_format($r['productividadPromedio'], 1, ',', '.') }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    @php
                                        $semaforo = $r['semaforo_prod'];
                                        $colorClass = $semaforo === 'VERDE' ? 'text-green-600 bg-green-100' : ($semaforo === 'AMARILLO' ? 'bg-amber-100' : 'text-red-600 bg-red-100');
                                        $dotColor = $semaforo === 'VERDE' ? 'bg-green-500' : ($semaforo === 'AMARILLO' ? 'bg-amber-500' : 'bg-red-500');
                                    @endphp
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $colorClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                        @if($semaforo === 'AMARILLO')<span style="color:#92400e !important;">{{ $semaforo }}</span>@else{{ $semaforo }}@endif
                                    </span>
                                </td>
                                <td class="px-2 py-1.5 text-right font-medium">{{ number_format($r['bonoProductividad'], 0, ',', '.') }} CLP</td>
                                <td class="px-2 py-1.5 text-right font-bold text-green-600 bg-green-50/80" rowspan="2">{{ number_format($r['bonoTotal'], 0, ',', '.') }} CLP</td>
                            </tr>
                            <tr class="{{ $rowBgAy }} {{ $borderAy }}">
                                <td class="px-2 py-1.5 text-center text-gray-700">Total Asistencia</td>
                                <td class="px-2 py-1.5 text-center">{{ $r['metaAsistencia'] }}%</td>
                                <td class="px-2 py-1.5 text-center font-medium">{{ number_format($r['totalAsistencia'], 1, ',', '.') }}%</td>
                                <td class="px-2 py-1.5 text-center">
                                    @php
                                        $semaforo = $r['semaforo_asist'];
                                        $colorClass = $semaforo === 'VERDE' ? 'text-green-600 bg-green-100' : ($semaforo === 'AMARILLO' ? 'bg-amber-100' : 'text-red-600 bg-red-100');
                                        $dotColor = $semaforo === 'VERDE' ? 'bg-green-500' : ($semaforo === 'AMARILLO' ? 'bg-amber-500' : 'bg-red-500');
                                    @endphp
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $colorClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                        @if($semaforo === 'AMARILLO')<span style="color:#92400e !important;">{{ $semaforo }}</span>@else{{ $semaforo }}@endif
                                    </span>
                                </td>
                                <td class="px-2 py-1.5 text-right font-medium">{{ number_format($r['bonoAsistencia'], 0, ',', '.') }} CLP</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(count($resultados) === 0)
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <p class="text-gray-500">No hay colaboradores activos para calcular bonos</p>
        </div>
    @endif

    <!-- Modal de Configuración -->
    @if($showConfigModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeConfigModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="saveConfig">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Configuración de Bonos</h3>
                            <div class="space-y-6 max-h-96 overflow-y-auto">
                                <!-- Montos Líder -->
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-3">Montos Líder</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="formLiderProductividad" :value="__('Productividad ($)')" />
                                            <x-text-input
                                                id="formLiderProductividad"
                                                type="number"
                                                step="0.01"
                                                wire:model="formLiderProductividad"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <x-input-label for="formLiderAsistencia" :value="__('Asistencia ($)')" />
                                            <x-text-input
                                                id="formLiderAsistencia"
                                                type="number"
                                                step="0.01"
                                                wire:model="formLiderAsistencia"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Total: {{ number_format($formLiderProductividad + $formLiderAsistencia, 0, ',', '.') }} CLP</p>
                                </div>

                                <!-- Montos Ayudante -->
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-3">Montos Ayudante</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="formAyudanteProductividad" :value="__('Productividad ($)')" />
                                            <x-text-input
                                                id="formAyudanteProductividad"
                                                type="number"
                                                step="0.01"
                                                wire:model="formAyudanteProductividad"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <x-input-label for="formAyudanteAsistencia" :value="__('Asistencia ($)')" />
                                            <x-text-input
                                                id="formAyudanteAsistencia"
                                                type="number"
                                                step="0.01"
                                                wire:model="formAyudanteAsistencia"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Total: {{ number_format($formAyudanteProductividad + $formAyudanteAsistencia, 0, ',', '.') }} CLP</p>
                                </div>

                                <!-- Factores de Cumplimiento -->
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-3">Factores de Cumplimiento</h4>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <x-input-label for="formFactorVerde" :value="__('Factor Verde')" />
                                            <x-text-input
                                                id="formFactorVerde"
                                                type="number"
                                                step="0.1"
                                                wire:model="formFactorVerde"
                                                class="mt-1 block w-full border-green-300"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <x-input-label for="formFactorAmarillo" :value="__('Factor Amarillo')" />
                                            <x-text-input
                                                id="formFactorAmarillo"
                                                type="number"
                                                step="0.1"
                                                wire:model="formFactorAmarillo"
                                                class="mt-1 block w-full border-yellow-300"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <x-input-label for="formFactorRojo" :value="__('Factor Rojo')" />
                                            <x-text-input
                                                id="formFactorRojo"
                                                type="number"
                                                step="0.1"
                                                wire:model="formFactorRojo"
                                                class="mt-1 block w-full border-red-300"
                                                required
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Límites Productividad -->
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-3">Límites Productividad (%)</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="formMetaProductividad" :value="__('Meta Verde (>=)')" />
                                            <x-text-input
                                                id="formMetaProductividad"
                                                type="number"
                                                wire:model="formMetaProductividad"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <x-input-label for="formLimiteAmarilloProd" :value="__('Límite Amarillo (>=)')" />
                                            <x-text-input
                                                id="formLimiteAmarilloProd"
                                                type="number"
                                                wire:model="formLimiteAmarilloProd"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Límites Asistencia -->
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-3">Límites Asistencia (%)</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="formMetaAsistencia" :value="__('Meta Verde (>=)')" />
                                            <x-text-input
                                                id="formMetaAsistencia"
                                                type="number"
                                                step="0.1"
                                                wire:model="formMetaAsistencia"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <x-input-label for="formLimiteAmarilloAsist" :value="__('Límite Amarillo (>=)')" />
                                            <x-text-input
                                                id="formLimiteAmarilloAsist"
                                                type="number"
                                                step="0.1"
                                                wire:model="formLimiteAmarilloAsist"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-primary-button type="submit" class="w-full sm:w-auto sm:ml-3">
                                {{ __('Guardar Configuración') }}
                            </x-primary-button>
                            <button
                                type="button"
                                wire:click="closeConfigModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                            >
                                {{ __('Cancelar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
