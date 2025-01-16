<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteInventarioDetalle extends Model
{
    use HasFactory;

    protected $table = 'ajustes_inventario_detalle';

    protected $fillable = [
        'ajuste_inventario_id',
        'producto_id',
        'cantidad',
        'costo_unitario',
        'observacion'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario' => 'decimal:2'
    ];

    public function ajusteInventario()
    {
        return $this->belongsTo(AjusteInventario::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}

