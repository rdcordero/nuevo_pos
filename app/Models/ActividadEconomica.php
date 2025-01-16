<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadEconomica extends Model
{
    use HasFactory;

    protected $table = 'actividades_economicas';

    /**
     * The primary key is not auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The primary key is a composite of pais_id and codigo
     */
    protected $primaryKey =  'codigo';

    protected $fillable = [
        'pais_id',
        'codigo',
        'descripcion'
    ];

    /**
     * Get the pais that owns the actividad economica.
     */
    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    /**
     * Get the clientes for the actividad economica.
     */
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'actividad_economica_codigo', 'codigo')
                    ->where('clientes.pais_id', $this->pais_id);
    }

    /**
     * Set the keys for a save update query.
     * Necessary for composite primary keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('pais_id', $this->getAttribute('pais_id'))
              ->where('codigo', $this->getAttribute('codigo'));
        
        return $query;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'codigo';
    }

    /**
     * Get the codigo and descripcion as a single string.
     */
    public function getCodigoDescripcionAttribute()
    {
        return "{$this->codigo} - {$this->descripcion}";
    }
}

