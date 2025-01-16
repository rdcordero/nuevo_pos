<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Impuesto extends Model
{
    protected $table = 'impuestos';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'porcentaje',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'porcentaje' => 'decimal:2'
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class);
    }
}