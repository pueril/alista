<div class="space-y-4 sm:space-y-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">SKUs</h3>
        @if($this->isSupervisor)
            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2.5 rounded-lg bg-sky-200 px-6 py-3.5 text-lg font-semibold text-sky-900 shadow-md border border-sky-300 hover:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 active:bg-sky-400"
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/30 text-sky-900 text-xl font-bold leading-none" aria-hidden="true">+</span>
                {{ __('Nuevo SKU') }}
            </button>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Código SKU') }}
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Familia') }}
                        </th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Meta Diaria') }}
                        </th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Prod/Hora') }}
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Estado') }}
                        </th>
                        @if($this->isSupervisor)
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Acciones') }}
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($skus as $sku)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $sku->codigo }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $sku->familia }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">
                                {{ number_format($sku->meta_diaria, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">
                                {{ number_format($sku->prod_hora, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                @if($sku->activo)
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                        Activo
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            @if($this->isSupervisor)
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-center">
                                    <div class="flex justify-center items-center gap-4">
                                        <button
                                            type="button"
                                            wire:click="openEditModal({{ $sku->id }})"
                                            class="inline-flex items-center justify-center min-w-[2.75rem] min-h-[2.75rem] p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                            title="Editar"
                                        >
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="toggleActive({{ $sku->id }})"
                                            class="inline-flex items-center justify-center min-w-[2.75rem] min-h-[2.75rem] p-2 rounded-lg {{ $sku->activo ? 'text-amber-600 hover:bg-amber-50 hover:text-amber-900' : 'text-green-600 hover:bg-green-50 hover:text-green-900' }} focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $sku->activo ? 'focus:ring-amber-500' : 'focus:ring-green-500' }}"
                                            title="{{ $sku->activo ? 'Desactivar' : 'Activar' }}"
                                        >
                                            @if($sku->activo)
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="openDeleteModal({{ $sku->id }})"
                                            class="inline-flex items-center justify-center min-w-[2.75rem] min-h-[2.75rem] p-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                            title="Eliminar permanentemente"
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
                            <td colspan="{{ $this->isSupervisor ? '6' : '5' }}" class="px-4 py-6 text-center text-sm text-gray-500">
                                {{ __('No hay SKUs registrados.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
            {{ $skus->links() }}
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
                                {{ $editingId ? __('Editar SKU') : __('Nuevo SKU') }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="formCodigo" :value="__('Código SKU')" />
                                    <x-text-input
                                        id="formCodigo"
                                        type="text"
                                        wire:model="formCodigo"
                                        class="mt-1 block w-full rounded-lg border-slate-300"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('formCodigo')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="formFamilia" :value="__('Familia')" />
                                    <x-text-input
                                        id="formFamilia"
                                        type="text"
                                        wire:model="formFamilia"
                                        class="mt-1 block w-full rounded-lg border-slate-300"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('formFamilia')" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="formMetaDiaria" :value="__('Meta Diaria')" />
                                        <x-text-input
                                            id="formMetaDiaria"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model="formMetaDiaria"
                                            class="mt-1 block w-full rounded-lg border-slate-300"
                                            required
                                        />
                                        <x-input-error :messages="$errors->get('formMetaDiaria')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="formProdHora" :value="__('Prod/Hora')" />
                                        <x-text-input
                                            id="formProdHora"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model="formProdHora"
                                            class="mt-1 block w-full rounded-lg border-slate-300"
                                            required
                                        />
                                        <x-input-error :messages="$errors->get('formProdHora')" class="mt-2" />
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
                                {{ $editingId ? __('Actualizar') : __('Crear') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteModal && $deletingId)
        @php
            $sku = \App\Models\SKU::find($deletingId);
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDeleteModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="px-4 pt-5 pb-2 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-5">
                            {{ __('Confirmar Eliminación') }}
                        </h3>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-red-800">
                                ¿Está seguro de eliminar permanentemente el SKU <strong>{{ $sku?->codigo }}</strong>?
                            </p>
                            <p class="text-red-600 text-sm mt-2">
                                Esta acción no se puede deshacer. Se eliminarán también todos los registros de productividad asociados.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 pb-5 px-4 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-3">
                        <button
                            type="button"
                            wire:click="closeDeleteModal"
                            class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-slate-300 bg-white px-4 py-4 text-base font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                        >
                            {{ __('Cancelar') }}
                        </button>
                        <button
                            type="button"
                            wire:click="confirmDelete"
                            class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg px-5 py-4 text-base font-semibold text-white shadow-md border border-red-700 hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            style="background-color: #dc2626;"
                        >
                            {{ __('Eliminar Permanentemente') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
