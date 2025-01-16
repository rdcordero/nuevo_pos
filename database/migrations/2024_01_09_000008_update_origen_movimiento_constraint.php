<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop existing check constraint
        DB::statement('ALTER TABLE inventario_movimientos DROP CONSTRAINT IF EXISTS CK__inventario_movimientos__origen_movimiento');
        
        // Add new check constraint with updated values
        DB::statement("ALTER TABLE inventario_movimientos ADD CONSTRAINT CK__inventario_movimientos__origen_movimiento CHECK (origen_movimiento IN ('compra', 'venta', 'ajuste', 'nota_credito', 'transferencia'))");
    }

    public function down()
    {
        // Drop the new constraint
        DB::statement('ALTER TABLE inventario_movimientos DROP CONSTRAINT IF EXISTS CK__inventario_movimientos__origen_movimiento');
        
        // Restore original constraint
        DB::statement("ALTER TABLE inventario_movimientos ADD CONSTRAINT CK__inventario_movimientos__origen_movimiento CHECK (origen_movimiento IN ('compra', 'venta', 'ajuste', 'nota_credito'))");
    }
};

