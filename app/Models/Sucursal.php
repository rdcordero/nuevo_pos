<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'direccion',
        'telefono',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usuario_sucursal')
                    ->withPivot('activo')
                    ->withTimestamps();
    }

    public function usuariosActivos(): BelongsToMany
    {
        return $this->usuarios()->wherePivot('activo', true);
    }

    public function bodegas(): HasMany
    {
        return $this->hasMany(Bodega::class);
    }

    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class);
    }
}