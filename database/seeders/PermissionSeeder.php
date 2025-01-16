<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos para todas las funcionalidades
        $permissions = [
            // Empresas
            'ver empresas',
            'crear empresas',
            'editar empresas',
            'eliminar empresas',

            // Sucursales
            'ver sucursales',
            'crear sucursales',
            'editar sucursales',
            'eliminar sucursales',

            // Usuarios
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',

            // Roles
            'ver roles',
            'crear roles',
            'editar roles',
            'eliminar roles',

            // Productos
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',

            // Categorías
            'ver categorias',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',

            // Subcategorías
            'ver subcategorias',
            'crear subcategorias',
            'editar subcategorias',
            'eliminar subcategorias',

            // Impuestos
            'ver impuestos',
            'crear impuestos',
            'editar impuestos',
            'eliminar impuestos',

            // Bodegas
            'ver bodegas',
            'crear bodegas',
            'editar bodegas',
            'eliminar bodegas',

            // Inventario
            'ver inventario',
            'crear inventario',
            'editar inventario',
            'eliminar inventario',
            'ajustar inventario',

            // Cajas
            'ver cajas',
            'crear cajas',
            'editar cajas',
            'eliminar cajas',
            'abrir caja',
            'cerrar caja',

            // Horarios
            'ver horarios',
            'crear horarios',
            'editar horarios',
            'eliminar horarios',

            // Ventas
            'ver ventas',
            'realizar venta',
            'anular venta',
            'ver reportes ventas',
            'aplicar descuento',
            'cambiar precios',

            // Reportes
            'ver reportes',
            'exportar reportes',
            'ver estadisticas',

            // Permisos para cajas
            'ver cajas',
            'crear cajas',
            'editar cajas',
            'eliminar cajas',

            // Permisos para turnos
            'ver turnos',
            'crear turnos',
            'editar turnos',
            'eliminar turnos',
            'cerrar turnos'


        ];

        // Crear los permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear/Actualizar roles con nombres en minúsculas
        $administradorRole = Role::firstOrCreate(['name' => 'administrador']);
        $gerenteRole = Role::firstOrCreate(['name' => 'gerente']);
        $cajeroRole = Role::firstOrCreate(['name' => 'cajero']);

        // Asignar todos los permisos al rol Administrador
        $administradorRole->givePermissionTo(Permission::all());

        // Asignar permisos al rol Gerente
        $gerentePermisos = [
            'ver empresas',
            'ver sucursales',
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'ver productos',
            'crear productos',
            'editar productos',
            'ver categorias',
            'crear categorias',
            'editar categorias',
            'ver subcategorias',
            'crear subcategorias',
            'editar subcategorias',
            'ver impuestos',
            'ver bodegas',
            'ver inventario',
            'crear inventario',
            'editar inventario',
            'ajustar inventario',
            'ver cajas',
            'crear cajas',
            'editar cajas',
            'abrir caja',
            'cerrar caja',
            'ver horarios',
            'crear horarios',
            'editar horarios',
            'ver ventas',
            'realizar venta',
            'anular venta',
            'ver reportes ventas',
            'aplicar descuento',
            'cambiar precios',
            'ver reportes',
            'exportar reportes',
            'ver estadisticas'
        ];
        $gerenteRole->syncPermissions($gerentePermisos);

        // Asignar permisos al rol Cajero
        $cajeroPermisos = [
            'ver productos',
            'ver categorias',
            'ver subcategorias',
            'ver inventario',
            'ver ventas',
            'realizar venta',
            'ver reportes ventas',
            'abrir caja',
            'cerrar caja'
        ];
        $cajeroRole->syncPermissions($cajeroPermisos);
    }
}
