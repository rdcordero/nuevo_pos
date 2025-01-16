<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaEstado extends Model
{
    protected $table = 'venta_estados';

    protected $fillable = [
        'venta_id',
        'usuario_id',
        'estado_anterior',
        'estado_nuevo',
        'observacion',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

