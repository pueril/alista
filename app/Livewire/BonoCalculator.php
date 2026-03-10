<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithDateFilters;
use App\Models\Asistencia;
use App\Models\Colaborador;
use App\Models\ConfiguracionBono;
use App\Models\RegistroDiario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class BonoCalculator extends Component
{
    use WithDateFilters;

    public ?string $fechaInicio = null;
    public ?string $fechaFin = null;
    public bool $showConfigModal = false;
    
    public ?ConfiguracionBono $config = null;
    public array $resultados = [];
    
    // Form state para configuración
    public float $formLiderProductividad = 70000;
    public float $formLiderAsistencia = 30000;
    public float $formAyudanteProductividad = 45000;
    public float $formAyudanteAsistencia = 19500;
    public float $formFactorVerde = 1.2;
    public float $formFactorAmarillo = 0.8;
    public float $formFactorRojo = 0.5;
    public float $formMetaProductividad = 100;
    public float $formLimiteAmarilloProd = 80;
    public float $formMetaAsistencia = 0;
    public float $formLimiteAmarilloAsist = -5;
    public float $formDescuentoAtraso = -2;
    public float $formDescuentoAusencia = -5;
    public float $formHorasPorDia = 9.5;

    protected $queryString = [
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->initializeDateFilters();
        $this->loadConfig();
        $this->calculateBonos();
    }

    public function updated($name, $value): void
    {
        if (in_array($name, ['fechaInicio', 'fechaFin'], true)) {
            $this->saveDateFiltersToSession();
            $this->calculateBonos();
        }
    }

    public function clearFilters(): void
    {
        $this->fechaInicio = null;
        $this->fechaFin = null;
        $this->clearDateFiltersFromSession();
        $this->calculateBonos();
    }

    public function getIsSupervisorProperty(): bool
    {
        $user = Auth::user();
        return $user && $user->isSupervisor();
    }

    protected function loadConfig(): void
    {
        $this->config = ConfiguracionBono::first();
        
        if (! $this->config) {
            // Crear configuración por defecto
            $this->config = ConfiguracionBono::create([
                'lider_productividad' => 70000,
                'lider_asistencia' => 30000,
                'ayudante_productividad' => 45000,
                'ayudante_asistencia' => 19500,
                'factor_verde' => 1.2,
                'factor_amarillo' => 0.8,
                'factor_rojo' => 0.5,
                'meta_productividad' => 100,
                'limite_amarillo_prod' => 80,
                'meta_asistencia' => 0,
                'limite_amarillo_asist' => -5,
                'descuento_atraso' => -2,
                'descuento_ausencia' => -5,
                'horas_por_dia' => 9.5,
            ]);
        }

        // Cargar valores al formulario
        $this->formLiderProductividad = $this->config->lider_productividad;
        $this->formLiderAsistencia = $this->config->lider_asistencia;
        $this->formAyudanteProductividad = $this->config->ayudante_productividad;
        $this->formAyudanteAsistencia = $this->config->ayudante_asistencia;
        $this->formFactorVerde = $this->config->factor_verde;
        $this->formFactorAmarillo = $this->config->factor_amarillo;
        $this->formFactorRojo = $this->config->factor_rojo;
        $this->formMetaProductividad = $this->config->meta_productividad;
        $this->formLimiteAmarilloProd = $this->config->limite_amarillo_prod;
        $this->formMetaAsistencia = $this->config->meta_asistencia;
        $this->formLimiteAmarilloAsist = $this->config->limite_amarillo_asist;
        $this->formDescuentoAtraso = (float) ($this->config->descuento_atraso ?? -2);
        $this->formDescuentoAusencia = (float) ($this->config->descuento_ausencia ?? -5);
        $this->formHorasPorDia = (float) ($this->config->horas_por_dia ?? 9.5);
    }

    public function openConfigModal(): void
    {
        $this->showConfigModal = true;
    }

    public function closeConfigModal(): void
    {
        $this->showConfigModal = false;
        $this->loadConfig(); // Recargar valores originales
    }

    public function saveConfig(): void
    {
        if (! $this->isSupervisor) {
            throw ValidationException::withMessages([
                'form' => ['Solo los supervisores pueden modificar la configuración.'],
            ]);
        }

        $this->validate([
            'formLiderProductividad' => 'required|numeric|min:0',
            'formLiderAsistencia' => 'required|numeric|min:0',
            'formAyudanteProductividad' => 'required|numeric|min:0',
            'formAyudanteAsistencia' => 'required|numeric|min:0',
            'formFactorVerde' => 'required|numeric|min:0',
            'formFactorAmarillo' => 'required|numeric|min:0',
            'formFactorRojo' => 'required|numeric|min:0',
            'formMetaProductividad' => 'required|numeric',
            'formLimiteAmarilloProd' => 'required|numeric',
            'formMetaAsistencia' => 'required|numeric',
            'formLimiteAmarilloAsist' => 'required|numeric',
            'formDescuentoAtraso' => 'required|numeric',
            'formDescuentoAusencia' => 'required|numeric',
            'formHorasPorDia' => 'required|numeric|min:0.1',
        ]);

        $this->config->update([
            'lider_productividad' => $this->formLiderProductividad,
            'lider_asistencia' => $this->formLiderAsistencia,
            'ayudante_productividad' => $this->formAyudanteProductividad,
            'ayudante_asistencia' => $this->formAyudanteAsistencia,
            'factor_verde' => $this->formFactorVerde,
            'factor_amarillo' => $this->formFactorAmarillo,
            'factor_rojo' => $this->formFactorRojo,
            'meta_productividad' => $this->formMetaProductividad,
            'limite_amarillo_prod' => $this->formLimiteAmarilloProd,
            'meta_asistencia' => $this->formMetaAsistencia,
            'limite_amarillo_asist' => $this->formLimiteAmarilloAsist,
            'descuento_atraso' => $this->formDescuentoAtraso,
            'descuento_ausencia' => $this->formDescuentoAusencia,
            'horas_por_dia' => $this->formHorasPorDia,
        ]);

        $this->config->refresh();
        $this->closeConfigModal();
        $this->calculateBonos();
        session()->flash('message', 'Configuración guardada correctamente.');
    }

    protected function calculateBonos(): void
    {
        if (! $this->config) {
            $this->loadConfig();
        }

        $colaboradores = Colaborador::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $this->resultados = [];

        foreach ($colaboradores as $colab) {
            // Obtener registros de productividad
            $query = RegistroDiario::where('colaborador_id', $colab->id);
            
            if ($this->fechaInicio) {
                $query->whereDate('fecha', '>=', Carbon::parse($this->fechaInicio));
            }
            if ($this->fechaFin) {
                $query->whereDate('fecha', '<=', Carbon::parse($this->fechaFin));
            }

            $registros = $query->get();

            // Calcular productividad promedio
            $promedioProductividad = $registros->count() > 0
                ? $registros->avg('productividad')
                : 0;

            // Obtener asistencias del período
            $asistenciasQuery = Asistencia::where('colaborador_id', $colab->id);

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
            }

            $asistencias = $asistenciasQuery->get();
            $totalAsistencia = $asistencias->sum('total');

            // Determinar semáforo para PRODUCTIVIDAD
            $semaforo_prod = 'ROJO';
            if ($promedioProductividad >= $this->config->meta_productividad) {
                $semaforo_prod = 'VERDE';
            } elseif ($promedioProductividad >= $this->config->limite_amarillo_prod) {
                $semaforo_prod = 'AMARILLO';
            }
            $factor_prod = $this->getFactor($semaforo_prod);

            // Determinar semáforo para ASISTENCIA
            $semaforo_asist = 'ROJO';
            if ($totalAsistencia >= $this->config->meta_asistencia) {
                $semaforo_asist = 'VERDE';
            } elseif ($totalAsistencia >= $this->config->limite_amarillo_asist) {
                $semaforo_asist = 'AMARILLO';
            }
            $factor_asist = $this->getFactor($semaforo_asist);

            // Calcular bonos según perfil
            if ($colab->perfil === 'LIDER') {
                $bonoBaseProd = $this->config->lider_productividad;
                $bonoBaseAsist = $this->config->lider_asistencia;
            } else {
                $bonoBaseProd = $this->config->ayudante_productividad;
                $bonoBaseAsist = $this->config->ayudante_asistencia;
            }

            $bonoProductividad = round($bonoBaseProd * $factor_prod);
            $bonoAsistencia = round($bonoBaseAsist * $factor_asist);
            $bonoTotal = $bonoProductividad + $bonoAsistencia;

            $this->resultados[] = [
                'colaborador' => $colab,
                'productividadPromedio' => $promedioProductividad,
                'totalAsistencia' => $totalAsistencia,
                'metaProductividad' => $this->config->meta_productividad,
                'metaAsistencia' => $this->config->meta_asistencia,
                'semaforo_prod' => $semaforo_prod,
                'factor_prod' => $factor_prod,
                'semaforo_asist' => $semaforo_asist,
                'factor_asist' => $factor_asist,
                'bonoBaseProd' => $bonoBaseProd,
                'bonoBaseAsist' => $bonoBaseAsist,
                'bonoProductividad' => $bonoProductividad,
                'bonoAsistencia' => $bonoAsistencia,
                'bonoTotal' => $bonoTotal,
                'registrosCount' => $registros->count(),
            ];
        }

        // Ajuste especial para líderes:
        // Productividad del líder = promedio de la productividad de todos los colaboradores (ayudantes + líder)
        // Asistencia del líder = se mantiene su propia asistencia calculada arriba.
        if (count($this->resultados) > 0) {
            $productividades = array_column($this->resultados, 'productividadPromedio');

            $promedioGlobalProductividad = count($productividades) > 0
                ? array_sum($productividades) / count($productividades)
                : 0.0;

            foreach ($this->resultados as &$resultado) {
                if ($resultado['colaborador']->perfil === 'LIDER') {
                    $promProd = $promedioGlobalProductividad;

                    // Recalcular solo PRODUCTIVIDAD del líder con el promedio global
                    $semaforoProd = 'ROJO';
                    if ($promProd >= $this->config->meta_productividad) {
                        $semaforoProd = 'VERDE';
                    } elseif ($promProd >= $this->config->limite_amarillo_prod) {
                        $semaforoProd = 'AMARILLO';
                    }
                    $factorProd = $this->getFactor($semaforoProd);

                    $bonoBaseProdLider = $this->config->lider_productividad;
                    $bonoProd = round($bonoBaseProdLider * $factorProd);

                    // Asistencia del líder se mantiene tal como se calculó antes
                    $bonoAsist = $resultado['bonoAsistencia'];

                    $resultado['productividadPromedio'] = $promProd;
                    $resultado['semaforo_prod'] = $semaforoProd;
                    $resultado['factor_prod'] = $factorProd;
                    $resultado['bonoBaseProd'] = $bonoBaseProdLider;
                    $resultado['bonoProductividad'] = $bonoProd;
                    $resultado['bonoTotal'] = $bonoProd + $bonoAsist;
                }
            }
            unset($resultado);
        }
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

    protected function getFactor(string $semaforo): float
    {
        return match ($semaforo) {
            'VERDE' => $this->config->factor_verde,
            'AMARILLO' => $this->config->factor_amarillo,
            default => $this->config->factor_rojo,
        };
    }

    public function getLideresProperty(): array
    {
        return array_filter($this->resultados, fn($r) => $r['colaborador']->perfil === 'LIDER');
    }

    public function getAyudantesProperty(): array
    {
        return array_filter($this->resultados, fn($r) => $r['colaborador']->perfil === 'AYUDANTE');
    }

    public function getTotalBonosPagarProperty(): float
    {
        return array_sum(array_column($this->resultados, 'bonoTotal'));
    }

    public function render()
    {
        return view('livewire.bono-calculator');
    }
}
