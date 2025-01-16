<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa_default_id',
        'sucursal_default_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relaciones
    public function empresaDefault(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_default_id');
    }

    public function sucursalDefault(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_default_id');
    }

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'usuario_empresa', 'user_id', 'empresa_id')
                    ->withTimestamps();
    }

    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'usuario_sucursal', 'user_id', 'sucursal_id')
                    ->withTimestamps();
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'user_id')
                    ->whereHas('empresa', function($query) {
                        $query->where('empresas.id', session('empresa_activa', $this->empresa_default_id));
                    });
    }

    public function ventaEstados(): HasMany
    {
        return $this->hasMany(VentaEstado::class, 'user_id')
                    ->whereHas('venta.empresa', function($query) {
                        $query->where('empresas.id', session('empresa_activa', $this->empresa_default_id));
                    });
    }

    // Scopes
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('users.empresa_default_id', $empresaId);
    }

    public function scopeSucursal($query, $sucursalId)
    {
        return $query->where('users.sucursal_default_id', $sucursalId);
    }

    // Métodos auxiliares
    public function getEmpresaActivaId()
    {
        return session('empresa_activa', $this->empresa_default_id);
    }

    public function getSucursalActivaId()
    {
        return session('sucursal_activa', $this->sucursal_default_id);
    }

    // Métodos de verificación de acceso
    public function tieneAccesoAEmpresa($empresaId): bool
    {
        return $this->empresas()->where('empresas.id', $empresaId)->exists();
    }

    public function tieneAccesoASucursal($sucursalId): bool
    {
        return $this->sucursales()->where('sucursales.id', $sucursalId)->exists();
    }

    // Métodos de consulta
    public function empresasActivas()
    {
        return $this->empresas()
                    ->wherePivot('activo', true)
                    ->where('empresas.activo', true);
    }

     // Métodos de obtención de sucursales
     public function sucursalesDeEmpresa($empresaId)
     {
         try {
             $sucursales = DB::table('sucursales as s')
                 ->join('usuario_sucursal as us', 'us.sucursal_id', '=', 's.id')
                 ->where('us.user_id', $this->id)
                 ->where('us.activo', true)
                 ->where('s.empresa_id', $empresaId)
                 ->where('us.empresa_id', $empresaId)
                 ->where('s.activo', true)
                 ->select('s.*')
                 ->get();
 
             Log::debug('Obteniendo sucursales de empresa', [
                 'user_id' => $this->id,
                 'empresa_id' => $empresaId,
                 'count' => $sucursales->count()
             ]);
 
             return $sucursales;
         } catch (\Exception $e) {
             Log::error('Error al obtener sucursales de empresa', [
                 'user_id' => $this->id,
                 'empresa_id' => $empresaId,
                 'error' => $e->getMessage()
             ]);
             return collect();
         }
     }
}

