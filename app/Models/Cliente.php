<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'pais_id',
        'codigo',
        'nombre',
        'nombre_comercial',
        'dui',
        'nit',
        'nrc',
        'tipo_cliente',
        'giro',
        'actividad_economica_codigo',
        'direccion',
        'departamento',
        'municipio',
        'distrito',
        'complemento',
        'telefono',
        'celular',
        'email',
        'web',
        'categoria',
        'limite_credito',
        'dias_credito',
        'vendedor',
        'observaciones',
        'exento',
        'gran_contribuyente',
        'activo'
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'dias_credito' => 'integer',
        'exento' => 'boolean',
        'gran_contribuyente' => 'boolean',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    public function actividadEconomica()
    {
        return $this->belongsTo(ActividadEconomica::class, 'actividad_economica_codigo', 'codigo')
                    ->where('actividades_economicas.pais_id', $this->pais_id);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeContribuyentes($query)
    {
        return $query->where('tipo_cliente', 'contribuyente');
    }

    public function scopeNoContribuyentes($query)
    {
        return $query->where('tipo_cliente', 'no_contribuyente');
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Accessors & Mutators
    public function getDireccionCompletaAttribute()
    {
        $partes = [];
        
        if ($this->direccion) {
            $partes[] = $this->direccion;
        }
        
        if ($this->distrito) {
            $partes[] = $this->distrito;
        }
        
        if ($this->municipio) {
            $partes[] = $this->municipio;
        }
        
        if ($this->departamento) {
            $partes[] = $this->departamento;
        }
        
        if ($this->pais && $this->pais->codigo !== 'SLV') {
            $partes[] = $this->pais->nombre;
        }
        
        if ($this->complemento) {
            $partes[] = $this->complemento;
        }
        
        return implode(', ', $partes);
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre_comercial ? "{$this->nombre} ({$this->nombre_comercial})" : $this->nombre;
    }

    public function getIdentificacionPrincipalAttribute()
    {
        if ($this->tipo_cliente === 'contribuyente') {
            return $this->nrc;
        }
        return $this->dui ?: $this->nit;
    }

    // Métodos de validación
    public function tieneDocumentosValidos()
    {
        if ($this->tipo_cliente === 'contribuyente') {
            return !empty($this->nrc) && !empty($this->nit);
        }
        return !empty($this->dui) || !empty($this->nit);
    }

    public function puedeEmitirCcf()
    {
        return $this->tipo_cliente === 'contribuyente' && 
               !empty($this->nrc) && 
               !empty($this->nit) && 
               !empty($this->giro);
    }

    public function getLimiteCreditoDisponibleAttribute()
    {
        $creditoUtilizado = $this->ventas()
            ->where('estado', 'completada')
            ->sum('saldo_pendiente');
            
        return $this->limite_credito - $creditoUtilizado;
    }

    // Boot method para formateo automático de documentos
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cliente) {
            // Formatear NIT (####-######-###-#)
            if ($cliente->nit) {
                $cliente->nit = preg_replace('/[^0-9]/', '', $cliente->nit);
                if (strlen($cliente->nit) === 14) {
                    $cliente->nit = substr($cliente->nit, 0, 4) . '-' . 
                                  substr($cliente->nit, 4, 6) . '-' . 
                                  substr($cliente->nit, 10, 3) . '-' . 
                                  substr($cliente->nit, 13, 1);
                }
            }

            // Formatear DUI (########-#)
            if ($cliente->dui) {
                $cliente->dui = preg_replace('/[^0-9]/', '', $cliente->dui);
                if (strlen($cliente->dui) === 9) {
                    $cliente->dui = substr($cliente->dui, 0, 8) . '-' . 
                                  substr($cliente->dui, 8, 1);
                }
            }

            // Formatear NRC (######-#)
            if ($cliente->nrc) {
                $cliente->nrc = preg_replace('/[^0-9]/', '', $cliente->nrc);
                if (strlen($cliente->nrc) === 7) {
                    $cliente->nrc = substr($cliente->nrc, 0, 6) . '-' . 
                                  substr($cliente->nrc, 6, 1);
                }
            }

            // Asegurar que el email esté en minúsculas
            if ($cliente->email) {
                $cliente->email = strtolower($cliente->email);
            }
        });

        // Asignar país por defecto si no se especifica
        static::creating(function ($cliente) {
            if (!$cliente->pais_id) {
                $paisPorDefecto = Pais::where('codigo', 'SLV')->first();
                if ($paisPorDefecto) {
                    $cliente->pais_id = $paisPorDefecto->id;
                }
            }
        });
    }
}

