<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = [
        'codigo',
        'nombre',
        'codigo_mh'
    ];

    // Relaciones
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function actividadesEconomicas()
    {
        return $this->hasMany(ActividadEconomica::class);
    }
}

