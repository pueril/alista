<div class="space-y-4 sm:space-y-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Registros</h3>
        <button
            type="button"
            wire:click="openCreateModal"
            class="inline-flex items-center gap-2.5 rounded-lg bg-sky-200 px-6 py-3.5 text-lg font-semibold text-sky-900 shadow-md border border-sky-300 hover:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 active:bg-sky-400"
        >
            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/30 text-sky-900 text-xl font-bold leading-none" aria-hidden="true">+</span>
            {{ __('Nuevo Registro') }}
        </button>
    </div>

    {{-- Tarjeta de filtros compacta, en línea con el dashboard --}}
    <div class="rounded-2xl bg-slate-200/80 p-4 shadow-inner border border-slate-300/50">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-wrap items-end gap-3 sm:gap-4">
                <div class="w-[8.5rem]">
                    <x-input-label for="fechaInicio" :value="__('Desde')" class="!text-xs" />
                    <x-text-input
                        id="fechaInicio"
                        type="date"
                        wire:model.live="fechaInicio"
                        class="mt-1 block w-full text-sm py-1.5 rounded-lg border-slate-300"
                    />
                </div>
                <div class="w-[8.5rem]">
                    <x-input-label for="fechaFin" :value="__('Hasta')" class="!text-xs" />
                    <x-text-input
                        id="fechaFin"
                        type="date"
                        wire:model.live="fechaFin"
                        class="mt-1 block w-full text-sm py-1.5 rounded-lg border-slate-300"
                    />
                </div>
            </div>
            @if($this->isSupervisor)
                <div class="min-w-0 max-w-[12rem] sm:max-w-[14rem]">
                    <x-input-label for="colaboradorId" :value="__('Colaborador')" class="!text-xs" />
                    <select
                        id="colaboradorId"
                        wire:model.live="colaboradorId"
                        class="mt-1 block w-full text-sm rounded-lg border-slate-300 py-1.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">{{ __('Todos') }}</option>
                        @foreach($colaboradores as $colaborador)
                            <option value="{{ $colaborador->id }}">
                                {{ $colaborador->nombre }} ({{ $colaborador->iniciales }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="min-w-0 max-w-[12rem] sm:max-w-[14rem]">
                <x-input-label for="skuId" :value="__('SKU')" class="!text-xs" />
                <select
                    id="skuId"
                    wire:model.live="skuId"
                    class="mt-1 block w-full text-sm rounded-lg border-slate-300 py-1.5 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">{{ __('Todos') }}</option>
                    @foreach($skus as $sku)
                        <option value="{{ $sku->id }}">
                            {{ $sku->codigo }} - {{ $sku->familia }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Fecha') }}
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Colaborador') }}
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('SKU') }}
                        </th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Procesadas') }}
                        </th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Meta') }}
                        </th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Productividad') }}
                        </th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Inicio') }}
                        </th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Fin') }}
                        </th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Horas') }}
                        </th>
                        @if($this->isSupervisor)
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Acciones') }}
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($registros as $registro)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $registro->fecha?->format('d-m-Y') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                @if($registro->colaborador)
                                    {{ $registro->colaborador->nombre }}
                                    ({{ $registro->colaborador->iniciales }})
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $registro->sku?->codigo }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">
                                {{ number_format($registro->procesadas, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">
                                {{ number_format($registro->meta_establecida ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right">
                                @php
                                    $prod = $registro->productividad ?? 0;
                                    $color = $prod >= 100 ? 'text-green-600' : ($prod >= 80 ? 'text-yellow-600' : 'text-red-600');
                                @endphp
                                <span class="font-semibold {{ $color }}">
                                    {{ number_format($prod, 1, ',', '.') }}%
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-900">
                                {{ $registro->hora_ingreso }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-900">
                                {{ $registro->hora_finalizacion }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">
                                {{ number_format($registro->horas_proceso ?? 0, 2, ',', '.') }}
                            </td>
                            @if($this->isSupervisor)
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-center">
                                    <div class="flex justify-center items-center gap-4">
                                        <button
                                            type="button"
                                            wire:click="openEditModal('{{ $registro->id }}')"
                                            class="inline-flex items-center justify-center min-w-[2.75rem] min-h-[2.75rem] p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                            title="Editar"
                                        >
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="delete('{{ $registro->id }}')"
                                            wire:confirm="¿Está seguro de eliminar este registro?"
                                            class="inline-flex items-center justify-center min-w-[2.75rem] min-h-[2.75rem] p-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                            title="Eliminar"
                                        >
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $this->isSupervisor ? '10' : '9' }}" class="px-4 py-6 text-center text-sm text-gray-500">
                                {{ __('No hay registros que coincidan con los filtros seleccionados.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
            {{ $registros->links() }}
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <form wire:submit.prevent="save">
                        <div class="px-4 pt-5 pb-2 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-5">
                                {{ $editingId ? __('Editar Registro') : __('Nuevo Registro') }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="formFecha" :value="__('Fecha')" />
                                    <x-text-input
                                        id="formFecha"
                                        type="date"
                                        wire:model="formFecha"
                                        class="mt-1 block w-full rounded-lg border-slate-300"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('formFecha')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="formColaboradorId" :value="__('Colaborador')" />
                                    <select
                                        id="formColaboradorId"
                                        wire:model="formColaboradorId"
                                        class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 py-2"
                                        required
                                    >
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        @foreach($colaboradores as $colaborador)
                                            <option value="{{ $colaborador->id }}">
                                                {{ $colaborador->nombre }} ({{ $colaborador->iniciales }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('formColaboradorId')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="formSkuId" :value="__('SKU')" />
                                    <select
                                        id="formSkuId"
                                        wire:model="formSkuId"
                                        class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 py-2"
                                        required
                                    >
                                        <option value="">{{ __('Seleccionar...') }}</option>
                                        @foreach($skus as $sku)
                                            <option value="{{ $sku->id }}">
                                                {{ $sku->codigo }} - {{ $sku->familia }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('formSkuId')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="formProcesadas" :value="__('Unidades Procesadas')" />
                                    <x-text-input
                                        id="formProcesadas"
                                        type="number"
                                        min="0"
                                        wire:model="formProcesadas"
                                        class="mt-1 block w-full rounded-lg border-slate-300"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('formProcesadas')" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="formHoraIngreso" :value="__('Hora Inicio')" />
                                        <x-text-input
                                            id="formHoraIngreso"
                                            type="time"
                                            wire:model="formHoraIngreso"
                                            class="mt-1 block w-full rounded-lg border-slate-300"
                                            required
                                        />
                                        <x-input-error :messages="$errors->get('formHoraIngreso')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="formHoraFinalizacion" :value="__('Hora Fin')" />
                                        <x-text-input
                                            id="formHoraFinalizacion"
                                            type="time"
                                            wire:model="formHoraFinalizacion"
                                            class="mt-1 block w-full rounded-lg border-slate-300"
                                            required
                                        />
                                        <x-input-error :messages="$errors->get('formHoraFinalizacion')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-5 pb-5 px-4 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-3">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-slate-300 bg-white px-4 py-4 text-base font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                            >
                                {{ __('Cancelar') }}
                            </button>
                            <button
                                type="submit"
                                style="background-color: #2563eb; color: #ffffff;"
                                class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg px-5 py-4 text-base font-semibold shadow-md border border-blue-600 hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
                            >
                                {{ $editingId ? __('Actualizar') : __('Guardar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

