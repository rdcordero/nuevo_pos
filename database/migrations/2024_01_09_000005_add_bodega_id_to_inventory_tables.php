<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Crear bodega predeterminada
        $empresaId = DB::table('empresas')->first()->id;
        $sucursalId = DB::table('sucursales')->where('empresa_id', $empresaId)->first()->id;
        
        $bodegaId = DB::table('bodegas')->insertGetId([
            'nombre' => 'Bodega Principal',
            'descripcion' => 'Bodega predeterminada del sistema',
            'sucursal_id' => $sucursalId,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Crear tablas temporales
        Schema::create('temp_inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('empresa_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('NO ACTION');
            $table->foreignId('bodega_id')->constrained()->onDelete('NO ACTION');
            $table->enum('tipo_movimiento', ['entrada', 'salida']);
            $table->enum('origen_movimiento', ['compra', 'venta', 'ajuste', 'nota_credito']);
            $table->unsignedBigInteger('documento_id')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->decimal('cantidad', 10, 2);
            $table->decimal('costo_unitario', 10, 2);
            $table->string('numero_documento')->nullable();
            $table->text('observacion')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('NO ACTION');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('temp_ajustes_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('NO ACTION');
            $table->foreignId('bodega_id')->constrained()->onDelete('NO ACTION');
            $table->string('numero_documento');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->date('fecha');
            $table->text('motivo');
            $table->text('observacion')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('NO ACTION');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Copiar datos existentes a las tablas temporales
        DB::transaction(function () use ($bodegaId) {
            // Copiar inventario_movimientos
            DB::unprepared('SET IDENTITY_INSERT temp_inventario_movimientos ON;');
            
            $movimientos = DB::table('inventario_movimientos')->get();
            foreach ($movimientos as $movimiento) {
                DB::table('temp_inventario_movimientos')->insert([
                    'id' => $movimiento->id,
                    'producto_id' => $movimiento->producto_id,
                    'empresa_id' => $movimiento->empresa_id,
                    'sucursal_id' => $movimiento->sucursal_id,
                    'bodega_id' => $bodegaId,
                    'tipo_movimiento' => $movimiento->tipo_movimiento,
                    'origen_movimiento' => $movimiento->origen_movimiento,
                    'documento_id' => $movimiento->documento_id,
                    'tipo_documento' => $movimiento->tipo_documento,
                    'cantidad' => $movimiento->cantidad,
                    'costo_unitario' => $movimiento->costo_unitario,
                    'numero_documento' => $movimiento->numero_documento,
                    'observacion' => $movimiento->observacion,
                    'usuario_id' => $movimiento->usuario_id,
                    'created_at' => $movimiento->created_at,
                    'updated_at' => $movimiento->updated_at,
                    'deleted_at' => $movimiento->deleted_at,
                ]);
            }
            
            DB::unprepared('SET IDENTITY_INSERT temp_inventario_movimientos OFF;');

            // Copiar ajustes_inventario
            DB::unprepared('SET IDENTITY_INSERT temp_ajustes_inventario ON;');
            
            $ajustes = DB::table('ajustes_inventario')->get();
            foreach ($ajustes as $ajuste) {
                DB::table('temp_ajustes_inventario')->insert([
                    'id' => $ajuste->id,
                    'empresa_id' => $ajuste->empresa_id,
                    'sucursal_id' => $ajuste->sucursal_id,
                    'bodega_id' => $bodegaId,
                    'numero_documento' => $ajuste->numero_documento,
                    'tipo' => $ajuste->tipo,
                    'fecha' => $ajuste->fecha,
                    'motivo' => $ajuste->motivo,
                    'observacion' => $ajuste->observacion,
                    'usuario_id' => $ajuste->usuario_id,
                    'created_at' => $ajuste->created_at,
                    'updated_at' => $ajuste->updated_at,
                    'deleted_at' => $ajuste->deleted_at,
                ]);
            }
            
            DB::unprepared('SET IDENTITY_INSERT temp_ajustes_inventario OFF;');
        });

        // 4. Eliminar las restricciones de clave foránea
        Schema::table('ajustes_inventario_detalle', function (Blueprint $table) {
            $table->dropForeign(['ajuste_inventario_id']);
        });

        // 5. Eliminar tablas originales
        Schema::dropIfExists('inventario_movimientos');
        Schema::dropIfExists('ajustes_inventario');

        // 6. Renombrar tablas temporales
        Schema::rename('temp_inventario_movimientos', 'inventario_movimientos');
        Schema::rename('temp_ajustes_inventario', 'ajustes_inventario');

        // 7. Recrear las restricciones de clave foránea
        Schema::table('ajustes_inventario_detalle', function (Blueprint $table) {
            $table->foreign('ajuste_inventario_id')
                  ->references('id')
                  ->on('ajustes_inventario')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        // No es posible revertir esta migración de manera segura
        // ya que implica pérdida de datos de la columna bodega_id
    }
};

