<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">Vista general de tu sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:dashboard-overview />
        </div>
    </div>
</x-app-layout>
