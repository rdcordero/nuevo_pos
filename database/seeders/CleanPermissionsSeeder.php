<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanPermissionsSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // El orden es importante para SQL Server
        // Primero eliminamos las tablas intermedias
        DB::table('model_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('role_has_permissions')->delete();

        // Luego eliminamos los roles y permisos
        DB::table('permissions')->delete();
        DB::table('roles')->delete();

        // Reiniciamos los identificadores
        DB::statement('DBCC CHECKIDENT (\'permissions\', RESEED, 0)');
        DB::statement('DBCC CHECKIDENT (\'roles\', RESEED, 0)');

        Schema::enableForeignKeyConstraints();
    }
}