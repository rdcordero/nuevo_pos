<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'subcategoria_id',
        'tipo',
        'unidad_medida',
        'precio_compra',
        'precio_venta',
        'stock_minimo',
        'stock_maximo',
        'stock',
        'punto_reorden',
        'ubicacion',
        'imagen',
        'activo',
        'empresa_id'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'punto_reorden' => 'integer'
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function codigosBarra(): HasMany
    {
        return $this->hasMany(CodigoBarra::class);
    }

    public function impuestos()
    {
        return $this->belongsToMany(Impuesto::class, 'producto_impuesto')
            ->withTimestamps();
    }

    public function inventario(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    public function componentes()
    {
        return $this->belongsToMany(Producto::class, 'producto_compuesto', 'producto_id', 'componente_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function productosQueLoUsan()
    {
        return $this->belongsToMany(Producto::class, 'producto_compuesto', 'componente_id', 'producto_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function esCompuesto()
    {
        return $this->componentes()->exists();
    }

    public function esComponente()
    {
        return $this->productosQueLoUsan()->exists();
    }
}