<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaImpuesto extends Model
{
    protected $table = 'venta_impuestos';

    protected $fillable = [
        'venta_id',
        'impuesto_id',
        'base_imponible',
        'porcentaje',
        'monto',
    ];

    protected $casts = [
        'base_imponible' => 'decimal:2',
        'porcentaje' => 'decimal:2',
        'monto' => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function impuesto(): BelongsTo
    {
        return $this->belongsTo(Impuesto::class);
    }
}

