<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                {{ __('Colaboradores') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">Administra el equipo del área de alistamiento</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:colaboradores-table />
        </div>
    </div>
</x-app-layout>
