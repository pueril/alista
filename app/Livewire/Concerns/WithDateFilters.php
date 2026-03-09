<?php

namespace App\Livewire\Concerns;

trait WithDateFilters
{
    /**
     * Inicializa los filtros de fecha desde la sesión si no están en la URL.
     * La URL tiene prioridad sobre la sesión.
     */
    protected function initializeDateFilters(): void
    {
        // Solo usar sesión si no hay valores en la URL (queryString)
        // Si hay valores en la URL, se mantienen (ya están asignados por Livewire)
        // Si no hay valores en la URL, cargar desde sesión
        if ($this->fechaInicio === null || $this->fechaInicio === '') {
            $this->fechaInicio = session('filtro_fecha_inicio');
        }
        
        if ($this->fechaFin === null || $this->fechaFin === '') {
            $this->fechaFin = session('filtro_fecha_fin');
        }
    }

    /**
     * Guarda los filtros de fecha en la sesión.
     */
    protected function saveDateFiltersToSession(): void
    {
        session([
            'filtro_fecha_inicio' => $this->fechaInicio,
            'filtro_fecha_fin' => $this->fechaFin,
        ]);
    }

    /**
     * Limpia los filtros de fecha de la sesión.
     */
    protected function clearDateFiltersFromSession(): void
    {
        session()->forget(['filtro_fecha_inicio', 'filtro_fecha_fin']);
    }
}
