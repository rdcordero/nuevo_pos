<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumentoVenta extends Model
{
    use HasFactory;

    protected $table = 'tipos_documento_venta';

    protected $fillable = [
        'codigo',
        'nombre',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'tipo_documento_id');
    }

    public function correlativos()
    {
        return $this->hasMany(CorrelativoDocumento::class, 'tipo_documento_id');
    }

    public function obtenerCorrelativoActivo($sucursalId)
    {
        return $this->correlativos()
            ->where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->where('fecha_vencimiento', '>=', now()->startOfDay())
            ->where('correlativo_actual', '<', DB::raw('correlativo_final'))
            ->first();
    }
}

