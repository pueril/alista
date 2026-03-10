<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithDateFilters;
use App\Models\Asistencia;
use App\Models\Colaborador;
use App\Models\RegistroDiario;
use App\Models\SKU;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductividadDashboard extends Component
{
    use WithDateFilters;

    public string $tipo = 'colaborador'; // 'colaborador' o 'sku'
    public ?string $fechaInicio = null;
    public ?string $fechaFin = null;

    protected $queryString = [
        'tipo' => ['except' => 'colaborador'],
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->initializeDateFilters();
    }

    public function updating($name, $value): void
    {
        if (in_array($name, ['fechaInicio', 'fechaFin'], true)) {
            $this->saveDateFiltersToSession();
        }
    }

    public function clearFilters(): void
    {
        $this->fechaInicio = null;
        $this->fechaFin = null;
        $this->clearDateFiltersFromSession();
    }

    public function getIsSupervisorProperty(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->isSupervisor();
    }

    public function render()
    {
        $where = [];
        
        if ($this->fechaInicio) {
            $where[] = ['fecha', '>=', Carbon::parse($this->fechaInicio)];
        }
        if ($this->fechaFin) {
            $where[] = ['fecha', '<=', Carbon::parse($this->fechaFin)];
        }

        // Restricción para colaboradores
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && !$user->isSupervisor() && $user->colaborador_id) {
            $where[] = ['colaborador_id', $user->colaborador_id];
        }

        if ($this->tipo === 'colaborador') {
            $registros = RegistroDiario::with(['colaborador', 'sku'])
                ->where($where)
                ->get();

            // Obtener asistencias del período filtrado
            $asistenciasQuery = Asistencia::query();

            if ($user && !$user->isSupervisor() && $user->colaborador_id) {
                $asistenciasQuery->where('colaborador_id', $user->colaborador_id);
            }

            if ($this->fechaInicio || $this->fechaFin) {
                $startDate = $this->fechaInicio ? Carbon::parse($this->fechaInicio) : null;
                $endDate = $this->fechaFin ? Carbon::parse($this->fechaFin) : null;

                if ($startDate && $endDate) {
                    $startYear = $startDate->year;
                    $endYear = $endDate->year;
                    $startWeek = $this->getWeekNumber($startDate);
                    $endWeek = $this->getWeekNumber($endDate);

                    if ($startYear === $endYear) {
                        $asistenciasQuery->where('anio', $startYear)
                            ->whereBetween('semana', [$startWeek, $endWeek]);
                    } else {
                        $asistenciasQuery->whereBetween('anio', [$startYear, $endYear]);
                    }
                } elseif ($startDate) {
                    $asistenciasQuery->where('anio', '>=', $startDate->year);
                } elseif ($endDate) {
                    $asistenciasQuery->where('anio', '<=', $endDate->year);
                }
            } else {
                // Sin filtro de fechas: usar año actual como antes
                $asistenciasQuery->where('anio', now()->year);
            }

            $asistencias = $asistenciasQuery->get();

            // Agrupar asistencias por colaborador
            $asistenciaPorColaborador = [];
            foreach ($asistencias as $a) {
                if (!isset($asistenciaPorColaborador[$a->colaborador_id])) {
                    $asistenciaPorColaborador[$a->colaborador_id] = ['ausencia' => 0, 'atraso' => 0, 'total' => 0];
                }
                $asistenciaPorColaborador[$a->colaborador_id]['ausencia'] += $a->ausencia;
                $asistenciaPorColaborador[$a->colaborador_id]['atraso'] += $a->atraso;
                $asistenciaPorColaborador[$a->colaborador_id]['total'] += $a->total;
            }

            // Agrupar registros por colaborador
            $grouped = [];
            foreach ($registros as $r) {
                $key = $r->colaborador_id;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'colaborador' => $r->colaborador,
                        'totalProcesadas' => 0,
                        'totalHoras' => 0,
                        'sumProductividad' => 0,
                        'count' => 0,
                        'skus' => [],
                    ];
                }
                $grouped[$key]['totalProcesadas'] += $r->procesadas;
                $grouped[$key]['totalHoras'] += $r->horas_proceso;
                $grouped[$key]['sumProductividad'] += $r->productividad;
                $grouped[$key]['count'] += 1;

                // Agrupar por SKU
                $skuKey = $r->sku_id;
                if (!isset($grouped[$key]['skus'][$skuKey])) {
                    $grouped[$key]['skus'][$skuKey] = [
                        'sku' => $r->sku,
                        'totalProcesadas' => 0,
                        'sumProductividad' => 0,
                        'count' => 0,
                    ];
                }
                $grouped[$key]['skus'][$skuKey]['totalProcesadas'] += $r->procesadas;
                $grouped[$key]['skus'][$skuKey]['sumProductividad'] += $r->productividad;
                $grouped[$key]['skus'][$skuKey]['count'] += 1;
            }

            $data = [];
            foreach ($grouped as $g) {
                $asistencia = $asistenciaPorColaborador[$g['colaborador']->id] ?? ['ausencia' => 0, 'atraso' => 0, 'total' => 0];
                $data[] = [
                    'colaborador' => $g['colaborador'],
                    'totalProcesadas' => $g['totalProcesadas'],
                    'totalHoras' => $g['totalHoras'],
                    'promedioProductividad' => $g['count'] > 0 ? $g['sumProductividad'] / $g['count'] : 0,
                    'asistencia' => $asistencia,
                    'skus' => array_map(function($s) {
                        return [
                            'sku' => $s['sku'],
                            'totalProcesadas' => $s['totalProcesadas'],
                            'promedioProductividad' => $s['count'] > 0 ? $s['sumProductividad'] / $s['count'] : 0,
                        ];
                    }, $g['skus']),
                ];
            }
        } else {
            // Vista por SKU
            $registros = RegistroDiario::with(['colaborador', 'sku'])
                ->where($where)
                ->get();

            $grouped = [];
            foreach ($registros as $r) {
                $key = $r->sku_id;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'sku' => $r->sku,
                        'totalProcesadas' => 0,
                        'sumProductividad' => 0,
                        'count' => 0,
                        'colaboradores' => [],
                    ];
                }
                $grouped[$key]['totalProcesadas'] += $r->procesadas;
                $grouped[$key]['sumProductividad'] += $r->productividad;
                $grouped[$key]['count'] += 1;

                // Agrupar por colaborador
                $colabKey = $r->colaborador_id;
                if (!isset($grouped[$key]['colaboradores'][$colabKey])) {
                    $grouped[$key]['colaboradores'][$colabKey] = [
                        'colaborador' => $r->colaborador,
                        'sumProductividad' => 0,
                        'count' => 0,
                    ];
                }
                $grouped[$key]['colaboradores'][$colabKey]['sumProductividad'] += $r->productividad;
                $grouped[$key]['colaboradores'][$colabKey]['count'] += 1;
            }

            $data = [];
            foreach ($grouped as $g) {
                $data[] = [
                    'sku' => $g['sku'],
                    'totalProcesadas' => $g['totalProcesadas'],
                    'promedioProductividad' => $g['count'] > 0 ? $g['sumProductividad'] / $g['count'] : 0,
                    'colaboradores' => array_map(function($c) {
                        return [
                            'colaborador' => $c['colaborador'],
                            'promedioProductividad' => $c['count'] > 0 ? $c['sumProductividad'] / $c['count'] : 0,
                        ];
                    }, $g['colaboradores']),
                ];
            }
        }

        return view('livewire.productividad-dashboard', [
            'data' => $data,
        ]);
    }

    protected function getWeekNumber(Carbon $date): int
    {
        // Encontrar el primer lunes del año
        $firstDayOfYear = Carbon::create($date->year, 1, 1);
        $dayOfWeek = $firstDayOfYear->dayOfWeek; // 0 = domingo, 1 = lunes, ..., 6 = sábado

        // Calcular días hasta el primer lunes
        $daysToMonday = $dayOfWeek === 0 ? 1 : ($dayOfWeek === 1 ? 0 : 8 - $dayOfWeek);
        $firstMonday = $firstDayOfYear->copy()->addDays($daysToMonday);

        // Si la fecha está antes del primer lunes, pertenece a la semana 1
        if ($date->lt($firstMonday)) {
            return 1;
        }

        // Calcular la diferencia en días desde el primer lunes
        $diff = $firstMonday->diffInDays($date, false);

        // Calcular el número de semana (sumar 1 porque la primera semana es la 1)
        $weekNumber = (int) floor($diff / 7) + 1;

        // Asegurar que esté en el rango válido (1-52)
        return max(1, min(52, $weekNumber));
    }
}
