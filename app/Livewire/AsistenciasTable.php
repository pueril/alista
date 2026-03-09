<?php

namespace App\Livewire;

use App\Models\Asistencia;
use App\Models\Colaborador;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AsistenciasTable extends Component
{
    public int $selectedAnio;
    public ?int $selectedSemana = null;
    public ?int $selectedMes = null;
    
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $formColaboradorId = '';
    public int $formSemana;
    public int $formAnio;
    public float $formAusencia = 0;
    public float $formAtraso = 0;

    protected $queryString = [
        'selectedAnio' => ['except' => ''],
        'selectedSemana' => ['except' => ''],
        'selectedMes' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->selectedAnio = now()->year;
        $this->formAnio = now()->year;
        $this->formSemana = $this->getCurrentWeek();
    }

    public function updating($name, $value): void
    {
        if ($name === 'selectedMes') {
            $this->selectedSemana = null;
        }
        if ($name === 'selectedSemana') {
            $this->selectedMes = null;
        }
    }

    public function getIsSupervisorProperty(): bool
    {
        $user = Auth::user();
        return $user && $user->isSupervisor();
    }

    protected function getCurrentWeek(): int
    {
        return (int) now()->weekOfYear;
    }

    /**
     * Devuelve los números de semana (ISO 1-53) que tienen al menos un día en el mes dado.
     */
    protected function getWeeksInMonth(int $month, int $year): array
    {
        $weeks = [];
        $date = Carbon::create($year, $month, 1);
        $lastDay = Carbon::create($year, $month)->endOfMonth();

        while ($date->lte($lastDay)) {
            $w = (int) $date->weekOfYear;
            if (! in_array($w, $weeks, true)) {
                $weeks[] = $w;
            }
            $date->addDay();
        }

        sort($weeks);

        return $weeks;
    }

    protected function getWeekNumber(Carbon $date): int
    {
        return (int) $date->weekOfYear;
    }

    /**
     * Rango de fechas (lun-dom) para una semana ISO del año.
     */
    public function getWeekDateRange(int $weekNumber, int $year): string
    {
        $date = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endDate = $date->copy()->addDays(6);

        return $date->format('d/m') . ' - ' . $endDate->format('d/m');
    }

    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->formColaboradorId = '';
        $this->formSemana = $this->getCurrentWeek();
        $this->formAnio = now()->year;
        $this->formAusencia = 0;
        $this->formAtraso = 0;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $asistencia = Asistencia::findOrFail($id);
        $this->editingId = $id;
        $this->formColaboradorId = $asistencia->colaborador_id;
        $this->formSemana = $asistencia->semana;
        $this->formAnio = $asistencia->anio;
        $this->formAusencia = $asistencia->ausencia;
        $this->formAtraso = $asistencia->atraso;
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
        $this->formColaboradorId = '';
        $this->formSemana = $this->getCurrentWeek();
        $this->formAnio = now()->year;
        $this->formAusencia = 0;
        $this->formAtraso = 0;
    }

    public function save(): void
    {
        if (! $this->isSupervisor) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden crear o editar asistencias.'],
            ]);
        }

        $this->validate([
            'formColaboradorId' => 'required|string',
            'formSemana' => 'required|integer|min:1|max:52',
            'formAnio' => 'required|integer',
            'formAusencia' => 'required|numeric',
            'formAtraso' => 'required|numeric',
        ]);

        $total = $this->formAusencia + $this->formAtraso;

        if ($this->editingId) {
            $asistencia = Asistencia::findOrFail($this->editingId);
            $asistencia->update([
                'ausencia' => $this->formAusencia,
                'atraso' => $this->formAtraso,
                'total' => $total,
            ]);
            session()->flash('message', 'Registro de asistencia actualizado correctamente.');
        } else {
            Asistencia::updateOrCreate(
                [
                    'semana' => $this->formSemana,
                    'anio' => $this->formAnio,
                    'colaborador_id' => $this->formColaboradorId,
                ],
                [
                    'ausencia' => $this->formAusencia,
                    'atraso' => $this->formAtraso,
                    'total' => $total,
                ]
            );
            session()->flash('message', 'Registro de asistencia creado correctamente.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        if (! $this->isSupervisor) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden eliminar asistencias.'],
            ]);
        }

        $asistencia = Asistencia::findOrFail($id);
        $asistencia->delete();
        session()->flash('message', 'Registro de asistencia eliminado correctamente.');
    }

    public function render()
    {
        // Construir query de asistencias
        $query = Asistencia::with('colaborador')
            ->where('anio', $this->selectedAnio);

        if ($this->selectedSemana) {
            $query->where('semana', (int) $this->selectedSemana);
        } elseif ($this->selectedMes !== null && $this->selectedMes !== '') {
            $mes = (int) $this->selectedMes;
            $anio = (int) $this->selectedAnio;
            $semanasDelMes = $this->getWeeksInMonth($mes, $anio);
            if (! empty($semanasDelMes)) {
                $query->whereIn('semana', $semanasDelMes);
            }
        }

        // Restricción para colaboradores
        $user = Auth::user();
        if ($user && !$user->isSupervisor() && $user->colaborador_id) {
            $query->where('colaborador_id', $user->colaborador_id);
        }

        $asistencias = $query->orderBy('semana')->orderBy('colaborador_id')->get();

        // Obtener semanas únicas
        $semanasUnicas = $asistencias->pluck('semana')->unique()->sort()->values()->toArray();

        // Calcular acumulados por colaborador
        $acumulados = [];
        foreach ($asistencias->groupBy('colaborador_id') as $colabId => $asistenciasColab) {
            $colaborador = $asistenciasColab->first()->colaborador;
            $acumulados[$colabId] = [
                'colaborador' => $colaborador,
                'ausenciaTotal' => $asistenciasColab->sum('ausencia'),
                'atrasoTotal' => $asistenciasColab->sum('atraso'),
                'totalGeneral' => $asistenciasColab->sum('total'),
            ];
        }
        usort($acumulados, fn($a, $b) => $a['colaborador']->nombre <=> $b['colaborador']->nombre);

        // Obtener colaboradores activos (solo para supervisores)
        $colaboradores = [];
        if ($this->isSupervisor) {
            $colaboradores = Colaborador::where('activo', true)
                ->orderBy('nombre')
                ->get();
        }

        // Preparar datos de semanas con rangos de fechas
        $semanasConRango = [];
        foreach ($semanasUnicas as $semana) {
            $semanasConRango[$semana] = $this->getWeekDateRange($semana, $this->selectedAnio);
        }

        // Función helper para obtener asistencia de un colaborador en una semana
        $getAsistenciaSemana = function($colaboradorId, $semana) use ($asistencias) {
            return $asistencias->firstWhere(fn($a) => $a->colaborador_id == $colaboradorId && $a->semana == $semana);
        };

        return view('livewire.asistencias-table', [
            'asistencias' => $asistencias,
            'semanasUnicas' => $semanasUnicas,
            'semanasConRango' => $semanasConRango,
            'acumulados' => $acumulados,
            'colaboradores' => $colaboradores,
            'getAsistenciaSemana' => $getAsistenciaSemana,
        ]);
    }
}
