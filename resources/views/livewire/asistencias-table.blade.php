<div class="space-y-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Control de Asistencia</h3>
            <p class="text-sm text-gray-500 mt-1">Registro semanal de ausencias y atrasos</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <select
                wire:model.live="selectedAnio"
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            >
                @for($year = 2024; $year <= 2037; $year++)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
            <select
                wire:model.live="selectedMes"
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Todos los meses</option>
                @php
                    $meses = [
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                    ];
                @endphp
                @foreach($meses as $num => $nombre)
                    <option value="{{ $num }}">Semanas {{ $nombre }}</option>
                @endforeach
            </select>
            <select
                wire:model.live="selectedSemana"
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Todas las semanas</option>
                @for($week = 1; $week <= 52; $week++)
                    <option value="{{ $week }}">Semana {{ $week }} ({{ $this->getWeekDateRange($week, $selectedAnio) }})</option>
                @endfor
            </select>
            @if($this->isSupervisor)
                <x-primary-button wire:click="openCreateModal">
                    {{ __('Nuevo Registro') }}
                </x-primary-button>
            @endif
        </div>
    </div>

    <!-- Tabla de Control Semanal -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-purple-600 to-indigo-600">
            <h2 class="text-xl font-semibold text-white">Control de Asistencia - {{ $selectedAnio }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 sticky left-0 bg-gray-100 z-10">Colaborador</th>
                        @foreach($semanasUnicas as $semana)
                            <th colspan="3" class="px-2 py-3 text-center font-semibold text-gray-700 border-l">
                                <div>Semana {{ $semana }}</div>
                                <div class="text-xs font-normal text-gray-500">({{ $semanasConRango[$semana] ?? '' }})</div>
                            </th>
                        @endforeach
                        <th colspan="3" class="px-4 py-3 text-center font-semibold text-gray-700 bg-blue-50 border-l-2 border-blue-300">
                            Acumulado
                        </th>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-sm text-gray-600 sticky left-0 bg-gray-50 z-10"></th>
                        @foreach($semanasUnicas as $semana)
                            <th class="px-2 py-2 text-center text-sm text-gray-600 border-l">AU</th>
                            <th class="px-2 py-2 text-center text-sm text-gray-600">AT</th>
                            <th class="px-1 py-2 text-center text-sm text-gray-600"></th>
                        @endforeach
                        <th class="px-2 py-2 text-center text-sm text-gray-600 bg-blue-50 border-l-2 border-blue-300">AU</th>
                        <th class="px-2 py-2 text-center text-sm text-gray-600 bg-blue-50">AT</th>
                        <th class="px-2 py-2 text-center text-sm text-gray-600 bg-blue-50">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($acumulados as $idx => $acum)
                        <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-4 py-3 font-medium text-gray-800 sticky left-0 z-10" style="background-color: {{ $idx % 2 === 0 ? 'white' : '#f9fafb' }}">
                                {{ $acum['colaborador']->nombre }}
                            </td>
                            @foreach($semanasUnicas as $semana)
                                @php
                                    $asist = $getAsistenciaSemana($acum['colaborador']->id, $semana);
                                @endphp
                                <td class="px-2 py-3 text-center border-l {{ ($asist?->ausencia ?? 0) < 0 ? 'text-red-600 font-semibold' : (($asist?->ausencia ?? 0) === 0 ? 'text-gray-500' : 'text-green-600') }}">
                                    @if($asist)
                                        {{ number_format($asist->ausencia, 1, ',', '.') }}%
                                    @endif
                                </td>
                                <td class="px-2 py-3 text-center {{ ($asist?->atraso ?? 0) < 0 ? 'text-red-600 font-semibold' : (($asist?->atraso ?? 0) === 0 ? 'text-gray-500' : 'text-green-600') }}">
                                    @if($asist)
                                        {{ number_format($asist->atraso, 1, ',', '.') }}%
                                    @endif
                                </td>
                                <td class="px-1 py-3 text-center">
                                    @if($this->isSupervisor && $asist)
                                        <button
                                            wire:click="openEditModal({{ $asist->id }})"
                                            class="p-1 text-blue-600 hover:bg-blue-100 rounded"
                                            title="Editar semana {{ $semana }}"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-2 py-3 text-center bg-blue-50 border-l-2 border-blue-300 {{ $acum['ausenciaTotal'] < 0 ? 'text-red-600 font-semibold' : ($acum['ausenciaTotal'] === 0 ? 'text-gray-500' : 'text-green-600') }}">
                                {{ number_format($acum['ausenciaTotal'], 1, ',', '.') }}%
                            </td>
                            <td class="px-2 py-3 text-center bg-blue-50 {{ $acum['atrasoTotal'] < 0 ? 'text-red-600 font-semibold' : ($acum['atrasoTotal'] === 0 ? 'text-gray-500' : 'text-green-600') }}">
                                {{ number_format($acum['atrasoTotal'], 1, ',', '.') }}%
                            </td>
                            <td class="px-2 py-3 text-center bg-blue-50 font-bold {{ $acum['totalGeneral'] < 0 ? 'text-red-600' : ($acum['totalGeneral'] === 0 ? 'text-gray-500' : 'text-green-600') }}">
                                {{ number_format($acum['totalGeneral'], 1, ',', '.') }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(count($acumulados) === 0)
            <div class="p-8 text-center text-gray-500">
                No hay registros de asistencia para el período seleccionado
            </div>
        @endif
    </div>

    <!-- Resumen de Acumulados -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-blue-600 to-cyan-600">
            <h2 class="text-xl font-semibold text-white">Resumen Acumulado por Colaborador - {{ $selectedAnio }}</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($acumulados as $acum)
                    <div class="p-4 rounded-lg border-2 {{ $acum['totalGeneral'] < -5 ? 'border-red-300 bg-red-50' : ($acum['totalGeneral'] < 0 ? 'border-yellow-300 bg-yellow-50' : 'border-green-300 bg-green-50') }}">
                        <h3 class="font-semibold text-gray-800 mb-3">{{ $acum['colaborador']->nombre }}</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ausencias:</span>
                                <span class="{{ $acum['ausenciaTotal'] < 0 ? 'text-red-600 font-semibold' : ($acum['ausenciaTotal'] === 0 ? 'text-gray-500' : 'text-green-600') }}">
                                    {{ number_format($acum['ausenciaTotal'], 1, ',', '.') }}%
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Atrasos:</span>
                                <span class="{{ $acum['atrasoTotal'] < 0 ? 'text-red-600 font-semibold' : ($acum['atrasoTotal'] === 0 ? 'text-gray-500' : 'text-green-600') }}">
                                    {{ number_format($acum['atrasoTotal'], 1, ',', '.') }}%
                                </span>
                            </div>
                            <div class="flex justify-between pt-2 border-t">
                                <span class="font-semibold text-gray-700">Total:</span>
                                <span class="font-bold {{ $acum['totalGeneral'] < 0 ? 'text-red-600' : ($acum['totalGeneral'] === 0 ? 'text-gray-500' : 'text-green-600') }}">
                                    {{ number_format($acum['totalGeneral'], 1, ',', '.') }}%
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal para nuevo/editar registro -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ $editingId ? __('Editar Registro de Asistencia') : __('Nuevo Registro de Asistencia') }}
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="formColaboradorId" :value="__('Colaborador')" />
                                    <select
                                        id="formColaboradorId"
                                        wire:model="formColaboradorId"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                        @if($editingId) disabled @endif
                                    >
                                        <option value="">{{ __('Seleccionar colaborador') }}</option>
                                        @foreach($colaboradores as $colaborador)
                                            <option value="{{ $colaborador->id }}">{{ $colaborador->nombre }} ({{ $colaborador->iniciales }})</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('formColaboradorId')" class="mt-2" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="formAnio" :value="__('Año')" />
                                        <select
                                            id="formAnio"
                                            wire:model="formAnio"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            required
                                            @if($editingId) disabled @endif
                                        >
                                            @for($year = 2024; $year <= 2037; $year++)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endfor
                                        </select>
                                        <x-input-error :messages="$errors->get('formAnio')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="formSemana" :value="__('Semana')" />
                                        <select
                                            id="formSemana"
                                            wire:model="formSemana"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            required
                                            @if($editingId) disabled @endif
                                        >
                                            @for($week = 1; $week <= 52; $week++)
                                                <option value="{{ $week }}">Semana {{ $week }} ({{ $this->getWeekDateRange($week, $formAnio) }})</option>
                                            @endfor
                                        </select>
                                        <x-input-error :messages="$errors->get('formSemana')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="formAusencia" :value="__('Ausencia (AU) %')" />
                                        <x-text-input
                                            id="formAusencia"
                                            type="number"
                                            step="0.1"
                                            wire:model="formAusencia"
                                            class="mt-1 block w-full"
                                            placeholder="Ej: -5 para -5%"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Use valores negativos para descuentos</p>
                                        <x-input-error :messages="$errors->get('formAusencia')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="formAtraso" :value="__('Atraso (AT) %')" />
                                        <x-text-input
                                            id="formAtraso"
                                            type="number"
                                            step="0.1"
                                            wire:model="formAtraso"
                                            class="mt-1 block w-full"
                                            placeholder="Ej: -2 para -2%"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Use valores negativos para descuentos</p>
                                        <x-input-error :messages="$errors->get('formAtraso')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="p-3 bg-gray-100 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-700">Total calculado:</span>
                                        @php
                                            $total = $formAusencia + $formAtraso;
                                            $colorClass = $total < 0 ? 'text-red-600' : ($total === 0 ? 'text-gray-500' : 'text-green-600');
                                        @endphp
                                        <span class="text-lg font-bold {{ $colorClass }}">
                                            {{ number_format($total, 1, ',', '.') }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-primary-button type="submit" class="w-full sm:w-auto sm:ml-3">
                                {{ $editingId ? __('Actualizar') : __('Guardar') }}
                            </x-primary-button>
                            <button
                                type="button"
                                wire:click="closeModal"
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
