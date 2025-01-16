<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AjusteInventario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ajustes_inventario';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'bodega_id',
        'numero_documento',
        'tipo',
        'fecha',
        'motivo',
        'observacion',
        'usuario_id'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function detalles()
    {
        return $this->hasMany(AjusteInventarioDetalle::class);
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
