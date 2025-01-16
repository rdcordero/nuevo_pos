<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaDetalle extends Model
{
    protected $table = 'venta_detalles';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'codigo',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'descuento',
        'impuesto',
        'subtotal',
        'total',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:4',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Métodos
    public function calcularTotales()
    {
        $this->subtotal = $this->cantidad * $this->precio_unitario;
        $this->total = $this->subtotal - $this->descuento + $this->impuesto;
    }
}

