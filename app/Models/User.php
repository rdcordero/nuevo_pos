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
                    ->withPivot('activo')
                    ->withTimestamps();
    }

    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'usuario_sucursal', 'user_id', 'sucursal_id')
                    ->withPivot(['empresa_id', 'activo'])
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
        return $this->empresas()
                    ->where('empresas.id', $empresaId)
                    ->wherePivot('activo', true)
                    ->exists();
    }

    public function tieneAccesoASucursal($sucursalId, ?int $empresaId = null): bool
    {
        // Si no se proporciona empresa_id, usar la empresa activa
        $empresaId = $empresaId ?? $this->getEmpresaActivaId();

        try {
            return $this->sucursales()
                        ->where('sucursales.id', $sucursalId)
                        ->wherePivot('empresa_id', $empresaId)
                        ->wherePivot('activo', true)
                        ->exists();
        } catch (\Exception $e) {
            Log::error('Error verificando acceso a sucursal', [
                'user_id' => $this->id,
                'sucursal_id' => $sucursalId,
                'empresa_id' => $empresaId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
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

    // Métodos de asignación de sucursales
    public function asignarSucursal($sucursalId, $empresaId)
    {
        try {
            $this->sucursales()->attach($sucursalId, [
                'empresa_id' => $empresaId,
                'activo' => true
            ]);

            Log::debug('Sucursal asignada correctamente', [
                'user_id' => $this->id,
                'sucursal_id' => $sucursalId,
                'empresa_id' => $empresaId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al asignar sucursal', [
                'user_id' => $this->id,
                'sucursal_id' => $sucursalId,
                'empresa_id' => $empresaId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function desasignarSucursal($sucursalId, $empresaId)
    {
        try {
            $this->sucursales()
                ->wherePivot('sucursal_id', $sucursalId)
                ->wherePivot('empresa_id', $empresaId)
                ->updateExistingPivot($sucursalId, ['activo' => false]);

            Log::debug('Sucursal desasignada correctamente', [
                'user_id' => $this->id,
                'sucursal_id' => $sucursalId,
                'empresa_id' => $empresaId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al desasignar sucursal', [
                'user_id' => $this->id,
                'sucursal_id' => $sucursalId,
                'empresa_id' => $empresaId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sincronizarSucursales(array $sucursales, $empresaId)
    {
        try {
            DB::beginTransaction();

            // Desactivar todas las sucursales existentes para esta empresa
            $this->sucursales()
                ->wherePivot('empresa_id', $empresaId)
                ->updateExistingPivot($this->sucursales()->pluck('sucursales.id'), ['activo' => false]);

            // Preparar las nuevas asignaciones
            $asignaciones = collect($sucursales)->mapWithKeys(function($sucursalId) use ($empresaId) {
                return [$sucursalId => [
                    'empresa_id' => $empresaId,
                    'activo' => true
                ]];
            })->all();

            // Sincronizar las nuevas asignaciones
            $this->sucursales()->syncWithoutDetaching($asignaciones);

            DB::commit();

            Log::debug('Sucursales sincronizadas correctamente', [
                'user_id' => $this->id,
                'empresa_id' => $empresaId,
                'sucursales' => $sucursales
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al sincronizar sucursales', [
                'user_id' => $this->id,
                'empresa_id' => $empresaId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

