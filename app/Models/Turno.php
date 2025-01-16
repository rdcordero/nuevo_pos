<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    use SoftDeletes;

    protected $table = 'turnos';

    protected $fillable = [
        'sucursal_id',
        'caja_id',
        'usuario_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_apertura',
        'monto_cierre',
        'monto_sistema',
        'diferencia',
        'estado',
        'observaciones_apertura',
        'observaciones_cierre'
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_apertura' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
        'monto_sistema' => 'decimal:2',
        'diferencia' => 'decimal:2'
    ];

    // Relaciones
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Agregar relación con ventas
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class)
                    ->whereBetween('fecha', [$this->fecha_apertura, $this->fecha_cierre ?? now()])
                    ->orderBy('fecha', 'asc');
    }

    // Scopes
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->whereHas('sucursal', function($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }

    public function scopeSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    // Métodos
    public function calcularDiferencia()
    {
        if ($this->monto_cierre !== null && $this->monto_sistema !== null) {
            $this->diferencia = $this->monto_cierre - $this->monto_sistema;
        }
    }

    public function estaAbierto(): bool
    {
        return $this->estado === 'abierto';
    }
}

