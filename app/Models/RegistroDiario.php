<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroDiario extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'registros_diarios';

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fecha',
        'colaborador_id',
        'sku_id',
        'procesadas',
        'meta_establecida',
        'productividad',
        'hora_ingreso',
        'hora_finalizacion',
        'horas_proceso',
    ];

    /**
     * Casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'meta_establecida' => 'decimal:2',
            'productividad' => 'decimal:2',
            'horas_proceso' => 'decimal:2',
        ];
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(SKU::class);
    }
}
