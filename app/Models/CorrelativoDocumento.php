<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CorrelativoDocumento extends Model
{
    use HasFactory;

    protected $table = 'correlativos_documento';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'tipo_documento_id',
        'serie',
        'correlativo_actual',
        'correlativo_inicial',
        'correlativo_final',
        'fecha_inicio',
        'fecha_vencimiento',
        'activo'
    ];

    protected $casts = [
        'correlativo_actual' => 'integer',
        'correlativo_inicial' => 'integer',
        'correlativo_final' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
        'activo' => 'boolean'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumentoVenta::class, 'tipo_documento_id');
    }

    public function generarNumeroDocumento()
    {
        if (!$this->activo) {
            throw new \Exception('El correlativo no está activo.');
        }

        if ($this->fecha_vencimiento < Carbon::now()->startOfDay()) {
            throw new \Exception('El correlativo está vencido.');
        }

        if ($this->correlativo_actual >= $this->correlativo_final) {
            throw new \Exception('Se ha alcanzado el límite del correlativo.');
        }

        $this->correlativo_actual++;
        $correlativo = str_pad($this->correlativo_actual, 8, '0', STR_PAD_LEFT);
        $numeroDocumento = $this->serie ? $this->serie . $correlativo : $correlativo;
        $this->save();
        
        return $numeroDocumento;
    }

    public function disponible()
    {
        return $this->activo && 
               $this->fecha_vencimiento >= Carbon::now()->startOfDay() && 
               $this->correlativo_actual < $this->correlativo_final;
    }
}

