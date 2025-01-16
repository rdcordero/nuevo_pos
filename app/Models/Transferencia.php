<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transferencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_documento',
        'empresa_id',
        'bodega_origen_id',
        'bodega_destino_id',
        'fecha',
        'motivo',
        'observacion',
        'estado',
        'usuario_id'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function bodegaOrigen()
    {
        return $this->belongsTo(Bodega::class, 'bodega_origen_id');
    }

    public function bodegaDestino()
    {
        return $this->belongsTo(Bodega::class, 'bodega_destino_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(TransferenciaDetalle::class);
    }
}

