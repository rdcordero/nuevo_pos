<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormaPago extends Model
{
    use SoftDeletes;

    protected $table = 'formas_pago';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo',
        'tipo',
        'requiere_referencia',
        'activo',
        'descripcion',
    ];

    protected $casts = [
        'requiere_referencia' => 'boolean',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ventaPagos(): HasMany
    {
        return $this->hasMany(VentaPago::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}

