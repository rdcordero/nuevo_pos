<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('unidad_medida')->default('unidad')->after('tipo');
            $table->integer('punto_reorden')->default(0)->after('stock_maximo');
            $table->string('ubicacion')->nullable()->after('punto_reorden');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['unidad_medida', 'punto_reorden', 'ubicacion']);
        });
    }
};
