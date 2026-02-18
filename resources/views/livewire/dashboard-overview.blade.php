<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-sm text-gray-500">Colaboradores</div>
            <div class="mt-2 text-2xl font-semibold">{{ $totalColaboradores }}</div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-sm text-gray-500">Registros diarios</div>
            <div class="mt-2 text-2xl font-semibold">{{ $totalRegistros }}</div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-sm text-gray-500">SKUs activos</div>
            <div class="mt-2 text-2xl font-semibold">{{ $totalSkus }}</div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-sm text-gray-500">% Productividad promedio</div>
            <div class="mt-2 text-2xl font-semibold">
                @if(! is_null($productividadPromedio))
                    {{ number_format($productividadPromedio, 1) }}%
                @else
                    —
                @endif
            </div>
        </div>
    </div>
</div>

