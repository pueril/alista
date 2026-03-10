<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithDateFilters;
use App\Models\Colaborador;
use App\Models\RegistroDiario;
use App\Models\SKU;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class DailyRecordsTable extends Component
{
    use WithPagination, WithDateFilters;

    public ?string $fechaInicio = null;
    public ?string $fechaFin = null;
    public ?string $colaboradorId = null;
    public ?string $skuId = null;
    public int $perPage = 15;

    // Modal state
    public bool $showModal = false;
    public ?string $editingId = null;
    public string $formFecha = '';
    public string $formColaboradorId = '';
    public string $formSkuId = '';
    public int $formProcesadas = 0;
    public string $formHoraIngreso = '08:00';
    public string $formHoraFinalizacion = '17:00';

    protected $queryString = [
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
        'colaboradorId' => ['except' => ''],
        'skuId' => ['except' => ''],
    ];

    public function updating($name, $value): void
    {
        if (in_array($name, ['fechaInicio', 'fechaFin'], true)) {
            $this->saveDateFiltersToSession();
        }
        if (in_array($name, ['fechaInicio', 'fechaFin', 'colaboradorId', 'skuId'], true)) {
            $this->resetPage();
        }
    }

    public function mount(): void
    {
        $this->initializeDateFilters();
        $this->formFecha = now()->format('Y-m-d');
        
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && $user->isColaborador() && $user->colaborador_id) {
            $this->formColaboradorId = (string) $user->colaborador_id;
        }
    }

    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->formFecha = now()->format('Y-m-d');
        
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && $user->isColaborador() && $user->colaborador_id) {
            $this->formColaboradorId = (string) $user->colaborador_id;
        } else {
            $this->formColaboradorId = '';
        }
        $this->formSkuId = '';
        $this->formProcesadas = 0;
        $this->formHoraIngreso = '08:00';
        $this->formHoraFinalizacion = '17:00';
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $registro = RegistroDiario::findOrFail($id);
        $this->editingId = $id;
        $this->formFecha = $registro->fecha->format('Y-m-d');
        $this->formColaboradorId = $registro->colaborador_id;
        $this->formSkuId = $registro->sku_id;
        $this->formProcesadas = $registro->procesadas;
        $this->formHoraIngreso = $registro->hora_ingreso;
        $this->formHoraFinalizacion = $registro->hora_finalizacion;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->formFecha = now()->format('Y-m-d');
        $this->formColaboradorId = '';
        $this->formSkuId = '';
        $this->formProcesadas = 0;
        $this->formHoraIngreso = '08:00';
        $this->formHoraFinalizacion = '17:00';
    }

    protected function calculateHours(string $horaInicio, string $horaFin): float
    {
        [$hInicio, $mInicio] = explode(':', $horaInicio);
        [$hFin, $mFin] = explode(':', $horaFin);
        
        $minutosInicio = (int)$hInicio * 60 + (int)$mInicio;
        $minutosFin = (int)$hFin * 60 + (int)$mFin;
        
        // Si la hora fin es menor que inicio, asumimos que es del día siguiente
        if ($minutosFin < $minutosInicio) {
            $minutosFin += 24 * 60; // Agregar 24 horas en minutos
        }
        
        $diferenciaMinutos = $minutosFin - $minutosInicio;
        
        return round($diferenciaMinutos / 60.0, 2);
    }

    protected function calculateProductivity(int $procesadas, float $metaEstablecida): float
    {
        if ($metaEstablecida <= 0) {
            return 0;
        }
        return ($procesadas / $metaEstablecida) * 100;
    }

    public function save(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            throw ValidationException::withMessages([
                'form' => ['No hay usuario autenticado.'],
            ]);
        }

        $isSupervisor = $this->isSupervisor;
        $isColaborador = $user->isColaborador();

        if (! $isSupervisor && ! $isColaborador) {
            throw ValidationException::withMessages([
                'form' => ['No tienes permisos para crear o editar registros.'],
            ]);
        }

        if ($isColaborador) {
            if (! $user->colaborador_id) {
                throw ValidationException::withMessages([
                    'form' => ['Tu usuario no tiene un colaborador asociado. Contacta a un supervisor.'],
                ]);
            }

            $this->formColaboradorId = (string) $user->colaborador_id;
        }

        $this->validate([
            'formFecha' => 'required|date',
            'formColaboradorId' => 'required|string',
            'formSkuId' => 'required|string',
            'formProcesadas' => 'required|integer|min:0',
            'formHoraIngreso' => 'required|string',
            'formHoraFinalizacion' => 'required|string',
        ], [
            'formFecha.required' => 'La fecha es obligatoria.',
            'formColaboradorId.required' => 'Debe seleccionar un colaborador.',
            'formSkuId.required' => 'Debe seleccionar un SKU.',
            'formProcesadas.required' => 'Debe ingresar la cantidad procesada.',
            'formProcesadas.min' => 'La cantidad procesada no puede ser negativa.',
        ]);

        $sku = SKU::findOrFail($this->formSkuId);
        $horasProceso = $this->calculateHours($this->formHoraIngreso, $this->formHoraFinalizacion);
        $metaEstablecida = $sku->prod_hora * $horasProceso; // Meta diaria ajustada por horas
        $productividad = $this->calculateProductivity($this->formProcesadas, $metaEstablecida);

        if ($this->editingId) {
            $registro = RegistroDiario::findOrFail($this->editingId);
            $registro->update([
                'fecha' => Carbon::parse($this->formFecha),
                'colaborador_id' => $this->formColaboradorId,
                'sku_id' => $this->formSkuId,
                'procesadas' => $this->formProcesadas,
                'meta_establecida' => $metaEstablecida,
                'productividad' => $productividad,
                'hora_ingreso' => $this->formHoraIngreso,
                'hora_finalizacion' => $this->formHoraFinalizacion,
                'horas_proceso' => $horasProceso,
            ]);
            session()->flash('message', 'Registro actualizado correctamente.');
        } else {
            RegistroDiario::create([
                'fecha' => Carbon::parse($this->formFecha),
                'colaborador_id' => $this->formColaboradorId,
                'sku_id' => $this->formSkuId,
                'procesadas' => $this->formProcesadas,
                'meta_establecida' => $metaEstablecida,
                'productividad' => $productividad,
                'hora_ingreso' => $this->formHoraIngreso,
                'hora_finalizacion' => $this->formHoraFinalizacion,
                'horas_proceso' => $horasProceso,
            ]);
            session()->flash('message', 'Registro creado correctamente.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        if (! $this->isSupervisor) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden eliminar registros.'],
            ]);
        }

        $registro = RegistroDiario::findOrFail($id);
        $registro->delete();
        session()->flash('message', 'Registro eliminado correctamente.');
        $this->resetPage();
    }

    public function getIsSupervisorProperty(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->isSupervisor();
    }

    public function render()
    {
        $query = RegistroDiario::with(['colaborador', 'sku'])->orderByDesc('fecha');

        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', Carbon::parse($this->fechaInicio));
        }

        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', Carbon::parse($this->fechaFin));
        }

        if ($this->colaboradorId) {
            $query->where('colaborador_id', $this->colaboradorId);
        }

        if ($this->skuId) {
            $query->where('sku_id', $this->skuId);
        }

        // Si es colaborador, limitar a sus propios registros
        if (! $this->isSupervisor && Auth::user()?->colaborador_id) {
            $query->where('colaborador_id', Auth::user()->colaborador_id);
        }

        $registros = $query->paginate($this->perPage);

        $colaboradores = Colaborador::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $skus = SKU::where('activo', true)
            ->orderBy('codigo')
            ->get();

        return view('livewire.daily-records-table', [
            'registros' => $registros,
            'colaboradores' => $colaboradores,
            'skus' => $skus,
        ]);
    }
}

