<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            // Primero eliminamos las columnas que no deberían estar
            $table->dropColumn(['precio']);
            
            // Agregamos las nuevas columnas
            $table->decimal('precio_compra', 10, 2)->after('descripcion');
            $table->decimal('precio_venta', 10, 2)->after('precio_compra');
            $table->integer('stock_minimo')->after('precio_venta');
            $table->integer('stock_maximo')->after('stock_minimo');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            // Revertimos los cambios
            $table->decimal('precio', 10, 2)->after('descripcion');
            
            $table->dropColumn([
                'precio_compra',
                'precio_venta',
                'stock_minimo',
                'stock_maximo'
            ]);
        });
    }
};