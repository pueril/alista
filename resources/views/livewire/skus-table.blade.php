<div class="space-y-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Gestión de SKUs</h3>
            <p class="text-sm text-gray-500 mt-1">Administra el catálogo de productos y sus familias</p>
        </div>
        @if($this->isSupervisor)
            <x-primary-button wire:click="openCreateModal">
                {{ __('Nuevo SKU') }}
            </x-primary-button>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
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
                                    <div class="flex justify-center gap-2">
                                        <button
                                            wire:click="openEditModal({{ $sku->id }})"
                                            class="text-indigo-600 hover:text-indigo-900"
                                            title="Editar"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="toggleActive({{ $sku->id }})"
                                            class="{{ $sku->activo ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}"
                                            title="{{ $sku->activo ? 'Desactivar' : 'Activar' }}"
                                        >
                                            @if($sku->activo)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </button>
                                        <button
                                            wire:click="openDeleteModal({{ $sku->id }})"
                                            class="text-red-600 hover:text-red-900"
                                            title="Eliminar permanentemente"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $skus->links() }}
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ $editingId ? __('Editar SKU') : __('Nuevo SKU') }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="formCodigo" :value="__('Código SKU')" />
                                    <x-text-input
                                        id="formCodigo"
                                        type="text"
                                        wire:model="formCodigo"
                                        class="mt-1 block w-full"
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
                                        class="mt-1 block w-full"
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
                                            class="mt-1 block w-full"
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
                                            class="mt-1 block w-full"
                                            required
                                        />
                                        <x-input-error :messages="$errors->get('formProdHora')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-primary-button type="submit" class="w-full sm:w-auto sm:ml-3">
                                {{ $editingId ? __('Actualizar') : __('Crear') }}
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

    @if($showDeleteModal && $deletingId)
        @php
            $sku = \App\Models\SKU::find($deletingId);
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDeleteModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
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

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="button"
                            wire:click="confirmDelete"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            {{ __('Eliminar Permanentemente') }}
                        </button>
                        <button
                            type="button"
                            wire:click="closeDeleteModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            {{ __('Cancelar') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
