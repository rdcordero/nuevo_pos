<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodigoBarra extends Model
{
    protected $table = 'codigos_barra';

    protected $fillable = [
        'producto_id',
        'codigo',
        'principal'
    ];

    protected $casts = [
        'principal' => 'boolean',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}