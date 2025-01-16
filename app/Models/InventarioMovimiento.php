<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventarioMovimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventario_movimientos';

    protected $fillable = [
        'producto_id',
        'empresa_id',
        'sucursal_id',
        'bodega_id',
        'tipo_movimiento',
        'origen_movimiento',
        'documento_id',
        'tipo_documento',
        'cantidad',
        'costo_unitario',
        'numero_documento',
        'observacion',
        'usuario_id'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario' => 'decimal:2'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
}
