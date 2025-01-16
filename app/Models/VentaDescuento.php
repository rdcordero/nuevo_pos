<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaDescuento extends Model
{
    protected $table = 'venta_descuentos';

    protected $fillable = [
        'venta_id',
        'tipo',
        'descripcion',
        'porcentaje',
        'monto',
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'monto' => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}

