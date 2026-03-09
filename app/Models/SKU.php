<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SKU extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'skus';

    /**
     * Atributos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'codigo',
        'familia',
        'meta_diaria',
        'prod_hora',
        'activo',
    ];

    /**
     * Casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta_diaria' => 'decimal:2',
            'prod_hora' => 'decimal:5',
            'activo' => 'boolean',
        ];
    }

    public function registros(): HasMany
    {
        return $this->hasMany(RegistroDiario::class);
    }
}
