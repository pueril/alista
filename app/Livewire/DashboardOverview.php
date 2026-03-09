<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithDateFilters;
use App\Models\Asistencia;
use App\Models\Colaborador;
use App\Models\RegistroDiario;
use App\Models\SKU;
use Livewire\Component;

class DashboardOverview extends Component
{
    use WithDateFilters;

    public ?string $fechaInicio = null;
    public ?string $fechaFin = null;

    public int $totalColaboradores = 0;
    public int $totalRegistros = 0;
    public int $totalSkus = 0;
    public ?float $productividadPromedio = null;

    protected $queryString = [
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->initializeDateFilters();
        $this->loadData();
    }

    public function updatedFechaInicio(): void
    {
        $this->saveDateFiltersToSession();
        $this->loadData();
    }

    public function updatedFechaFin(): void
    {
        $this->saveDateFiltersToSession();
        $this->loadData();
    }

    public function clearFilters(): void
    {
        $this->fechaInicio = null;
        $this->fechaFin = null;
        $this->clearDateFiltersFromSession();
        $this->loadData();
    }

    protected function loadData(): void
    {
        // Total colaboradores y SKUs no se filtran por fecha (son totales del sistema)
        $this->totalColaboradores = Colaborador::where('activo', true)->count();
        $this->totalSkus = SKU::where('activo', true)->count();

        // Registros diarios y productividad sí se filtran por fecha
        $queryRegistros = RegistroDiario::query();

        if ($this->fechaInicio) {
            $queryRegistros->where('fecha', '>=', $this->fechaInicio);
        }

        if ($this->fechaFin) {
            $queryRegistros->where('fecha', '<=', $this->fechaFin);
        }

        $this->totalRegistros = $queryRegistros->count();
        $this->productividadPromedio = $queryRegistros->avg('productividad');
    }

    public function render()
    {
        return view('livewire.dashboard-overview');
    }
}

