<div class="space-y-4 sm:space-y-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Colaboradores</h3>
        @if($this->isSupervisor)
            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2.5 rounded-lg bg-sky-200 px-6 py-3.5 text-lg font-semibold text-sky-900 shadow-md border border-sky-300 hover:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 active:bg-sky-400"
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/30 text-sky-900 text-xl font-bold leading-none" aria-hidden="true">+</span>
                {{ __('Nuevo Colaborador') }}
            </button>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Iniciales') }}
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Nombre Completo') }}
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Perfil') }}
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
                    @forelse($colaboradores as $colaborador)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $colaborador->iniciales }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                {{ $colaborador->nombre }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                @if($colaborador->perfil === 'LIDER')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Líder
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Ayudante
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                @if($colaborador->activo)
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                        Vigente
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
                                            wire:click="openEditModal('{{ $colaborador->id }}')"
                                            class="inline-flex items-center justify-center min-w-[2.75rem] min-h-[2.75rem] p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                            title="Editar"
                                        >
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="toggleActive('{{ $colaborador->id }}')"
                                            class="inline-flex items-center justify-center min-w-[2.75rem] min-h-[2.75rem] p-2 rounded-lg {{ $colaborador->activo ? 'text-amber-600 hover:bg-amber-50 hover:text-amber-900' : 'text-green-600 hover:bg-green-50 hover:text-green-900' }} focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $colaborador->activo ? 'focus:ring-amber-500' : 'focus:ring-green-500' }}"
                                            title="{{ $colaborador->activo ? 'Desactivar' : 'Activar' }}"
                                        >
                                            @if($colaborador->activo)
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
                                            wire:click="openDeleteModal('{{ $colaborador->id }}')"
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
                            <td colspan="{{ $this->isSupervisor ? '5' : '4' }}" class="px-4 py-6 text-center text-sm text-gray-500">
                                {{ __('No hay colaboradores registrados.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
            {{ $colaboradores->links() }}
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
                                {{ $editingId ? __('Editar Colaborador') : __('Nuevo Colaborador') }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="formIniciales" :value="__('Iniciales')" />
                                    <x-text-input
                                        id="formIniciales"
                                        type="text"
                                        wire:model="formIniciales"
                                        class="mt-1 block w-full rounded-lg border-slate-300"
                                        maxlength="4"
                                        required
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Máximo 4 caracteres</p>
                                    <x-input-error :messages="$errors->get('formIniciales')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="formNombre" :value="__('Nombre Completo')" />
                                    <x-text-input
                                        id="formNombre"
                                        type="text"
                                        wire:model="formNombre"
                                        class="mt-1 block w-full rounded-lg border-slate-300"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('formNombre')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="formPerfil" :value="__('Perfil')" />
                                    <select
                                        id="formPerfil"
                                        wire:model="formPerfil"
                                        class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 py-2"
                                        required
                                    >
                                        <option value="AYUDANTE">{{ __('Ayudante') }}</option>
                                        <option value="LIDER">{{ __('Líder') }}</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">El perfil afecta el cálculo del bono</p>
                                    <x-input-error :messages="$errors->get('formPerfil')" class="mt-2" />
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
            $colaborador = \App\Models\Colaborador::find($deletingId);
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
                                ¿Está seguro de eliminar permanentemente al colaborador <strong>{{ $colaborador?->nombre }}</strong>?
                            </p>
                            <p class="text-red-600 text-sm mt-2">
                                Esta acción no se puede deshacer. Se eliminarán también todos los registros de productividad y asistencia asociados.
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
