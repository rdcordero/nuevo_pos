<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('empresa_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('NO ACTION');
            $table->foreignId('bodega_id')->constrained()->onDelete('NO ACTION');
            $table->enum('tipo_movimiento', ['entrada', 'salida']);
            $table->enum('origen_movimiento', ['compra', 'venta', 'ajuste', 'nota_credito', 'transferencia']);
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
    }

    public function down()
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};

