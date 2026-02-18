<?php

namespace App\Livewire;

use App\Models\Asistencia;
use App\Models\Colaborador;
use App\Models\RegistroDiario;
use App\Models\SKU;
use Livewire\Component;

class DashboardOverview extends Component
{
    public int $totalColaboradores = 0;
    public int $totalRegistros = 0;
    public int $totalSkus = 0;
    public ?float $productividadPromedio = null;

    public function mount(): void
    {
        $this->totalColaboradores = Colaborador::count();
        $this->totalRegistros = RegistroDiario::count();
        $this->totalSkus = SKU::count();
        $this->productividadPromedio = RegistroDiario::avg('productividad');
    }

    public function render()
    {
        return view('livewire.dashboard-overview');
    }
}

