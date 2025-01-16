<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;
    
    protected $table = 'ventas';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'cliente_id',
        'usuario_id',
        'tipo_documento',
        'serie',
        'numero',
        'fecha',
        'fecha_vencimiento',
        'moneda',
        'tasa_cambio',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'estado',
        'notas',
        'condiciones_pago',
        'referencia',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'tasa_cambio' => 'decimal:4',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(VentaPago::class);
    }

    public function impuestos(): HasMany
    {
        return $this->hasMany(VentaImpuesto::class);
    }

    public function descuentos(): HasMany
    {
        return $this->hasMany(VentaDescuento::class);
    }

    public function estados(): HasMany
    {
        return $this->hasMany(VentaEstado::class);
    }

    // Scopes
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    // Atributos
    public function getNumeroCompletoAttribute(): string
    {
        return "{$this->serie}-{$this->numero}";
    }

    public function getSaldoAttribute(): float
    {
        return $this->total - $this->pagos()->sum('monto');
    }

    public function getEstaPagadaAttribute(): bool
    {
        return $this->saldo <= 0;
    }

    public function getEstaVencidaAttribute(): bool
    {
        if ($this->fecha_vencimiento && $this->estado !== 'anulada') {
            return !$this->esta_pagada && $this->fecha_vencimiento->isPast();
        }
        return false;
    }
}

