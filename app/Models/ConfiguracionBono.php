<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionBono extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'configuracion_bonos';

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lider_productividad',
        'lider_asistencia',
        'ayudante_productividad',
        'ayudante_asistencia',
        'factor_verde',
        'factor_amarillo',
        'factor_rojo',
        'meta_productividad',
        'limite_amarillo_prod',
        'meta_asistencia',
        'limite_amarillo_asist',
        'descuento_atraso',
        'descuento_ausencia',
        'horas_por_dia',
    ];

    /**
     * Casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lider_productividad' => 'decimal:2',
            'lider_asistencia' => 'decimal:2',
            'ayudante_productividad' => 'decimal:2',
            'ayudante_asistencia' => 'decimal:2',
            'factor_verde' => 'decimal:2',
            'factor_amarillo' => 'decimal:2',
            'factor_rojo' => 'decimal:2',
            'meta_productividad' => 'decimal:2',
            'limite_amarillo_prod' => 'decimal:2',
            'meta_asistencia' => 'decimal:2',
            'limite_amarillo_asist' => 'decimal:2',
            'descuento_atraso' => 'decimal:2',
            'descuento_ausencia' => 'decimal:2',
            'horas_por_dia' => 'decimal:2',
        ];
    }
}
