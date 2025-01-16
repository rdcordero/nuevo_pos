<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('bodega_id')->constrained('bodegas');
            $table->integer('cantidad')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_maximo')->default(0);
            $table->timestamps();

            // Un producto solo puede tener un registro de inventario por bodega
            $table->unique(['producto_id', 'bodega_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventario');
    }
};